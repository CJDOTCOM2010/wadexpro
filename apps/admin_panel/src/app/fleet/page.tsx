'use client'

import React, { useEffect, useState } from 'react'
import { adminApi } from '@/lib/axios'
import { 
    Truck, 
    Zap, 
    Search, 
    Filter, 
    Plus, 
    MoreHorizontal, 
    Settings, 
    Activity, 
    ShieldCheck, 
    AlertTriangle,
    BarChart3,
    Users,
    Circle
} from 'lucide-react'

export default function FleetPage() {
    const [vehicles, setVehicles] = useState<any[]>([])
    const [overview, setOverview] = useState<any>(null)
    const [loading, setLoading] = useState(true)

    const fetchData = async () => {
        setLoading(true)
        try {
            const [overviewRes, vehiclesRes] = await Promise.all([
                adminApi.getFleetOverview(),
                adminApi.getVehicles()
            ])
            setOverview(overviewRes.data.data)
            setVehicles(vehiclesRes.data.data)
        } catch (error) {
            console.error('Failed to fetch fleet data:', error)
        } finally {
            setLoading(false)
        }
    }

    useEffect(() => {
        fetchData()
    }, [])

    return (
        <div className="p-8 space-y-8 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
            {/* Header */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-3xl font-black tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <Truck className="w-8 h-8 text-blue-600" />
                        Fleet Hub
                    </h1>
                    <p className="text-zinc-500 dark:text-zinc-400 mt-1 uppercase text-xs font-black tracking-widest">
                        Asset Management & Capacity Planning
                    </p>
                </div>
                <div className="flex items-center gap-3">
                    <button className="flex items-center gap-2 px-4 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 transition-all">
                        <Settings className="w-4 h-4" />
                        Fleet Rules
                    </button>
                    <button className="flex items-center gap-2 px-5 py-2.5 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl text-sm font-black shadow-xl hover:scale-[1.02] transition-all">
                        <Plus className="w-4 h-4" />
                        ADD ASSET
                    </button>
                </div>
            </div>

            {/* Metrics Overview */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div className="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                    <div className="flex justify-between items-start mb-4">
                        <div className="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                            <Activity className="w-5 h-5 text-blue-600" />
                        </div>
                        <span className="text-[10px] font-black text-blue-600 bg-blue-50 dark:bg-blue-900/20 px-2 py-0.5 rounded-full uppercase">Live</span>
                    </div>
                    <p className="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Utilization</p>
                    <h2 className="text-2xl font-black mt-1 text-zinc-900 dark:text-white">{(overview?.utilization_rate * 100 || 0).toFixed(0)}%</h2>
                    <div className="mt-4 w-full bg-zinc-100 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden">
                        <div className="bg-blue-600 h-full w-[85%]" />
                    </div>
                </div>

                <div className="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                    <div className="flex justify-between items-start mb-4">
                        <div className="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl">
                            <Zap className="w-5 h-5 text-emerald-600" />
                        </div>
                    </div>
                    <p className="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Online Carriers</p>
                    <h2 className="text-2xl font-black mt-1 text-zinc-900 dark:text-white">{overview?.online_drivers || 0} / {overview?.total_drivers || 0}</h2>
                    <p className="text-[10px] font-bold text-emerald-500 mt-2 flex items-center gap-1 group cursor-pointer">
                        View Active Now <ChevronRight className="w-3 h-3 transition-transform group-hover:translate-x-1" />
                    </p>
                </div>

                <div className="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                    <div className="flex justify-between items-start mb-4">
                        <div className="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                            <ShieldCheck className="w-5 h-5 text-amber-600" />
                        </div>
                    </div>
                    <p className="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Verification Status</p>
                    <h2 className="text-2xl font-black mt-1 text-zinc-900 dark:text-white">92% Compliance</h2>
                    <p className="text-[10px] font-bold text-zinc-400 mt-2">8 vehicles awaiting inspection</p>
                </div>

                <div className="bg-zinc-900 p-6 rounded-3xl border border-zinc-800 shadow-xl relative overflow-hidden group">
                    <div className="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <BarChart3 className="w-20 h-20 text-blue-500 -rotate-12" />
                    </div>
                    <p className="text-[10px] font-black text-blue-400 uppercase tracking-widest">Total Fleet Value</p>
                    <h2 className="text-2xl font-black mt-1 text-white italic">OPERATIONAL</h2>
                    <p className="text-[10px] font-bold text-zinc-500 mt-4 uppercase">System Health: Optimal</p>
                </div>
            </div>

            {/* Asset Table Container */}
            <div className="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-hidden">
                <div className="p-6 border-b border-zinc-100 dark:border-zinc-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div className="relative w-full md:w-96">
                        <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" />
                        <input 
                            type="text" 
                            placeholder="Search by model, plate, or driver..."
                            className="w-full pl-11 pr-4 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all opacity-80"
                        />
                    </div>
                    <div className="flex items-center gap-2">
                        <button className="flex items-center gap-2 px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-black uppercase text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 transition-colors">
                            <Filter className="w-3.5 h-3.5" />
                            Filters
                        </button>
                    </div>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-zinc-50/50 dark:bg-zinc-800/30">
                                <th className="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Asset Details</th>
                                <th className="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Type</th>
                                <th className="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Assigned Driver</th>
                                <th className="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Condition</th>
                                <th className="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest text-right">Operational Status</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-zinc-100 dark:divide-zinc-800">
                            {vehicles.map((v) => (
                                <tr key={v.id} className="hover:bg-zinc-50 dark:hover:bg-zinc-800/20 transition-all group">
                                    <td className="px-6 py-5">
                                        <div className="flex items-center gap-4">
                                            <div className="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center p-2 group-hover:scale-110 transition-transform">
                                                <Truck className="w-full h-full text-zinc-500" />
                                            </div>
                                            <div>
                                                <p className="text-sm font-black text-zinc-900 dark:text-white uppercase italic">{v.make} {v.model}</p>
                                                <p className="text-[10px] font-mono font-bold text-zinc-400 bg-zinc-100 dark:bg-zinc-800 inline-block px-1.5 py-0.5 rounded mt-1">{v.plate_number}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-5">
                                        <span className="text-[10px] font-black uppercase text-zinc-500 tracking-tight border-b border-zinc-200 dark:border-zinc-700 pb-0.5">
                                            {v.type}
                                        </span>
                                    </td>
                                    <td className="px-6 py-5">
                                        {v.driver_name ? (
                                            <div className="flex items-center gap-3">
                                                <div className="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-[10px] font-black text-blue-600">
                                                    {v.driver_name.charAt(0)}
                                                </div>
                                                <div>
                                                    <p className="text-xs font-bold text-zinc-900 dark:text-white">{v.driver_name}</p>
                                                    <div className="flex items-center gap-1.5 mt-0.5">
                                                        <Circle className={`w-2 h-2 fill-current ${v.is_online ? 'text-emerald-500' : 'text-zinc-300'}`} />
                                                        <span className="text-[9px] font-black uppercase text-zinc-400">{v.is_online ? 'Online' : 'Offline'}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        ) : (
                                            <span className="text-[10px] font-bold text-zinc-400 italic">Unassigned</span>
                                        )}
                                    </td>
                                    <td className="px-6 py-5">
                                        <div className="flex items-center gap-2">
                                            <div className="flex-1 bg-emerald-100 dark:bg-emerald-900/20 rounded-full h-1">
                                                <div className="bg-emerald-500 h-full w-[95%]" />
                                            </div>
                                            <span className="text-[10px] font-black text-emerald-500">EXCELLENT</span>
                                        </div>
                                    </td>
                                    <td className="px-6 py-5 text-right">
                                        <div className="flex justify-end gap-2">
                                            <span className="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase rounded-full border border-emerald-100 dark:border-emerald-900/30">
                                                Active
                                            </span>
                                            <button className="p-1 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                                                <MoreHorizontal className="w-4 h-4 text-zinc-400" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                <div className="p-6 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30 flex items-center justify-between">
                    <div className="flex items-center gap-2 text-xs font-bold text-zinc-500">
                        <AlertTriangle className="w-4 h-4 text-amber-500" />
                        <span>3 vehicles due for maintenance this month</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <button className="px-4 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-[10px] font-black uppercase shadow-sm">View Full Logs</button>
                    </div>
                </div>
            </div>
        </div>
    )
}
