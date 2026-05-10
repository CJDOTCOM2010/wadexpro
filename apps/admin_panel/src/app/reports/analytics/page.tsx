'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { 
    TrendingUp, 
    TrendingDown, 
    DollarSign, 
    Car, 
    Users, 
    Calendar,
    ArrowUpRight,
    Map as MapIcon,
    Filter
} from 'lucide-react'
import { 
    AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer,
    PieChart, Pie, Cell, Legend, BarChart, Bar
} from 'recharts'

export default function AnalyticsPage() {
    const [overview, setOverview] = useState<any>(null)
    const [trends, setTrends] = useState<any[]>([])
    const [distribution, setDistribution] = useState<any[]>([])
    const [loading, setLoading] = useState(true)

    const COLORS = ['#2563eb', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6'];

    const fetchData = async () => {
        setLoading(true)
        try {
            const [ov, tr, di] = await Promise.all([
                adminApi.getAnalyticsOverview(),
                adminApi.getAnalyticsTrends(),
                adminApi.getAnalyticsDistribution()
            ])
            setOverview(ov.data.metrics)
            setTrends(tr.data)
            setDistribution(di.data)
        } catch (err) {
            console.error(err)
        }
        setLoading(false)
    }

    useEffect(() => { fetchData() }, [])

    if (loading) {
        return (
            <DashboardLayout>
                <div className="flex items-center justify-center h-[calc(100vh-200px)]">
                    <div className="flex flex-col items-center gap-4">
                        <div className="h-10 w-10 animate-spin rounded-full border-4 border-blue-600 border-t-transparent" />
                        <p className="text-zinc-500 font-medium">Synthesizing Market Data...</p>
                    </div>
                </div>
            </DashboardLayout>
        )
    }

    return (
        <DashboardLayout>
            <div className="space-y-8 pb-12">
                {/* Header */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 className="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">Orchestra Intelligence</h1>
                        <p className="mt-1 text-sm text-zinc-500 font-medium italic">High-fidelity operational performance metrics and growth forecasting</p>
                    </div>
                    <div className="flex items-center gap-3">
                        <button className="flex items-center gap-2 px-4 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 transition-all shadow-sm">
                            <Calendar className="h-4 w-4" />
                            Last 30 Days
                        </button>
                        <button 
                            onClick={fetchData}
                            className="p-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20"
                        >
                            <ArrowUpRight className="h-5 w-5" />
                        </button>
                    </div>
                </div>

                {/* Primary Metric Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {/* Revenue Card */}
                    <div className="relative overflow-hidden bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm group">
                        <div className="absolute top-0 right-0 p-8 opacity-[0.03] group-hover:scale-110 transition-transform">
                            <DollarSign className="h-24 w-24 text-blue-600" />
                        </div>
                        <div className="flex items-center justify-between mb-4">
                            <div className="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-2xl text-blue-600">
                                <DollarSign className="h-6 w-6" />
                            </div>
                            <div className={`flex items-center gap-1 text-xs font-black px-2 py-1 rounded-full ${overview?.revenue?.growth >= 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'}`}>
                                {overview?.revenue?.growth >= 0 ? <TrendingUp className="h-3 w-3" /> : <TrendingDown className="h-3 w-3" />}
                                {Math.abs(overview?.revenue?.growth)}%
                            </div>
                        </div>
                        <h3 className="text-sm font-bold text-zinc-500 uppercase tracking-widest">{overview?.revenue?.label}</h3>
                        <p className="text-4xl font-black text-zinc-900 dark:text-white mt-1">
                            {new Intl.NumberFormat('en-GH', { style: 'currency', currency: 'GHS' }).format(overview?.revenue?.current)}
                        </p>
                    </div>

                    {/* Ride Volume Card */}
                    <div className="relative overflow-hidden bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm group">
                        <div className="absolute top-0 right-0 p-8 opacity-[0.03] group-hover:scale-110 transition-transform">
                            <Car className="h-24 w-24 text-amber-600" />
                        </div>
                        <div className="flex items-center justify-between mb-4">
                            <div className="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-2xl text-amber-600">
                                <Car className="h-6 w-6" />
                            </div>
                            <div className="text-[10px] font-bold text-amber-600 uppercase tracking-widest px-2 py-1 bg-amber-100 rounded-full">
                                {overview?.rides?.active} active rides
                            </div>
                        </div>
                        <h3 className="text-sm font-bold text-zinc-500 uppercase tracking-widest">{overview?.rides?.label}</h3>
                        <p className="text-4xl font-black text-zinc-900 dark:text-white mt-1">
                            {overview?.rides?.completed} <span className="text-lg font-bold text-zinc-400">TODAY</span>
                        </p>
                    </div>

                    {/* Fleet Utilization Card */}
                    <div className="relative overflow-hidden bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm group">
                        <div className="absolute top-0 right-0 p-8 opacity-[0.03] group-hover:scale-110 transition-transform">
                            <Users className="h-24 w-24 text-emerald-600" />
                        </div>
                        <div className="flex items-center justify-between mb-4">
                            <div className="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl text-emerald-600">
                                <Users className="h-6 w-6" />
                            </div>
                            <div className="flex items-center gap-1.5">
                                <div className="h-2 w-2 rounded-full bg-emerald-500 animate-pulse" />
                                <span className="text-xs font-bold text-zinc-500 uppercase tracking-widest">{overview?.fleet?.online} Online</span>
                            </div>
                        </div>
                        <h3 className="text-sm font-bold text-zinc-500 uppercase tracking-widest">{overview?.fleet?.label}</h3>
                        <p className="text-4xl font-black text-zinc-900 dark:text-white mt-1">
                            {Math.round((overview?.fleet?.available / overview?.fleet?.online) * 100 || 0)}% <span className="text-lg font-bold text-zinc-400">READY</span>
                        </p>
                    </div>
                </div>

                {/* Charts Area */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Revenue Trend Area Chart */}
                    <div className="lg:col-span-2 bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                        <div className="flex items-center justify-between mb-8">
                            <div>
                                <h3 className="text-xl font-black text-zinc-900 dark:text-white">Revenue Command</h3>
                                <p className="text-sm text-zinc-500">Historical performance & growth trend analysis</p>
                            </div>
                            <div className="flex gap-2">
                                <div className="flex items-center gap-2 px-3 py-1 bg-blue-50 dark:bg-blue-900/20 rounded-full">
                                    <div className="h-2 w-2 rounded-full bg-blue-600" />
                                    <span className="text-[10px] font-bold text-blue-600 uppercase">Monetization</span>
                                </div>
                            </div>
                        </div>
                        <div className="h-[400px] w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <AreaChart data={trends}>
                                    <defs>
                                        <linearGradient id="colorRev" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="5%" stopColor="#2563eb" stopOpacity={0.1}/>
                                            <stop offset="95%" stopColor="#2563eb" stopOpacity={0}/>
                                        </linearGradient>
                                    </defs>
                                    <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f0f0f0" />
                                    <XAxis 
                                        dataKey="date" 
                                        axisLine={false} 
                                        tickLine={false} 
                                        tick={{fontSize: 10, fontWeight: 'bold', fill: '#94a3b8'}}
                                        dy={10}
                                    />
                                    <YAxis 
                                        axisLine={false} 
                                        tickLine={false} 
                                        tick={{fontSize: 10, fontWeight: 'bold', fill: '#94a3b8'}}
                                    />
                                    <Tooltip 
                                        contentStyle={{borderRadius: '16px', border: 'none', boxShadow: '0 20px 25px -5px rgba(0,0,0,0.1)'}}
                                        labelStyle={{fontWeight: 'black', color: '#1e293b'}}
                                    />
                                    <Area 
                                        type="monotone" 
                                        dataKey="revenue" 
                                        stroke="#2563eb" 
                                        strokeWidth={4}
                                        fillOpacity={1} 
                                        fill="url(#colorRev)" 
                                    />
                                </AreaChart>
                            </ResponsiveContainer>
                        </div>
                    </div>

                    {/* Market Share Pie Chart */}
                    <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col">
                        <div className="mb-8">
                            <h3 className="text-xl font-black text-zinc-900 dark:text-white">Fleet Distribution</h3>
                            <p className="text-sm text-zinc-500">Revenue split by vehicle class</p>
                        </div>
                        <div className="flex-1 min-h-[300px]">
                            <ResponsiveContainer width="100%" height="100%">
                                <PieChart>
                                    <Pie
                                        data={distribution}
                                        cx="50%"
                                        cy="50%"
                                        innerRadius={60}
                                        outerRadius={100}
                                        paddingAngle={8}
                                        dataKey="value"
                                        nameKey="vehicle_type"
                                    >
                                        {distribution.map((entry, index) => (
                                            <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                                        ))}
                                    </Pie>
                                    <Tooltip 
                                        contentStyle={{borderRadius: '16px', border: 'none', boxShadow: '0 10px 15px -3px rgba(0,0,0,0.1)'}}
                                    />
                                    <Legend 
                                        verticalAlign="bottom" 
                                        iconType="circle"
                                        formatter={(value) => <span className="text-xs font-bold uppercase tracking-wider text-zinc-500">{value}</span>}
                                    />
                                </PieChart>
                            </ResponsiveContainer>
                        </div>
                        <div className="mt-4 pt-6 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                            <div className="flex items-center gap-2">
                                <div className="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                                    <MapIcon className="h-4 w-4 text-zinc-500" />
                                </div>
                                <span className="text-xs font-bold text-zinc-500 uppercase">Geo Coverage</span>
                            </div>
                            <span className="text-sm font-black text-zinc-900 dark:text-white">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}
