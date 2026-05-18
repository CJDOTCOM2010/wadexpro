<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Custom PHP-based PostgreSQL database dumper.
 *
 * This service dumps the entire database (schema + data) using the existing
 * PDO connection, bypassing the need for the `pg_dump` CLI tool entirely.
 * This is critical for environments where:
 *   - pg_dump is not installed on the server
 *   - The database is accessed through a connection pooler (PgBouncer/Supabase)
 *   - IPv6/IPv4 routing prevents direct pg_dump connections
 */
class DatabaseDumper
{
    protected string $outputPath;
    protected array $excludeTables = [];
    protected string $schema = 'public';

    public function __construct(string $outputPath)
    {
        $this->outputPath = $outputPath;
    }

    /**
     * Set tables to exclude from the dump.
     */
    public function exclude(array $tables): self
    {
        $this->excludeTables = $tables;
        return $this;
    }

    /**
     * Execute the full database dump.
     *
     * @return string The path to the generated SQL dump file.
     */
    public function dump(): string
    {
        $handle = fopen($this->outputPath, 'w');

        if (!$handle) {
            throw new \RuntimeException("Cannot open file for writing: {$this->outputPath}");
        }

        try {
            $this->writeHeader($handle);
            $this->dumpExtensions($handle);
            $this->dumpSequences($handle);
            $this->dumpEnumTypes($handle);
            $this->dumpTables($handle);
            $this->dumpTableData($handle);
            $this->dumpIndexes($handle);
            $this->dumpForeignKeys($handle);
            $this->dumpSequenceValues($handle);
            $this->writeFooter($handle);
        } finally {
            fclose($handle);
        }

        Log::info('DatabaseDumper: Full dump completed', [
            'path' => $this->outputPath,
            'size' => filesize($this->outputPath),
        ]);

        return $this->outputPath;
    }

    /**
     * Write the SQL dump header.
     */
    protected function writeHeader($handle): void
    {
        $dbName = DB::connection()->getDatabaseName();
        $now = now()->toDateTimeString();
        $version = DB::selectOne("SELECT version()")->version ?? 'Unknown';

        fwrite($handle, "--\n");
        fwrite($handle, "-- WADEXPRO Full Database Dump\n");
        fwrite($handle, "-- Database: {$dbName}\n");
        fwrite($handle, "-- Server: {$version}\n");
        fwrite($handle, "-- Generated: {$now}\n");
        fwrite($handle, "-- Method: PHP PDO (connection-pooler compatible)\n");
        fwrite($handle, "--\n\n");
        fwrite($handle, "SET statement_timeout = 0;\n");
        fwrite($handle, "SET lock_timeout = 0;\n");
        fwrite($handle, "SET client_encoding = 'UTF8';\n");
        fwrite($handle, "SET standard_conforming_strings = on;\n");
        fwrite($handle, "SET check_function_bodies = false;\n");
        fwrite($handle, "SET client_min_messages = warning;\n\n");
    }

    /**
     * Write the SQL dump footer.
     */
    protected function writeFooter($handle): void
    {
        fwrite($handle, "\n-- Dump completed on " . now()->toDateTimeString() . "\n");
    }

    /**
     * Dump PostgreSQL extensions.
     */
    protected function dumpExtensions($handle): void
    {
        try {
            $extensions = DB::select("SELECT extname FROM pg_extension WHERE extname != 'plpgsql'");
            if (!empty($extensions)) {
                fwrite($handle, "--\n-- Extensions\n--\n\n");
                foreach ($extensions as $ext) {
                    fwrite($handle, "CREATE EXTENSION IF NOT EXISTS \"{$ext->extname}\";\n");
                }
                fwrite($handle, "\n");
            }
        } catch (\Exception $e) {
            fwrite($handle, "-- Skipped extensions (permission denied)\n\n");
        }
    }

