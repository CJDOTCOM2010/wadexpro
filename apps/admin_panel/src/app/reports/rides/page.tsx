'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { 
    Car, 
    CheckCircle2, 
    XCircle, 
    Clock,
    PieChart as PieIcon,
    BarChart3
} from 'lucide-react'
import { 
    PieChart, Pie, Cell, Tooltip, Legend, ResponsiveContainer,
    BarChart, Bar, XAxis, YAxis, CartesianGrid
} from 'recharts'

export default function RidesReportPage() {
    const [data, setData] = useState<any>(null)
    const [loading, setLoading] = useState(true)

    const COLORS = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];

    const fetchData = async () => {
        setLoading(true)
        try {
            const response = await adminApi.getAnalyticsRides()
            setData(response.data.data)
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
                    <div className="animate-spin h-8 w-8 border-4 border-blue-600 border-t-transparent rounded-full" />
                </div>
            </DashboardLayout>
        )
    }

    return (
        <DashboardLayout>
            <div className="space-y-8 pb-12">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">Fleet Dynamics</h1>
                    <p className="mt-1 text-sm text-zinc-500 font-medium italic">Operational volume, vehicle distribution, and fulfillment efficiency</p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {/* Status Distribution */}
                    <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center">
                        <div className="w-full mb-8">
                            <h2 className="text-2xl font-black text-zinc-900 dark:text-white">Fulfillment Funnel</h2>
                            <p className="text-sm text-zinc-500 font-medium">Breakdown of ride lifecycle states</p>
                        </div>
                        <div className="h-[350px] w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <PieChart>
                                    <Pie
                                        data={data?.by_status}
                                        cx="50%"
                                        cy="50%"
                                        innerRadius={80}
                                        outerRadius={120}
                                        paddingAngle={10}
                                        dataKey="count"
                                        nameKey="status"
                                        stroke="none"
                                    >
                                        {data?.by_status.map((entry: any, index: number) => (
                                            <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                                        ))}
                                    </Pie>
                                    <Tooltip 
                                        contentStyle={{borderRadius: '20px', border: 'none', boxShadow: '0 25px 50px -12px rgba(0,0,0,0.25)'}} 
                                    />
                                    <Legend iconType="circle" />
                                </PieChart>
                            </ResponsiveContainer>
                        </div>
                    </div>

                    {/* Vehicle Type Distribution */}
                    <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                        <div className="w-full mb-8">
                            <h2 className="text-2xl font-black text-zinc-900 dark:text-white">Vehicle Class Preference</h2>
                            <p className="text-sm text-zinc-500 font-medium">Regional demand by service category</p>
                        </div>
                        <div className="h-[350px] w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <BarChart data={data?.by_type} layout="vertical">
                                    <CartesianGrid strokeDasharray="3 3" horizontal={false} stroke="#f0f0f0" />
                                    <XAxis type="number" hide />
                                    <YAxis 
                                        dataKey="vehicle_type" 
                                        type="category" 
                                        axisLine={false} 
                                        tickLine={false}
                                        tick={{fontSize: 12, fontWeight: 'bold', fill: '#64748b', textTransform: 'uppercase'}}
                                    />
                                    <Tooltip cursor={{fill: 'transparent'}} />
                                    <Bar dataKey="count" fill="#3b82f6" radius={[0, 8, 8, 0]} barSize={40}>
                                        {data?.by_type.map((entry: any, index: number) => (
                                            <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                                        ))}
                                    </Bar>
                                </BarChart>
                            </ResponsiveContainer>
                        </div>
                    </div>
                </div>

                {/* KPI Grid */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div className="bg-zinc-900 p-6 rounded-3xl border border-zinc-800 text-white flex flex-col justify-between">
                         <div className="p-3 bg-blue-500/20 rounded-2xl text-blue-400 w-fit mb-4">
                            <CheckCircle2 className="h-6 w-6" />
                        </div>
                        <div>
                            <h3 className="text-xs font-bold text-zinc-500 uppercase tracking-widest">Completion Rate</h3>
                            <p className="text-3xl font-black mt-1">94.2%</p>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col justify-between">
                         <div className="p-3 bg-rose-50 dark:bg-rose-900/20 rounded-2xl text-rose-600 w-fit mb-4">
                            <XCircle className="h-6 w-6" />
                        </div>
                        <div>
                            <h3 className="text-xs font-bold text-zinc-500 uppercase tracking-widest">Cancellation Rate</h3>
                            <p className="text-3xl font-black text-zinc-900 dark:text-white mt-1">3.8%</p>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col justify-between">
                         <div className="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-2xl text-amber-600 w-fit mb-4">
                            <Clock className="h-6 w-6" />
                        </div>
                        <div>
                            <h3 className="text-xs font-bold text-zinc-500 uppercase tracking-widest">Avg Wait Time</h3>
                            <p className="text-3xl font-black text-zinc-900 dark:text-white mt-1">4.2 <span className="text-sm text-zinc-400">min</span></p>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col justify-between">
                         <div className="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-2xl text-purple-600 w-fit mb-4">
                            <BarChart3 className="h-6 w-6" />
                        </div>
                        <div>
                            <h3 className="text-xs font-bold text-zinc-500 uppercase tracking-widest">Active Fleet</h3>
                            <p className="text-3xl font-black text-zinc-900 dark:text-white mt-1">1,240</p>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}
