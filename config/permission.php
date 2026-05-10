<?php

return [

    'models' => [

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your permissions. Of course, it
         * is often just the "Permission" model but you may use whatever you like.
         *
         * The model you want to use as a Permission model must implement the
         * `Spatie\Permission\Contracts\Permission` interface.
         */

        'permission' => Spatie\Permission\Models\Permission::class,

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your roles. Of course, it
         * is often just the "Role" model but you may use whatever you like.
         *
         * The model you want to use as a Role model must implement the
         * `Spatie\Permission\Contracts\Role` interface.
         */

        'role' => Spatie\Permission\Models\Role::class,

    ],

    'table_names' => [

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'roles' => 'roles',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your permissions. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'permissions' => 'permissions',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your models permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_permissions' => 'model_has_permissions',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your models roles. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_roles' => 'model_has_roles',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        /*
         * Change this if you want to name the related model primary key other than
         * `model_id`.
         *
         * For convenience, this should match a subset of the column name used
         * in your native Laravel [user] table's "remember_token" column.
         */

        'role_pivot_key' => null, // default 'role_id'
        'permission_pivot_key' => null, // default 'permission_id'

        /*
         * Change this if you want to name the related model primary key other than
         * `model_id`.
         *
         * For convenience, this should match a subset of the column name used
         * in your native Laravel [user] table's "remember_token" column.
         */

        'model_morph_key' => 'model_id',

        /*
         * Change this if you want to name the related team foreign key other than
         * `team_id`.
         */

        'team_foreign_key' => 'team_id',
    ],

    /*
     * When set to true, the "ModelHasRoles" and "ModelHasPermissions" policies will
     * be applied to your app.
     */

    'register_permission_check_method' => true,

    /*
     * When set to true, the "Role" and "Permission" models will register a
     * "refreshed" event that will clear relevant caches when those models are
     * updated.
     */

    'register_octane_reset_events' => false,

    /*
     * When set to true, the "HasRoles" trait will define a "permissions"
     * relationship.
     */

    'teams' => false,

    /*
     * When set to true, the Spatie\Permission\PermissionServiceProvider will
     * automatically register the "Permission" and "Role" models.
     */

    'use_passport_client_credentials' => false,

    /*
     * When set to true, the "HasRoles" trait will check the permission with
     * the "wildcard" pattern.
     */

    'display_permission_in_exception' => false,

    /*
     * When set to true, the Spatie\Permission\PermissionServiceProvider will
     * notify you if it successfully registered the "Permission" and "Role"
     * models.
     */

    'display_role_in_exception' => false,

    /*
     * When set to true, the "HasRoles" trait will check the permission against
     * both the "name" and "guard_name" columns.
     */

    'enable_wildcard_permission' => false,

    /*
     * The cache configuration for the package.
     */

    'cache' => [

        /*
         * By default, all permissions are cached for 24 hours to speed up performance.
         * When permissions or roles are updated the cache is flushed automatically.
         */

        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        /*
         * The key to be used in the cache store.
         */

        'key' => 'spatie.permission.cache',

        /*
         * You may optionally specify a specific cache driver to use for permissions
         * and roles caching. If none is specified, the default drive is used.
         */

        'store' => 'default',
    ],
];