    /**
     * Dump custom ENUM types.
     */
    protected function dumpEnumTypes($handle): void
    {
        try {
            $enums = DB::select("
                SELECT t.typname as name,
                       string_agg(e.enumlabel, ',' ORDER BY e.enumsortorder) as labels
                FROM pg_type t
                JOIN pg_enum e ON t.oid = e.enumtypid
                JOIN pg_catalog.pg_namespace n ON n.oid = t.typnamespace
                WHERE n.nspname = ?
                GROUP BY t.typname
            ", [$this->schema]);

            if (!empty($enums)) {
                fwrite($handle, "--\n-- Custom ENUM Types\n--\n\n");
                foreach ($enums as $enum) {
                    $labels = implode(', ', array_map(fn($l) => "'" . addslashes(trim($l)) . "'", explode(',', $enum->labels)));
                    fwrite($handle, "DO \$\$ BEGIN\n");
                    fwrite($handle, "    CREATE TYPE \"{$enum->name}\" AS ENUM ({$labels});\n");
                    fwrite($handle, "EXCEPTION WHEN duplicate_object THEN null;\n");
                    fwrite($handle, "END \$\$;\n\n");
                }
            }
        } catch (\Exception $e) {
            Log::warning('DatabaseDumper: Could not dump enum types', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Dump sequences (before tables, so serial columns can reference them).
     */
    protected function dumpSequences($handle): void
    {
        try {
            $sequences = DB::select("
                SELECT sequence_name
                FROM information_schema.sequences
                WHERE sequence_schema = ?
            ", [$this->schema]);

            if (!empty($sequences)) {
                fwrite($handle, "--\n-- Sequences\n--\n\n");
                foreach ($sequences as $seq) {
                    fwrite($handle, "CREATE SEQUENCE IF NOT EXISTS \"{$seq->sequence_name}\";\n");
                }
                fwrite($handle, "\n");
            }
        } catch (\Exception $e) {
            Log::warning('DatabaseDumper: Could not dump sequences', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get all user tables in the public schema.
     */
    protected function getTables(): array
    {
        $tables = DB::select("
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = ?
              AND table_type = 'BASE TABLE'
            ORDER BY table_name
        ", [$this->schema]);

        return collect($tables)
            ->pluck('table_name')
            ->reject(fn($t) => in_array($t, $this->excludeTables))
            ->values()
            ->toArray();
    }

    /**
     * Dump CREATE TABLE statements for all tables.
     */
    protected function dumpTables($handle): void
    {
        $tables = $this->getTables();
        fwrite($handle, "--\n-- Table Structures\n--\n\n");

        foreach ($tables as $table) {
            $this->dumpTableStructure($handle, $table);
        }
    }

    /**
     * Dump the CREATE TABLE statement for a single table.
     */
    protected function dumpTableStructure($handle, string $table): void
    {
        $columns = DB::select("
            SELECT column_name, data_type, udt_name, character_maximum_length,
                   column_default, is_nullable, numeric_precision, numeric_scale
            FROM information_schema.columns
            WHERE table_schema = ? AND table_name = ?
            ORDER BY ordinal_position
        ", [$this->schema, $table]);

        if (empty($columns)) return;

        fwrite($handle, "DROP TABLE IF EXISTS \"{$table}\" CASCADE;\n");
        fwrite($handle, "CREATE TABLE \"{$table}\" (\n");

        $colDefs = [];
        foreach ($columns as $col) {
            $colDef = "    \"{$col->column_name}\" " . $this->mapColumnType($col);

            if ($col->column_default !== null) {
                $colDef .= " DEFAULT {$col->column_default}";
            }

            if ($col->is_nullable === 'NO') {
                $colDef .= " NOT NULL";
            }

            $colDefs[] = $colDef;
        }

        // Add primary key constraint
        $pk = DB::select("
            SELECT kcu.column_name
            FROM information_schema.table_constraints tc
            JOIN information_schema.key_column_usage kcu
              ON tc.constraint_name = kcu.constraint_name
              AND tc.table_schema = kcu.table_schema
            WHERE tc.table_schema = ?
              AND tc.table_name = ?
              AND tc.constraint_type = 'PRIMARY KEY'
            ORDER BY kcu.ordinal_position
        ", [$this->schema, $table]);

        if (!empty($pk)) {
            $pkCols = implode('", "', array_map(fn($p) => $p->column_name, $pk));
            $colDefs[] = "    PRIMARY KEY (\"{$pkCols}\")";
        }

        // Add unique constraints
        $uniques = DB::select("
            SELECT tc.constraint_name,
                   string_agg(kcu.column_name, ',' ORDER BY kcu.ordinal_position) as columns
            FROM information_schema.table_constraints tc
            JOIN information_schema.key_column_usage kcu
              ON tc.constraint_name = kcu.constraint_name
              AND tc.table_schema = kcu.table_schema
            WHERE tc.table_schema = ?
              AND tc.table_name = ?
              AND tc.constraint_type = 'UNIQUE'
            GROUP BY tc.constraint_name
        ", [$this->schema, $table]);

        foreach ($uniques as $uq) {
            $uqCols = implode('", "', explode(',', $uq->columns));
            $colDefs[] = "    CONSTRAINT \"{$uq->constraint_name}\" UNIQUE (\"{$uqCols}\")";
        }

        fwrite($handle, implode(",\n", $colDefs));
        fwrite($handle, "\n);\n\n");
    }

    /**
     * Map PostgreSQL column info to a SQL type string.
     */
    protected function mapColumnType($col): string
    {
        $type = strtolower($col->data_type);

        return match ($type) {
            'character varying' => $col->character_maximum_length
                ? "varchar({$col->character_maximum_length})"
                : 'varchar',
            'character' => $col->character_maximum_length
                ? "char({$col->character_maximum_length})"
                : 'char',
            'numeric', 'decimal' => ($col->numeric_precision && $col->numeric_scale)
                ? "numeric({$col->numeric_precision},{$col->numeric_scale})"
                : 'numeric',
            'integer' => 'integer',
            'bigint' => 'bigint',
            'smallint' => 'smallint',
            'boolean' => 'boolean',
            'text' => 'text',
            'json' => 'json',
            'jsonb' => 'jsonb',
            'uuid' => 'uuid',
            'date' => 'date',
            'time without time zone', 'time with time zone' => $type,
            'timestamp without time zone' => 'timestamp',
            'timestamp with time zone' => 'timestamptz',
            'double precision' => 'double precision',
            'real' => 'real',
            'bytea' => 'bytea',
            'inet' => 'inet',
            'cidr' => 'cidr',
            'macaddr' => 'macaddr',
            'interval' => 'interval',
            'array', 'ARRAY' => $col->udt_name ? "\"{$col->udt_name}\"" : 'text[]',
            'user-defined' => "\"{$col->udt_name}\"",
            default => $col->udt_name ?: $type,
        };
    }

    /**
     * Dump all table data as INSERT statements.
     */
    protected function dumpTableData($handle): void
    {
        $tables = $this->getTables();

        foreach ($tables as $table) {
            $this->dumpSingleTableData($handle, $table);
        }
    }

    /**
     * Dump data for a single table using chunked selects.
     */
    protected function dumpSingleTableData($handle, string $table): void
    {
        $count = DB::table($table)->count();

        if ($count === 0) {
            fwrite($handle, "-- Table \"{$table}\": 0 rows (empty)\n\n");
            return;
        }

        fwrite($handle, "--\n-- Data for table: {$table} ({$count} rows)\n--\n\n");

        // Get column names for the INSERT statement
        $columns = DB::select("
            SELECT column_name
            FROM information_schema.columns
            WHERE table_schema = ? AND table_name = ?
            ORDER BY ordinal_position
        ", [$this->schema, $table]);

        $colNames = array_map(fn($c) => '"' . $c->column_name . '"', $columns);
        $colList = implode(', ', $colNames);

        // Use chunking to avoid memory issues on large tables
        $chunkSize = 500;
        $offset = 0;

        while ($offset < $count) {
            $rows = DB::table($table)->offset($offset)->limit($chunkSize)->get();

            foreach ($rows as $row) {
                $values = [];
                foreach ($columns as $col) {
                    $colName = $col->column_name;
                    $val = $row->$colName ?? null;

                    if ($val === null) {
                        $values[] = 'NULL';
                    } elseif (is_bool($val)) {
                        $values[] = $val ? 'TRUE' : 'FALSE';
                    } elseif (is_int($val) || is_float($val)) {
                        $values[] = $val;
                    } elseif (is_array($val) || is_object($val)) {
                        $values[] = "'" . addslashes(json_encode($val)) . "'";
                    } else {
                        $values[] = "'" . str_replace("'", "''", (string) $val) . "'";
                    }
                }

                $valList = implode(', ', $values);
                fwrite($handle, "INSERT INTO \"{$table}\" ({$colList}) VALUES ({$valList});\n");
            }

            $offset += $chunkSize;
        }

        fwrite($handle, "\n");
    }

    /**
     * Dump indexes (excluding primary key and unique constraints already defined).
     */
    protected function dumpIndexes($handle): void
    {
        try {
            $indexes = DB::select("
                SELECT indexname, indexdef
                FROM pg_indexes
                WHERE schemaname = ?
                  AND indexname NOT IN (
                      SELECT constraint_name
                      FROM information_schema.table_constraints
                      WHERE table_schema = ?
                        AND constraint_type IN ('PRIMARY KEY', 'UNIQUE')
                  )
                ORDER BY tablename, indexname
            ", [$this->schema, $this->schema]);

            if (!empty($indexes)) {
                fwrite($handle, "--\n-- Indexes\n--\n\n");
                foreach ($indexes as $idx) {
                    fwrite($handle, "{$idx->indexdef};\n");
                }
                fwrite($handle, "\n");
            }
        } catch (\Exception $e) {
            Log::warning('DatabaseDumper: Could not dump indexes', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Dump foreign key constraints.
     */
    protected function dumpForeignKeys($handle): void
    {
        try {
            $fks = DB::select("
                SELECT
                    tc.table_name,
                    tc.constraint_name,
                    kcu.column_name,
                    ccu.table_name AS foreign_table_name,
                    ccu.column_name AS foreign_column_name,
                    rc.update_rule,
                    rc.delete_rule
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                  ON tc.constraint_name = kcu.constraint_name
                  AND tc.table_schema = kcu.table_schema
                JOIN information_schema.constraint_column_usage ccu
                  ON ccu.constraint_name = tc.constraint_name
                  AND ccu.table_schema = tc.table_schema
                JOIN information_schema.referential_constraints rc
                  ON rc.constraint_name = tc.constraint_name
                  AND rc.constraint_schema = tc.table_schema
                WHERE tc.constraint_type = 'FOREIGN KEY'
                  AND tc.table_schema = ?
                ORDER BY tc.table_name, tc.constraint_name
            ", [$this->schema]);

            if (!empty($fks)) {
                fwrite($handle, "--\n-- Foreign Key Constraints\n--\n\n");
                foreach ($fks as $fk) {
                    $onUpdate = $fk->update_rule !== 'NO ACTION' ? " ON UPDATE {$fk->update_rule}" : '';
                    $onDelete = $fk->delete_rule !== 'NO ACTION' ? " ON DELETE {$fk->delete_rule}" : '';

                    fwrite($handle, "ALTER TABLE \"{$fk->table_name}\" ADD CONSTRAINT \"{$fk->constraint_name}\" ");
                    fwrite($handle, "FOREIGN KEY (\"{$fk->column_name}\") ");
                    fwrite($handle, "REFERENCES \"{$fk->foreign_table_name}\" (\"{$fk->foreign_column_name}\"){$onUpdate}{$onDelete};\n");
                }
                fwrite($handle, "\n");
            }
        } catch (\Exception $e) {
            Log::warning('DatabaseDumper: Could not dump foreign keys', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Dump current sequence values so auto-increment columns continue from the right number.
     */
    protected function dumpSequenceValues($handle): void
    {
        try {
            $sequences = DB::select("
                SELECT sequence_name
                FROM information_schema.sequences
                WHERE sequence_schema = ?
            ", [$this->schema]);

            if (!empty($sequences)) {
                fwrite($handle, "--\n-- Sequence Values\n--\n\n");
                foreach ($sequences as $seq) {
                    try {
                        $val = DB::selectOne("SELECT last_value FROM \"{$seq->sequence_name}\"");
                        if ($val) {
                            fwrite($handle, "SELECT setval('{$seq->sequence_name}', {$val->last_value}, true);\n");
                        }
                    } catch (\Exception $e) {
                        // Skip sequences we can't read
                    }
                }
                fwrite($handle, "\n");
            }
        } catch (\Exception $e) {
            Log::warning('DatabaseDumper: Could not dump sequence values', ['error' => $e->getMessage()]);
        }
    }
}
