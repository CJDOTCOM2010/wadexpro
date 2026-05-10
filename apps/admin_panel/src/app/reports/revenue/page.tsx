'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { 
    DollarSign, 
    TrendingUp, 
    Download, 
    Calendar,
    ArrowUpRight,
    Search
} from 'lucide-react'
import { 
    AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer,
    BarChart, Bar, Cell
} from 'recharts'

export default function RevenueReportPage() {
    const [data, setData] = useState<any[]>([])
    const [loading, setLoading] = useState(true)
    const [stats, setStats] = useState<any>(null)

    const fetchRevenue = async () => {
        setLoading(true)
        try {
            const [revResponse, overviewResponse] = await Promise.all([
                adminApi.getAnalyticsRevenue(30),
                adminApi.getAnalyticsOverview()
            ])
            setData(revResponse.data.data)
            setStats(overviewResponse.data.data)
        } catch (err) {
            console.error(err)
        }
        setLoading(false)
    }

    useEffect(() => { fetchRevenue() }, [])

    const exportCSV = () => {
        const headers = ['Date', 'Total Revenue (GHS)', 'Ride Count']
        const csvContent = "data:text/csv;charset=utf-8," 
            + headers.join(',') + "\n"
            + data.map(row => `${row.date},${row.total},${row.ride_count}`).join("\n")

        const encodedUri = encodeURI(csvContent)
        const link = document.createElement("a")
        link.setAttribute("href", encodedUri)
        link.setAttribute("download", `revenue_report_${new Date().toISOString().split('T')[0]}.csv`)
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
    }

    if (loading) {
        return (
            <DashboardLayout>
                <div className="flex items-center justify-center h-[calc(100vh-200px)]">
                    <div className="animate-spin h-8 w-8 border-4 border-blue-600 border-t-transparent rounded-full" />
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
                        <h1 className="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">Revenue Intelligence</h1>
                        <p className="mt-1 text-sm text-zinc-500 font-medium italic">Comprehensive monetization insights and growth trajectory</p>
                    </div>
                    <div className="flex items-center gap-3">
                        <button 
                            onClick={exportCSV}
                            className="flex items-center gap-2 px-4 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 transition-all shadow-sm"
                        >
                            <Download className="h-4 w-4" />
                            Export CSV
                        </button>
                        <button className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20">
                            <Calendar className="h-4 w-4" />
                            Last 30 Days
                        </button>
                    </div>
                </div>

                {/* Summary Metrics */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div className="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
                         <div className="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-2xl text-blue-600 w-fit mb-4">
                            <DollarSign className="h-6 w-6" />
                        </div>
                        <h3 className="text-xs font-bold text-zinc-500 uppercase tracking-widest">Monthly Revenue</h3>
                        <p className="text-4xl font-black text-zinc-900 dark:text-white mt-1">
                            GHS {new Intl.NumberFormat().format(stats?.monthly_revenue || 0)}
                        </p>
                        <div className="mt-4 flex items-center gap-2 text-emerald-600 font-bold text-xs bg-emerald-50 dark:bg-emerald-900/10 px-2 py-1 rounded-full w-fit">
                            <TrendingUp className="h-3 w-3" />
                            +12.5% from last month
                        </div>
                    </div>

                    <div className="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                         <div className="p-3 bg-zinc-50 dark:bg-zinc-800 rounded-2xl text-zinc-600 w-fit mb-4">
                            <ArrowUpRight className="h-6 w-6" />
                        </div>
                        <h3 className="text-xs font-bold text-zinc-500 uppercase tracking-widest">Platform Commission (20%)</h3>
                        <p className="text-4xl font-black text-zinc-900 dark:text-white mt-1">
                            GHS {new Intl.NumberFormat().format((stats?.monthly_revenue || 0) * 0.2)}
                        </p>
                    </div>

                    <div className="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                         <div className="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-2xl text-amber-600 w-fit mb-4">
                            <TrendingUp className="h-6 w-6" />
                        </div>
                        <h3 className="text-xs font-bold text-zinc-500 uppercase tracking-widest">Average Fare</h3>
                        <p className="text-4xl font-black text-zinc-900 dark:text-white mt-1">
                            GHS {new Intl.NumberFormat().format((stats?.monthly_revenue / stats?.total_rides) || 0)}
                        </p>
                    </div>
                </div>

                {/* Main Revenue Chart */}
                <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                    <div className="flex items-center justify-between mb-8">
                        <div>
                            <h2 className="text-2xl font-black text-zinc-900 dark:text-white">Revenue Velocity</h2>
                            <p className="text-sm text-zinc-500 font-medium">Daily income aggregation across all vehicle categories</p>
                        </div>
                    </div>
                    <div className="h-[450px] w-full">
                        <ResponsiveContainer width="100%" height="100%">
                            <AreaChart data={data}>
                                <defs>
                                    <linearGradient id="revenueGradient" x1="0" y1="0" x2="0" y2="1">
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
                                    tickFormatter={(val) => `GHS ${val}`}
                                />
                                <Tooltip 
                                    contentStyle={{borderRadius: '20px', border: 'none', boxShadow: '0 25px 50px -12px rgba(0,0,0,0.25)', padding: '16px'}}
                                    itemStyle={{fontWeight: 'bold'}}
                                />
                                <Area 
                                    type="monotone" 
                                    dataKey="total" 
                                    stroke="#2563eb" 
                                    strokeWidth={4} 
                                    fillOpacity={1} 
                                    fill="url(#revenueGradient)" 
                                />
                            </AreaChart>
                        </ResponsiveContainer>
                    </div>
                </div>

                {/* Ride Count Histogram */}
                <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                    <h2 className="text-2xl font-black text-zinc-900 dark:text-white mb-6">Market Activity (Ride Count)</h2>
                    <div className="h-[250px] w-full">
                         <ResponsiveContainer width="100%" height="100%">
                            <BarChart data={data}>
                                <XAxis dataKey="date" hide />
                                <Tooltip cursor={{fill: 'transparent'}} contentStyle={{borderRadius: '12px'}} />
                                <Bar dataKey="ride_count" radius={[4, 4, 0, 0]}>
                                    {data.map((entry, index) => (
                                        <Cell key={`cell-${index}`} fill={index % 2 === 0 ? '#3b82f6' : '#93c5fd'} />
                                    ))}
                                </Bar>
                            </BarChart>
                        </ResponsiveContainer>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}
