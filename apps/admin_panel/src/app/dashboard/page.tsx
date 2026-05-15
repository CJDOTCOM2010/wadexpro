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
    Zap,
    Shield,
    Settings,
    Package,
    DollarSign,
    Target,
    Award,
    BarChart3,
    RefreshCw,
    Database,
    Server,
    Globe,
    Building2,
    Clock3,
    AlertCircle,
    CheckCircle,
    XCircle,
    Eye,
    Ban,
    Star
} from 'lucide-react'
import Link from 'next/link'
import { useSocket } from '@/hooks/useSocket'
import {
    AreaChart,
    Area,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    ResponsiveContainer
} from 'recharts'

interface DashboardData {
    users: { total_customers: number; total_drivers: number; active_drivers: number }
    rides: { total: number; completed: number; pending: number; active: number }
    revenue: { today: number; month: number; currency: string }
    alerts: { pending_driver_approvals: number; active_sos_events: number }
}

interface ModuleStatus {
    name: string
    slug: string
    is_enabled: boolean
}

interface TopDriver {
    id: string
    name: string
    rides_completed: number
    earnings: number
    rating: number
    vehicle_type: string
}

interface RegionStat {
    region: string
    rides: number
    revenue: number
    drivers: number
}

const COLORS = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899']

export default function DashboardPage() {
    const { socket } = useSocket()
    const [data, setData] = useState<DashboardData | null>(null)
    const [loading, setLoading] = useState(true)
    const [liveEvents, setLiveEvents] = useState<any[]>([])
    
    // Additional data states
    const [revenueData, setRevenueData] = useState<any[]>([])
    const [moduleStatus, setModuleStatus] = useState<ModuleStatus[]>([])
    const [topDrivers, setTopDrivers] = useState<TopDriver[]>([])
    const [leaderboard, setLeaderboard] = useState<any>(null)
    const [ratio, setRatio] = useState<any>(null)
    const [regionStats, setRegionStats] = useState<RegionStat[]>([])
    const [logs, setLogs] = useState<any[]>([])

    const fetchOverview = () => {
        adminApi.getDashboardOverview()
            .then(res => setData(res.data.data))
            .catch(console.error)
            .finally(() => setLoading(false))
    }

    const loadAnalyticsData = async () => {
        try {
            const [revRes, modRes, leadRes, ratioRes, logRes] = await Promise.all([
                adminApi.getAnalyticsRevenue(7),
                adminApi.getModules(),
                adminApi.getLeaderboards(),
                adminApi.getSupplyDemandRatio(),
                adminApi.getLogs({ limit: '10' })
            ])
            
            if (revRes.data.data) {
                setRevenueData(revRes.data.data)
            }
            if (modRes.data.data) {
                setModuleStatus(modRes.data.data)
            }
            if (leadRes.data.data) {
                setLeaderboard(leadRes.data.data)
                setTopDrivers(leadRes.data.data.drivers?.slice(0, 5) || [])
            }
            if (ratioRes.data.data) {
                setRatio(ratioRes.data.data)
            }
            if (logRes.data.data) {
                setLogs(logRes.data.data)
            }
        } catch (error) {
            console.error('Failed to load analytics:', error)
        }
    }

    useEffect(() => {
        fetchOverview()
        loadAnalyticsData()
    }, [])

    useEffect(() => {
        if (!socket) return

        const handleNewLogisticsEvent = (event: any) => {
            setLiveEvents(prev => [{ ...event, time: new Date().toLocaleTimeString() }, ...prev].slice(0, 10))
            setData(prev => {
                if (!prev) return prev
                return {
                    ...prev,
                    rides: { ...prev.rides, active: prev.rides.active + 1 }
                }
            })
        }

        const handleStatusChange = (data: any) => {
            setLiveEvents(prev => [{ type: 'status_change', ...data, time: new Date().toLocaleTimeString() }, ...prev].slice(0, 10))
            if (data.status === 'completed') {
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
            change: '+12%',
            trend: 'up'
        },
        {
            name: 'Total Drivers',
            value: data?.users.total_drivers?.toLocaleString() || '0',
            icon: UserCheck,
            color: 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20',
            href: '/drivers',
            subtext: `${data?.users.active_drivers || 0} online`,
            change: '+8%',
            trend: 'up'
        },
        {
            name: 'Active Rides',
            value: data?.rides.active?.toLocaleString() || '0',
            icon: Car,
            color: 'text-amber-600 bg-amber-50 dark:bg-amber-900/20',
            href: '/rides',
            subtext: `${data?.rides.pending || 0} pending`,
            change: '+5%',
            trend: 'up'
        },
        {
            name: 'Completed Rides',
            value: data?.rides.completed?.toLocaleString() || '0',
            icon: Activity,
            color: 'text-purple-600 bg-purple-50 dark:bg-purple-900/20',
            href: '/rides?status=completed',
            change: '+15%',
            trend: 'up'
        },
        {
            name: "Today's Revenue",
            value: `${data?.revenue.currency || 'GHS'} ${data?.revenue.today?.toLocaleString() || '0'}`,
            icon: Wallet,
            color: 'text-green-600 bg-green-50 dark:bg-green-900/20',
            href: '/payments',
            change: '+23%',
            trend: 'up'
        },
        {
            name: 'Monthly Revenue',
            value: `${data?.revenue.currency || 'GHS'} ${data?.revenue.month?.toLocaleString() || '0'}`,
            icon: TrendingUp,
            color: 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20',
            href: '/payments',
            change: '+18%',
            trend: 'up'
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

    const quickActions = [
        { name: 'Add User', href: '/systems-command/users', icon: Users, color: 'bg-blue-500' },
        { name: 'Module Control', href: '/systems-command/modules', icon: Layers, color: 'bg-purple-500' },
        { name: 'System Settings', href: '/systems-command/settings', icon: Settings, color: 'bg-zinc-500' },
        { name: 'View Reports', href: '/reports/analytics', icon: BarChart3, color: 'bg-amber-500' },
    ]

    const getModuleStatusIcon = (enabled: boolean) => {
        return enabled ? <CheckCircle className="h-4 w-4 text-emerald-500" /> : <XCircle className="h-4 w-4 text-red-400" />
    }

    return (
        <DashboardLayout>
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-bold text-zinc-900 dark:text-white">Super Admin Overview</h1>
                        <p className="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            Platform overview, analytics, and system control center
                        </p>
                    </div>
                    <div className="flex items-center gap-3">
                        <button 
                            onClick={() => { fetchOverview(); loadAnalyticsData(); }}
                            className="flex items-center gap-2 px-4 py-2 text-sm font-medium text-zinc-600 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors"
                        >
                            <RefreshCw className="h-4 w-4" />
                            Refresh
                        </button>
                        <Link
                            href="/systems-command"
                            className="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <Shield className="h-4 w-4" />
                            System Control
                        </Link>
                    </div>
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

                {/* Main Stats Grid */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                    {stats.map((stat) => (
                        <Link
                            key={stat.name}
                            href={stat.href}
                            className="group relative overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5 shadow-sm transition-all hover:shadow-md hover:border-blue-200 dark:hover:border-blue-800"
                        >
                            <div className="flex items-start justify-between mb-3">
                                <div className={`rounded-lg p-2.5 ${stat.color}`}>
                                    <stat.icon className="h-5 w-5" />
                                </div>
                                {stat.change && (
                                    <span className={`flex items-center text-xs font-bold ${stat.trend === 'up' ? 'text-emerald-600' : 'text-red-600'}`}>
                                        {stat.trend === 'up' ? <ArrowUpRight className="h-3 w-3" /> : <ArrowDownRight className="h-3 w-3" />}
                                        {stat.change}
                                    </span>
                                )}
                            </div>
                            <p className="text-sm font-medium text-zinc-500 dark:text-zinc-400">{stat.name}</p>
                            <p className="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{stat.value}</p>
                            {stat.subtext && (
                                <p className="mt-1 text-xs text-zinc-400 dark:text-zinc-500">{stat.subtext}</p>
                            )}
                            <div className="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out" />
                        </Link>
                    ))}
                </div>

                {/* Charts & Analytics Row */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Revenue Chart */}
                    <div className="lg:col-span-2 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                        <div className="flex items-center justify-between p-4 border-b border-zinc-100 dark:border-zinc-800">
                            <div className="flex items-center gap-2">
                                <TrendingUp className="h-4 w-4 text-emerald-600" />
                                <h3 className="text-sm font-bold text-zinc-900 dark:text-white">Revenue Trends</h3>
                            </div>
                            <select className="text-xs border border-zinc-200 dark:border-zinc-700 rounded-lg px-2 py-1 bg-transparent">
                                <option>Last 7 days</option>
                                <option>Last 30 days</option>
                                <option>Last 90 days</option>
                            </select>
                        </div>
                        <div className="p-4 h-64">
                            {revenueData.length > 0 ? (
                                <ResponsiveContainer width="100%" height="100%">
                                    <AreaChart data={revenueData}>
                                        <defs>
                                            <linearGradient id="colorRevenue" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="5%" stopColor="#10B981" stopOpacity={0.3}/>
                                                <stop offset="95%" stopColor="#10B981" stopOpacity={0}/>
                                            </linearGradient>
                                        </defs>
                                        <CartesianGrid strokeDasharray="3 3" stroke="#E5E7EB" />
                                        <XAxis dataKey="date" fontSize={11} stroke="#9CA3AF" />
                                        <YAxis fontSize={11} stroke="#9CA3AF" />
                                        <Tooltip 
                                            contentStyle={{ borderRadius: '8px', border: '1px solid #E5E7EB' }}
                                            formatter={(value: number) => [`GHS ${value.toLocaleString()}`, 'Revenue']}
                                        />
                                        <Area 
                                            type="monotone" 
                                            dataKey="revenue" 
                                            stroke="#10B981" 
                                            strokeWidth={2}
                                            fillOpacity={1} 
                                            fill="url(#colorRevenue)" 
                                        />
                                    </AreaChart>
                                </ResponsiveContainer>
                            ) : (
                                <div className="flex items-center justify-center h-full text-zinc-400 text-sm">
                                    Loading revenue data...
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Supply/Demand Ratio */}
                    <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                        <div className="flex items-center justify-between p-4 border-b border-zinc-100 dark:border-zinc-800">
                            <div className="flex items-center gap-2">
                                <Target className="h-4 w-4 text-purple-600" />
                                <h3 className="text-sm font-bold text-zinc-900 dark:text-white">Supply vs Demand</h3>
                            </div>
                        </div>
                        <div className="p-4 h-64">
                            {ratio ? (
                                <div className="flex flex-col items-center justify-center h-full">
                                    <div className="relative w-40 h-40">
                                        <svg className="w-full h-full transform -rotate-90">
                                            <circle cx="80" cy="80" r="70" stroke="#E5E7EB" strokeWidth="12" fill="none" />
                                            <circle 
                                                cx="80" cy="80" r="70" 
                                                stroke="#8B5CF6" 
                                                strokeWidth="12" 
                                                fill="none"
                                                strokeDasharray={`${(ratio.drivers_available / ratio.total_rides) * 440} 440`}
                                                strokeLinecap="round"
                                            />
                                        </svg>
                                        <div className="absolute inset-0 flex flex-col items-center justify-center">
                                            <span className="text-2xl font-bold text-zinc-900 dark:text-white">{ratio.drivers_available}</span>
                                            <span className="text-xs text-zinc-500">Available</span>
                                        </div>
                                    </div>
                                    <div className="mt-4 grid grid-cols-2 gap-4 w-full text-center">
                                        <div className="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                            <p className="text-lg font-bold text-emerald-600">{ratio.drivers_available}</p>
                                            <p className="text-xs text-zinc-500">Drivers</p>
                                        </div>
                                        <div className="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                            <p className="text-lg font-bold text-amber-600">{ratio.pending_rides}</p>
                                            <p className="text-xs text-zinc-500">Pending</p>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className="flex items-center justify-center h-full text-zinc-400 text-sm">
                                    Loading ratio data...
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* System Control Row */}
                <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    {/* Module Status */}
                    <div className="lg:col-span-2 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                        <div className="flex items-center justify-between p-4 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                            <div className="flex items-center gap-2">
                                <Layers className="h-4 w-4 text-blue-600" />
                                <h3 className="text-sm font-bold text-zinc-900 dark:text-white">System Modules</h3>
                            </div>
                            <Link href="/systems-command/modules" className="text-xs text-blue-600 hover:underline">Manage</Link>
                        </div>
                        <div className="p-4 grid grid-cols-2 gap-3">
                            {moduleStatus.slice(0, 6).map((mod) => (
                                <div key={mod.slug} className="flex items-center justify-between p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                                    <div className="flex items-center gap-2">
                                        {getModuleStatusIcon(mod.is_enabled)}
                                        <span className="text-sm font-medium text-zinc-700 dark:text-zinc-300">{mod.name}</span>
                                    </div>
                                    <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${mod.is_enabled ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-200 text-zinc-500'}`}>
                                        {mod.is_enabled ? 'ACTIVE' : 'DISABLED'}
                                    </span>
                                </div>
                            ))}
                            {moduleStatus.length === 0 && (
                                <div className="col-span-2 text-center py-4 text-zinc-400 text-sm">
                                    Loading modules...
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Quick Actions */}
                    <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                        <div className="flex items-center justify-between p-4 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                            <div className="flex items-center gap-2">
                                <Zap className="h-4 w-4 text-amber-500" />
                                <h3 className="text-sm font-bold text-zinc-900 dark:text-white">Quick Actions</h3>
                            </div>
                        </div>
                        <div className="p-4 space-y-2">
                            {quickActions.map((action) => (
                                <Link
                                    key={action.name}
                                    href={action.href}
                                    className="flex items-center gap-3 p-3 rounded-lg border border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors group"
                                >
                                    <div className={`rounded-lg p-2 text-white ${action.color}`}>
                                        <action.icon className="h-4 w-4" />
                                    </div>
                                    <span className="text-sm font-medium text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white">
                                        {action.name}
                                    </span>
                                    <ArrowUpRight className="h-4 w-4 ml-auto text-zinc-300 group-hover:text-zinc-500" />
                                </Link>
                            ))}
                        </div>
                    </div>

                    {/* System Health */}
                    <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                        <div className="flex items-center justify-between p-4 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                            <div className="flex items-center gap-2">
                                <Server className="h-4 w-4 text-emerald-600" />
                                <h3 className="text-sm font-bold text-zinc-900 dark:text-white">System Health</h3>
                            </div>
                        </div>
                        <div className="p-4 space-y-3">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <Database className="h-4 w-4 text-zinc-400" />
                                    <span className="text-sm text-zinc-600 dark:text-zinc-400">Database</span>
                                </div>
                                <span className="flex items-center gap-1 text-xs font-bold text-emerald-600">
                                    <CheckCircle className="h-3 w-3" /> Healthy
                                </span>
                            </div>
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <Server className="h-4 w-4 text-zinc-400" />
                                    <span className="text-sm text-zinc-600 dark:text-zinc-400">API Server</span>
                                </div>
                                <span className="flex items-center gap-1 text-xs font-bold text-emerald-600">
                                    <CheckCircle className="h-3 w-3" /> Online
                                </span>
                            </div>
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <Activity className="h-4 w-4 text-zinc-400" />
                                    <span className="text-sm text-zinc-600 dark:text-zinc-400">Socket</span>
                                </div>
                                <span className="flex items-center gap-1 text-xs font-bold text-emerald-600">
                                    <CheckCircle className="h-3 w-3" /> Connected
                                </span>
                            </div>
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <Globe className="h-4 w-4 text-zinc-400" />
                                    <span className="text-sm text-zinc-600 dark:text-zinc-400">Regions</span>
                                </div>
                                <span className="text-xs font-bold text-zinc-600 dark:text-zinc-400">3 Active</span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Top Drivers & Live Operations Row */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Top Drivers Leaderboard */}
                    <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                        <div className="flex items-center justify-between p-4 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                            <div className="flex items-center gap-2">
                                <Award className="h-4 w-4 text-amber-500" />
                                <h3 className="text-sm font-bold text-zinc-900 dark:text-white">Top Drivers</h3>
                            </div>
                            <Link href="/reports/drivers" className="text-xs text-blue-600 hover:underline">View All</Link>
                        </div>
                        <div className="divide-y divide-zinc-50 dark:divide-zinc-800">
                            {topDrivers.length > 0 ? (
                                topDrivers.map((driver, index) => (
                                    <div key={driver.id} className="flex items-center gap-3 p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                        <div className={`w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold ${
                                            index === 0 ? 'bg-amber-100 text-amber-600' :
                                            index === 1 ? 'bg-zinc-200 text-zinc-600' :
                                            index === 2 ? 'bg-orange-100 text-orange-600' :
                                            'bg-zinc-100 text-zinc-500'
                                        }`}>
                                            {index + 1}
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <p className="text-sm font-medium text-zinc-900 dark:text-white truncate">{driver.name}</p>
                                            <p className="text-xs text-zinc-500">{driver.rides_completed} rides • {driver.vehicle_type}</p>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-sm font-bold text-emerald-600">GHS {driver.earnings?.toLocaleString()}</p>
                                            <div className="flex items-center gap-1 text-xs text-amber-500">
                                                <Star className="h-3 w-3 fill-current" />
                                                {driver.rating?.toFixed(1)}
                                            </div>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <div className="p-8 text-center text-zinc-400 text-sm">
                                    Loading leaderboard...
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Live Operations Feed */}
                    <div className="lg:col-span-2 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                        <div className="flex items-center justify-between p-4 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                            <div className="flex items-center gap-2">
                                <Zap className="h-4 w-4 text-amber-500 fill-amber-500" />
                                <h3 className="text-sm font-bold text-zinc-900 dark:text-white">LIVE OPERATIONS</h3>
                            </div>
                            <div className="flex items-center gap-1.5">
                                <span className="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span className="text-[10px] font-black text-zinc-400 tracking-tighter uppercase">Real-time</span>
                            </div>
                        </div>
                        <div className="p-0 max-h-80 overflow-y-auto">
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
                </div>

                {/* Recent Activity Logs */}
                <div className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                    <div className="flex items-center justify-between p-4 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                        <div className="flex items-center gap-2">
                            <Activity className="h-4 w-4 text-blue-600" />
                            <h3 className="text-sm font-bold text-zinc-900 dark:text-white">Recent Activity Logs</h3>
                        </div>
                        <Link href="/monitoring/logs" className="text-xs text-blue-600 hover:underline">View All Logs</Link>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-zinc-50 dark:bg-zinc-800/50">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-bold text-zinc-500 uppercase">Timestamp</th>
                                    <th className="px-4 py-3 text-left text-xs font-bold text-zinc-500 uppercase">User</th>
                                    <th className="px-4 py-3 text-left text-xs font-bold text-zinc-500 uppercase">Action</th>
                                    <th className="px-4 py-3 text-left text-xs font-bold text-zinc-500 uppercase">Module</th>
                                    <th className="px-4 py-3 text-left text-xs font-bold text-zinc-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-zinc-50 dark:divide-zinc-800">
                                {logs.length > 0 ? (
                                    logs.slice(0, 5).map((log, i) => (
                                        <tr key={i} className="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                            <td className="px-4 py-3 text-xs text-zinc-500 font-mono">
                                                {new Date(log.created_at).toLocaleString()}
                                            </td>
                                            <td className="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">{log.user?.name || 'System'}</td>
                                            <td className="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">{log.action}</td>
                                            <td className="px-4 py-3 text-xs">
                                                <span className="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded text-zinc-600 dark:text-zinc-400">{log.module || 'System'}</span>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className={`text-[10px] font-bold px-2 py-1 rounded-full ${
                                                    log.status === 'success' ? 'bg-emerald-100 text-emerald-700' :
                                                    log.status === 'failed' ? 'bg-red-100 text-red-700' :
                                                    'bg-zinc-100 text-zinc-600'
                                                }`}>
                                                    {log.status || 'info'}
                                                </span>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan={5} className="px-4 py-8 text-center text-zinc-400 text-sm">
                                            Loading activity logs...
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}