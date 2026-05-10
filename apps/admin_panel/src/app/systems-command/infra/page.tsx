'use client'

import { useState, useEffect } from 'react'
import DashboardLayout from '@/components/DashboardLayout'
import axios from '@/lib/axios'
import { Cpu, Activity, Database, Zap, HardDrive, RefreshCw, CheckCircle2, Terminal } from 'lucide-react'
import { Button } from '@/components/ui/button'
import StepUpGuard from '@/components/StepUpGuard'

export default function InfrastructureHUDPage() {
    const [stats, setStats] = useState<any>({
        database: { status: 'healthy', latency: '12ms', storage: '4.2GB / 20GB' },
        cache: { status: 'online', memory: '1.2GB / 4GB', hits: '98.4%' },
        queues: { status: 'processing', pending: 12, failed: 0, processed_last_hr: 1420 },
        workers: { active: 8, idle: 2, peak_load: '42%' }
    })
    const [isProbing, setIsProbing] = useState(false)

    const probeSystem = async () => {
        setIsProbing(true)
        // Mocking a deep system probe
        setTimeout(() => {
            setIsProbing(false)
        }, 1500)
    }

    return (
        <DashboardLayout>
            <StepUpGuard>
                <div className="max-w-7xl mx-auto space-y-8">
                    <header className="flex justify-between items-end border-b border-zinc-200 dark:border-zinc-800 pb-8">
                        <div>
                             <div className="flex items-center gap-2 mb-2">
                                 <span className="px-2 py-0.5 bg-emerald-600 text-[10px] font-bold text-white rounded uppercase tracking-tighter">Live Monitor</span>
                                 <span className="text-zinc-400 text-xs">/ Systems Command</span>
                            </div>
                            <h1 className="text-4xl font-black tracking-tight text-zinc-900 dark:text-zinc-50 flex items-center gap-3">
                                <Cpu className="w-10 h-10 text-emerald-600" />
                                Infrastructure HUD
                            </h1>
                            <p className="mt-2 text-lg text-zinc-600 dark:text-zinc-400">
                                Real-time diagnostics of the WADEXP cloud engine and data persistent layers.
                            </p>
                        </div>
                        <Button 
                            onClick={probeSystem}
                            disabled={isProbing}
                            className="bg-emerald-600 hover:bg-emerald-700 text-white rounded-[1.2rem] px-8 py-6 font-black uppercase tracking-widest text-xs shadow-xl shadow-emerald-500/20"
                        >
                            <RefreshCw className={`w-4 h-4 mr-2 ${isProbing ? 'animate-spin' : ''}`} />
                            {isProbing ? 'Probing Engine...' : 'Run Deep Diagnostic'}
                        </Button>
                    </header>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {/* Database Health */}
                        <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4">
                            <div className="flex items-center justify-between">
                                <div className="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                                    <Database className="w-6 h-6 text-blue-600" />
                                </div>
                                <CheckCircle2 className="w-5 h-5 text-emerald-500" />
                            </div>
                            <div>
                                <h3 className="text-lg font-black text-zinc-900 dark:text-white">Database Core</h3>
                                <p className="text-xs text-zinc-500">PostgreSQL Cloud Cluster</p>
                            </div>
                            <div className="space-y-2 pt-4">
                                <div className="flex justify-between text-xs">
                                    <span className="text-zinc-500">Latency</span>
                                    <span className="font-bold text-emerald-500">{stats.database.latency}</span>
                                </div>
                                <div className="flex justify-between text-xs">
                                    <span className="text-zinc-500">Storage</span>
                                    <span className="font-bold">{stats.database.storage}</span>
                                </div>
                            </div>
                        </div>

                        {/* Cache Health */}
                        <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4">
                            <div className="flex items-center justify-between">
                                <div className="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                                    <Zap className="w-6 h-6 text-amber-600" />
                                </div>
                                <CheckCircle2 className="w-5 h-5 text-emerald-500" />
                            </div>
                            <div>
                                <h3 className="text-lg font-black text-zinc-900 dark:text-white">Cache Layer</h3>
                                <p className="text-xs text-zinc-500">Redis In-Memory Optima</p>
                            </div>
                            <div className="space-y-2 pt-4">
                                <div className="flex justify-between text-xs">
                                    <span className="text-zinc-500">Memory Use</span>
                                    <span className="font-bold text-amber-500">{stats.cache.memory}</span>
                                </div>
                                <div className="flex justify-between text-xs">
                                    <span className="text-zinc-500">Hit Rate</span>
                                    <span className="font-bold font-mono">{stats.cache.hits}</span>
                                </div>
                            </div>
                        </div>

                        {/* Queue Systems */}
                        <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4">
                            <div className="flex items-center justify-between">
                                <div className="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
                                    <RefreshCw className="w-6 h-6 text-purple-600" />
                                </div>
                                <Activity className="w-5 h-5 text-purple-500 animate-pulse" />
                            </div>
                            <div>
                                <h3 className="text-lg font-black text-zinc-900 dark:text-white">Async Queues</h3>
                                <p className="text-xs text-zinc-500">Job Processing Engine</p>
                            </div>
                            <div className="space-y-2 pt-4">
                                <div className="flex justify-between text-xs">
                                    <span className="text-zinc-500">Pending</span>
                                    <span className="font-bold text-purple-500 font-mono">{stats.queues.pending} jobs</span>
                                </div>
                                <div className="flex justify-between text-xs">
                                    <span className="text-zinc-500">Processed/hr</span>
                                    <span className="font-bold font-mono">{stats.queues.processed_last_hr}</span>
                                </div>
                            </div>
                        </div>

                        {/* Storage / Workers */}
                        <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4">
                            <div className="flex items-center justify-between">
                                <div className="p-3 bg-rose-50 dark:bg-rose-900/20 rounded-xl">
                                    <HardDrive className="w-6 h-6 text-rose-600" />
                                </div>
                                <div className="px-2 py-0.5 bg-rose-100 text-[8px] font-black text-rose-600 rounded uppercase">Peak Demand</div>
                            </div>
                            <div>
                                <h3 className="text-lg font-black text-zinc-900 dark:text-white">Fleet Workers</h3>
                                <p className="text-xs text-zinc-500">API Execution Nodes</p>
                            </div>
                            <div className="space-y-2 pt-4">
                                <div className="flex justify-between text-xs">
                                    <span className="text-zinc-500">Active Nodes</span>
                                    <span className="font-bold text-rose-500">{stats.workers.active} / 10</span>
                                </div>
                                <div className="flex justify-between text-xs">
                                    <span className="text-zinc-500">Peak Load</span>
                                    <span className="font-bold font-mono">{stats.workers.peak_load}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* System Terminal View */}
                    <div className="bg-zinc-950 rounded-[2.5rem] p-10 border border-zinc-800 shadow-2xl">
                        <div className="flex items-center gap-3 mb-8">
                            <Terminal className="w-6 h-6 text-emerald-500" />
                            <h2 className="text-xl font-black text-white uppercase tracking-widest">Global Terminal Output</h2>
                        </div>
                        <div className="space-y-3 font-mono text-[11px]">
                            <p className="text-emerald-500/80">[13:04:22] <span className="text-white">SUCCESS:</span> Redis cluster heartbeat synchronized (Region: Accra-North)</p>
                            <p className="text-emerald-500/80">[13:04:45] <span className="text-blue-400">INFO:</span> Optimizing Geofencing shards for higher concurrency...</p>
                            <p className="text-amber-500/80">[13:05:01] <span className="text-amber-400">WARN:</span> Worker #4 experienced minor latency spike (450ms)</p>
                            <p className="text-emerald-500/80">[13:06:12] <span className="text-white">SUCCESS:</span> Database vacuuming operation complete (Saved 45MB)</p>
                            <p className="text-zinc-600 animate-pulse">_ Waiting for next system interrupt...</p>
                        </div>
                    </div>
                </div>
            </StepUpGuard>
        </DashboardLayout>
    )
}
