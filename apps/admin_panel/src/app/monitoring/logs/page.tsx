'use client'

import { useState, useEffect } from 'react'
import DashboardLayout from '@/components/DashboardLayout'
import axios from '@/lib/axios'
import { Activity, User, Clock, Terminal, Search, Filter, RefreshCcw } from 'lucide-react'
import { Button } from '@/components/ui/button'

interface AuditLog {
    id: string
    action: string
    description: string
    ip_address: string
    user: {
        name: string
    }
    created_at: string
}

export default function AuditLogsPage() {
    const [logs, setLogs] = useState<AuditLog[]>([])
    const [isLoading, setIsLoading] = useState(true)
    const [isRefreshing, setIsRefreshing] = useState(false)

    useEffect(() => {
        fetchLogs()
        // Auto-refresh every 30 seconds for "Live" feel
        const interval = setInterval(fetchLogs, 30000)
        return () => clearInterval(interval)
    }, [])

    const fetchLogs = async () => {
        setIsRefreshing(true)
        try {
            const response = await axios.get('/monitoring/logs')
            setLogs(response.data.data)
        } catch (error) {
            console.error('Failed to stream audit logs.')
        } finally {
            setIsLoading(false)
            setIsRefreshing(false)
        }
    }

    return (
        <DashboardLayout>
            <div className="max-w-7xl mx-auto space-y-8">
                <header className="flex justify-between items-end">
                    <div>
                        <h1 className="text-4xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 flex items-center gap-3">
                            <Activity className="w-10 h-10 text-blue-600" />
                            Live Audit Trail
                        </h1>
                        <p className="mt-2 text-lg text-zinc-600 dark:text-zinc-400">
                            Real-time oversight of all administrative and operational platform interactions.
                        </p>
                    </div>
                    <div className="flex gap-3">
                        <Button 
                            onClick={fetchLogs}
                            disabled={isRefreshing}
                            className="bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 rounded-xl px-5 h-12 font-semibold transition-all"
                        >
                            <RefreshCcw className={`w-4 h-4 mr-2 ${isRefreshing ? 'animate-spin' : ''}`} />
                            Sync Now
                        </Button>
                    </div>
                </header>

                <div className="relative group">
                    <div className="absolute inset-y-0 left-5 flex items-center pointer-events-none">
                        <Search className="h-5 w-5 text-zinc-400" />
                    </div>
                    <input 
                        type="text" 
                        placeholder="Search logs by action or user..."
                        className="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl py-4 pl-14 pr-4 shadow-sm focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"
                    />
                </div>

                <div className="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                                <th className="px-8 py-5 text-xs font-bold uppercase tracking-widest text-zinc-500">Operation</th>
                                <th className="px-8 py-5 text-xs font-bold uppercase tracking-widest text-zinc-500">Actor</th>
                                <th className="px-8 py-5 text-xs font-bold uppercase tracking-widest text-zinc-500">Source IP</th>
                                <th className="px-8 py-5 text-xs font-bold uppercase tracking-widest text-zinc-500">Timestamp</th>
                                <th className="px-8 py-5 text-right text-xs font-bold uppercase tracking-widest text-zinc-500">Summary</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-zinc-100 dark:divide-zinc-800 font-mono text-xs">
                            {isLoading ? (
                                [1, 2, 3, 4, 5].map(i => (
                                    <tr key={i} className="animate-pulse">
                                        <td colSpan={5} className="px-8 py-8 h-12 bg-zinc-50/20 dark:bg-zinc-900/50" />
                                    </tr>
                                ))
                            ) : (
                                logs.map((log) => (
                                    <tr key={log.id} className="hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors">
                                        <td className="px-8 py-4">
                                            <div className="flex items-center gap-3">
                                                <div className="w-2 h-2 rounded-full bg-blue-500" />
                                                <span className="font-bold text-zinc-900 dark:text-zinc-200 uppercase">{log.action || 'INTERACTION'}</span>
                                            </div>
                                        </td>
                                        <td className="px-8 py-4">
                                            <div className="flex items-center gap-2">
                                                <User className="w-3 h-3 text-zinc-400" />
                                                <span className="text-zinc-600 dark:text-zinc-400">{log.user?.name || 'SYSTEM'}</span>
                                            </div>
                                        </td>
                                        <td className="px-8 py-4 text-zinc-500">
                                            {log.ip_address}
                                        </td>
                                        <td className="px-8 py-4">
                                            <div className="flex items-center gap-2 text-zinc-500">
                                                <Clock className="w-3 h-3" />
                                                {new Date(log.created_at).toLocaleString()}
                                            </div>
                                        </td>
                                        <td className="px-8 py-4 text-right overflow-hidden max-w-xs">
                                            <div className="inline-flex items-center gap-2 bg-zinc-100 dark:bg-zinc-800 px-3 py-1.5 rounded-lg text-zinc-700 dark:text-zinc-300">
                                                <Terminal className="w-3 h-3 text-zinc-400" />
                                                <span className="truncate">{log.description || 'Generic interaction trace.'}</span>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>

                <footer className="flex justify-between items-center text-xs text-zinc-500 mt-4 px-4">
                    <p>Displaying latest 50 operations. Audit persistence enabled for 365 days.</p>
                    <div className="flex gap-2">
                        <Button variant="outline" className="h-8 text-[10px] rounded-lg">Export CSV</Button>
                        <Button variant="outline" className="h-8 text-[10px] rounded-lg">Full Archive</Button>
                    </div>
                </footer>
            </div>
        </DashboardLayout>
    )
}
