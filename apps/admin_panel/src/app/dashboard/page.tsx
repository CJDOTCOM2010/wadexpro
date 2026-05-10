'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import {
    Users,
    Car,
    Wallet,
    TrendingUp,
    AlertTriangle,
    UserCheck,
    ArrowUpRight,
    ArrowDownRight,
    Activity,
    MapPin,
    Layers,
    Clock,
    Zap
} from 'lucide-react'
import Link from 'next/link'
import { useSocket } from '@/hooks/useSocket'

interface DashboardData {
    users: { total_customers: number; total_drivers: number; active_drivers: number }
    rides: { total: number; completed: number; pending: number; active: number }
    revenue: { today: number; month: number; currency: string }
    alerts: { pending_driver_approvals: number; active_sos_events: number }
}

export default function DashboardPage() {
    const { socket } = useSocket()
    const [data, setData] = useState<DashboardData | null>(null)
    const [loading, setLoading] = useState(true)
    const [liveEvents, setLiveEvents] = useState<any[]>([])

    const fetchOverview = () => {
        adminApi.getDashboardOverview()
            .then(res => setData(res.data.data))
            .catch(console.error)
            .finally(() => setLoading(false))
    }

    useEffect(() => {
        fetchOverview()
    }, [])

    useEffect(() => {
        if (!socket) return

        const handleNewLogisticsEvent = (event: any) => {
            // Update Activity Feed
            setLiveEvents(prev => [{ ...event, time: new Date().toLocaleTimeString() }, ...prev].slice(0, 10))
            
            // Increment counters locally for instant feedback
            setData(prev => {
                if (!prev) return prev
                return {
                    ...prev,
                    rides: {
                        ...prev.rides,
                        active: prev.rides.active + 1
                    }
                }
            })
        }

        const handleStatusChange = (data: any) => {
            setLiveEvents(prev => [{ type: 'status_change', ...data, time: new Date().toLocaleTimeString() }, ...prev].slice(0, 10))
            if (data.status === 'completed') {
               // Refresh full numbers occasionally or decrement active
               fetchOverview()
            }
        }

        socket.on('ride:new_request', handleNewLogisticsEvent)
        socket.on('order:new_request', handleNewLogisticsEvent)
        socket.on('ride:accepted', handleStatusChange)
        socket.on('ride:status_change', handleStatusChange)

        return () => {
            socket.off('ride:new_request')
            socket.off('order:new_request')
            socket.off('ride:accepted')
            socket.off('ride:status_change')
        }
    }, [socket])

    if (loading) {
        return (
            <DashboardLayout>
                <div className="flex h-[80vh] items-center justify-center">
                    <div className="flex flex-col items-center gap-3">
                        <div className="h-8 w-8 animate-spin rounded-full border-4 border-blue-600 border-t-transparent" />
                        <p className="text-sm text-zinc-500">Loading dashboard...</p>
                    </div>
                </div>
            </DashboardLayout>
        )
    }

    const stats = [
        {
            name: 'Total Customers',
            value: data?.users.total_customers?.toLocaleString() || '0',
            icon: Users,
            color: 'text-blue-600 bg-blue-50 dark:bg-blue-900/20',
            href: '/customers',
        },
        {
            name: 'Total Drivers',
            value: data?.users.total_drivers?.toLocaleString() || '0',
            icon: UserCheck,
            color: 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20',
            href: '/drivers',
            subtext: `${data?.users.active_drivers || 0} online`,
        },
        {
            name: 'Active Rides',
            value: data?.rides.active?.toLocaleString() || '0',
            icon: Car,
            color: 'text-amber-600 bg-amber-50 dark:bg-amber-900/20',
            href: '/rides',
            subtext: `${data?.rides.pending || 0} pending`,
        },
        {
            name: 'Completed Rides',
            value: data?.rides.completed?.toLocaleString() || '0',
            icon: Activity,
            color: 'text-purple-600 bg-purple-50 dark:bg-purple-900/20',
            href: '/rides?status=completed',
        },
        {
            name: "Today's Revenue",
            value: `${data?.revenue.currency || 'GHS'} ${data?.revenue.today?.toLocaleString() || '0'}`,
            icon: Wallet,
            color: 'text-green-600 bg-green-50 dark:bg-green-900/20',
            href: '/payments',
            trend: 'up',
        },
        {
            name: 'Monthly Revenue',
            value: `${data?.revenue.currency || 'GHS'} ${data?.revenue.month?.toLocaleString() || '0'}`,
            icon: TrendingUp,
            color: 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20',
            href: '/payments',
        },
    ]

    const alerts = [
        ...(data?.alerts.pending_driver_approvals ? [{
            title: 'Pending Driver Approvals',
            count: data.alerts.pending_driver_approvals,
            href: '/drivers?status=pending_verification',
            color: 'text-amber-600 bg-amber-50 border-amber-200 dark:bg-amber-900/20 dark:border-amber-800',
            icon: UserCheck,
        }] : []),
        ...(data?.alerts.active_sos_events ? [{
            title: 'Active SOS Alerts',
            count: data.alerts.active_sos_events,
            href: '/sos',
            color: 'text-red-600 bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800',
            icon: AlertTriangle,
        }] : []),
    ]

    return (
        <DashboardLayout>
            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-zinc-900 dark:text-white">Dashboard</h1>
                    <p className="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Platform overview and key metrics
                    </p>
                </div>

                {/* Alert Banners */}
                {alerts.length > 0 && (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {alerts.map((alert) => (
                            <Link
                                key={alert.title}
                                href={alert.href}
                                className={`flex items-center gap-4 p-4 rounded-lg border transition-all hover:shadow-sm ${alert.color}`}
                            >
                                <alert.icon className="h-5 w-5 flex-shrink-0" />
                                <div className="flex-1">
                                    <p className="text-sm font-semibold">{alert.title}</p>
                                </div>
                                <span className="text-2xl font-bold">{alert.count}</span>
                                <ArrowUpRight className="h-4 w-4 opacity-60" />
                            </Link>
                        ))}
                    </div>
                )}

                {/* Stats Grid */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {stats.map((stat) => (
                        <Link
                            key={stat.name}
                            href={stat.href}
                            className="group relative overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm transition-all hover:shadow-md hover:border-blue-200 dark:hover:border-blue-800"
                        >
                            <div className="flex items-start justify-between">
                                <div>
                                    <p className="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                        {stat.name}
                                    </p>
                                    <p className="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">
                                        {stat.value}
                                    </p>
                                    {stat.subtext && (
                                        <p className="mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                                            {stat.subtext}
                                        </p>
                                    )}
                                </div>
                                <div className={`rounded-lg p-3 ${stat.color}`}>
                                    <stat.icon className="h-5 w-5" />
                                </div>
                            </div>
                            <div className="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out" />
                        </Link>
                    ))}
                </div>

                {/* Operations Feed & Quick Actions */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Live Operations Feed */}
                    <div className="lg:col-span-2 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                        <div className="flex items-center justify-between p-4 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                           <div className="flex items-center gap-2">
                                <Zap className="h-4 w-4 text-amber-500 fill-amber-500" />
                                <h3 className="text-sm font-bold text-zinc-900 dark:text-white">LIVE OPERATIONS</h3>
                           </div>
                           <div className="flex items-center gap-1.5">
                                <span className="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span className="text-[10px] font-black text-zinc-400 tracking-tighter uppercase">Tactical Link: Active</span>
                           </div>
                        </div>
                        <div className="p-0">
                            {liveEvents.length === 0 ? (
                                <div className="flex flex-col items-center justify-center py-20 text-zinc-400">
                                    <Clock className="h-8 w-8 mb-2 opacity-20" />
                                    <p className="text-xs">No recent activity detected</p>
                                </div>
                            ) : (
                                <div className="divide-y divide-zinc-50 dark:divide-zinc-800">
                                    {liveEvents.map((event, i) => (
                                        <div key={i} className="flex items-center justify-between p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                            <div className="flex items-center gap-4">
                                                <div className={`p-2 rounded-lg ${
                                                    event.type === 'delivery' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600'
                                                }`}>
                                                    <Car className="h-4 w-4" />
                                                </div>
                                                <div>
                                                    <p className="text-sm font-bold text-zinc-900 dark:text-white">
                                                        {event.type === 'delivery' ? 'Express Delivery' : 'Ride Request'} 
                                                        <span className="ml-2 text-[10px] text-zinc-400 font-normal">#{event.rideId?.slice(-6)}</span>
                                                    </p>
                                                    <p className="text-xs text-zinc-500 truncate max-w-[300px]">
                                                        {event.pickupAddress || 'Updating location...'}
                                                    </p>
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-xs font-mono text-zinc-400">{event.time}</p>
                                                <span className={`text-[9px] font-black px-1.5 py-0.5 rounded uppercase ${
                                                    event.status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'
                                                }`}>
                                                    {event.status || 'Dispatched'}
                                                </span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Quick Actions Panel */}
                    <div className="space-y-4">
                        <h3 className="text-xs font-black text-zinc-400 uppercase tracking-widest pl-1">Command Shortcuts</h3>
                        <div className="grid grid-cols-1 gap-3">
                            <Link
                                href="/live-map"
                                className="flex items-center gap-3 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-colors group"
                            >
                                <div className="rounded-lg p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    <MapPin className="h-5 w-5" />
                                </div>
                                <span className="text-sm font-bold text-zinc-700 dark:text-zinc-300">Tactical Map</span>
                            </Link>
                            <Link
                                href="/drivers?status=pending_verification"
                                className="flex items-center gap-3 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-amber-50 dark:hover:bg-amber-900/10 transition-colors group"
                            >
                                <div className="rounded-lg p-2 bg-amber-50 dark:bg-amber-900/20 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-all">
                                    <UserCheck className="h-5 w-5" />
                                </div>
                                <span className="text-sm font-bold text-zinc-700 dark:text-zinc-300">Compliance Queue</span>
                            </Link>
                            <Link
                                href="/systems-command/modules"
                                className="flex items-center gap-3 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-colors group"
                            >
                                <div className="rounded-lg p-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                                    <Layers className="h-5 w-5" />
                                </div>
                                <span className="text-sm font-bold text-zinc-700 dark:text-zinc-300">Terminal Modules</span>
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </DashboardLayout>
    )
}
