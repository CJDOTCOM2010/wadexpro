'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { 
    Users, 
    Star, 
    CheckCircle, 
    ShieldCheck,
    Award,
    TrendingUp,
    Briefcase
} from 'lucide-react'

export default function DriversReportPage() {
    const [performance, setPerformance] = useState<any[]>([])
    const [loading, setLoading] = useState(true)

    const fetchData = async () => {
        setLoading(true)
        try {
            const response = await adminApi.getAnalyticsDrivers()
            setPerformance(response.data.data)
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
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 className="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">Fleet Performance</h1>
                        <p className="mt-1 text-sm text-zinc-500 font-medium italic">Driver productivity, satisfaction metrics, and leaderboard</p>
                    </div>
                </div>

                {/* Hero Stat Cards */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                        <div className="flex items-center gap-4 mb-4">
                            <div className="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-2xl text-blue-600">
                                <Users className="h-6 w-6" />
                            </div>
                            <span className="text-xs font-black text-zinc-400 uppercase tracking-widest">Retention</span>
                        </div>
                        <h3 className="text-sm font-bold text-zinc-500 uppercase">Active Drivers</h3>
                        <p className="text-4xl font-black text-zinc-900 dark:text-white mt-1">98.2%</p>
                    </div>

                    <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm border-b-4 border-b-amber-500">
                        <div className="flex items-center gap-4 mb-4">
                            <div className="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-2xl text-amber-600">
                                <Star className="h-6 w-6" />
                            </div>
                            <span className="text-xs font-black text-zinc-400 uppercase tracking-widest">Satisfaction</span>
                        </div>
                        <h3 className="text-sm font-bold text-zinc-500 uppercase">Avg Fleet Rating</h3>
                        <p className="text-4xl font-black text-zinc-900 dark:text-white mt-1">4.85 ★</p>
                    </div>

                    <div className="bg-zinc-900 p-8 rounded-[2rem] border border-zinc-800 text-white shadow-xl relative overflow-hidden">
                        <div className="absolute top-0 right-0 p-8 opacity-10">
                            <ShieldCheck className="h-24 w-24" />
                        </div>
                        <div className="flex items-center gap-4 mb-4">
                            <div className="p-3 bg-emerald-500/20 rounded-2xl text-emerald-400">
                                <Briefcase className="h-6 w-6" />
                            </div>
                            <span className="text-xs font-black text-emerald-500 uppercase tracking-widest">Operations</span>
                        </div>
                        <h3 className="text-sm font-bold text-zinc-400 uppercase">KYC Compliance</h3>
                        <p className="text-4xl font-black mt-1">100%</p>
                    </div>
                </div>

                {/* Top Drivers Leaderboard */}
                <div className="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                    <div className="p-8 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                        <div>
                            <h2 className="text-2xl font-black text-zinc-900 dark:text-white">Top 10 Performance Leaders</h2>
                            <p className="text-sm text-zinc-500">Global ranking based on trip volume and service quality</p>
                        </div>
                        <Award className="h-8 w-8 text-amber-500" />
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="bg-zinc-50 dark:bg-zinc-800/50">
                                    <th className="px-8 py-4 text-xs font-black text-zinc-500 uppercase tracking-widest">Rank</th>
                                    <th className="px-8 py-4 text-xs font-black text-zinc-500 uppercase tracking-widest">Driver</th>
                                    <th className="px-8 py-4 text-xs font-black text-zinc-500 uppercase tracking-widest">Vehicle</th>
                                    <th className="px-8 py-4 text-xs font-black text-zinc-500 uppercase tracking-widest text-center">Trips</th>
                                    <th className="px-8 py-4 text-xs font-black text-zinc-500 uppercase tracking-widest text-right">Avg Fare</th>
                                    <th className="px-8 py-4 text-xs font-black text-zinc-500 uppercase tracking-widest text-right">Performance</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-zinc-100 dark:divide-zinc-800">
                                {performance.map((driver, index) => (
                                    <tr key={index} className="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors group">
                                        <td className="px-8 py-6">
                                            <div className="flex items-center justify-center h-8 w-8 rounded-full bg-zinc-100 dark:bg-zinc-800 text-sm font-black text-zinc-600">
                                                #{index + 1}
                                            </div>
                                        </td>
                                        <td className="px-8 py-6">
                                            <div className="flex items-center gap-3">
                                                <div className="h-10 w-10 rounded-2xl bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 font-bold">
                                                    {driver.driver_name.charAt(0)}
                                                </div>
                                                <div>
                                                    <p className="text-sm font-black text-zinc-900 dark:text-white uppercase">{driver.driver_name}</p>
                                                    <p className="text-xs text-zinc-500">Verified Partner</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-8 py-6">
                                            <span className="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-full text-[10px] font-black uppercase text-zinc-500 tracking-tighter">
                                                {driver.vehicle_type}
                                            </span>
                                        </td>
                                        <td className="px-8 py-6 text-center font-black text-zinc-900 dark:text-white">
                                            {driver.total_trips}
                                        </td>
                                        <td className="px-8 py-6 text-right font-bold text-emerald-600">
                                            GHS {parseFloat(driver.avg_fare).toFixed(2)}
                                        </td>
                                        <td className="px-8 py-6">
                                            <div className="flex items-center justify-end gap-2 text-emerald-600 font-black text-xs">
                                                <TrendingUp className="h-3 w-3" />
                                                ELITE
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}
