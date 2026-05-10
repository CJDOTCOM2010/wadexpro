'use client'

import React, { useEffect, useState } from 'react'
import { adminApi } from '@/lib/axios'
import { 
    BarChart3, 
    TrendingUp, 
    Users, 
    MousePointer2, 
    Map as MapIcon, 
    Activity, 
    Navigation, 
    ArrowUpRight, 
    ArrowDownRight, 
    Filter, 
    Download, 
    Calendar,
    Trophy,
    Briefcase,
    Zap
} from 'lucide-react'
import { 
    AreaChart, 
    Area, 
    XAxis, 
    YAxis, 
    CartesianGrid, 
    Tooltip, 
    ResponsiveContainer, 
    BarChart, 
    Bar, 
    Cell,
    PieChart,
    Pie
} from 'recharts'
import dynamic from 'next/dynamic'

// Dynamically import Leaflet with no SSR
const MapContainer = dynamic(() => import('react-leaflet').then(mod => mod.MapContainer), { ssr: false })
const TileLayer = dynamic(() => import('react-leaflet').then(mod => mod.TileLayer), { ssr: false })
const HeatmapLayer = dynamic(() => import('./components/heatmap-layer'), { ssr: false })

export default function AnalyticsIntelligence() {
    const [overview, setOverview] = useState<any>(null)
    const [trends, setTrends] = useState<any[]>([])
    const [leaderboards, setLeaderboards] = useState<any>(null)
    const [ratio, setRatio] = useState<any>(null)
    const [heatmapData, setHeatmapData] = useState<any[]>([])
    const [loading, setLoading] = useState(true)

    useEffect(() => {
        loadData()
        const interval = setInterval(loadData, 60000) // Refresh every minute
        return () => clearInterval(interval)
    }, [])

    const loadData = async () => {
        try {
            const [ovRes, trRes, lbRes, rtRes, hmRes] = await Promise.all([
                adminApi.getAnalyticsOverview(),
                adminApi.getAnalyticsTrends(),
                adminApi.getLeaderboards(),
                adminApi.getSupplyDemandRatio(),
                adminApi.getDemandHeatmap()
            ])
            
            setOverview(ovRes.data.data)
            setTrends(trRes.data.data)
            setLeaderboards(lbRes.data.data)
            setRatio(rtRes.data.data)
            setHeatmapData(hmRes.data.data)
        } catch (error) {
            console.error('Failed to load intelligence data:', error)
        } finally {
            setLoading(false)
        }
    }

    return (
        <div className="min-h-screen bg-[#F8FAFC]">
            {/* Intel Header */}
            <div className="bg-[#0F172A] text-white p-12 relative overflow-hidden">
                <div className="absolute right-0 top-0 w-1/2 h-full bg-gradient-to-l from-blue-600/10 to-transparent skew-x-12 transform translate-x-32"></div>
                
                <div className="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                    <div>
                        <div className="flex items-center gap-3 mb-3">
                            <div className="bg-blue-600 p-2.5 rounded-2xl">
                                <Zap size={28} />
                            </div>
                            <h1 className="text-4xl font-black tracking-tight">LOGISTICS INTELLIGENCE</h1>
                        </div>
                        <p className="text-slate-400 font-medium max-w-xl text-lg">
                            Advanced fleet aggregation, predictive ROI analytics, and geographic demand heatmaps for the WADEXPRO global network.
                        </p>
                    </div>

                    <div className="flex gap-4">
                        <button className="flex items-center gap-2 bg-slate-800 hover:bg-slate-700 px-6 py-3 rounded-2xl font-bold transition-all text-sm border border-slate-700">
                            <Calendar size={18} />
                            Last 30 Days
                        </button>
                        <button className="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-2xl font-bold transition-all text-sm shadow-xl shadow-blue-600/20">
                            <Download size={18} />
                            Export BI Report
                        </button>
                    </div>
                </div>

                {/* Pulse Metrics */}
                {overview && (
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12 relative z-10">
                        {Object.entries(overview.metrics).map(([key, metric]: [string, any]) => (
                            <div key={key} className="bg-white/5 backdrop-blur-md border border-white/10 p-8 rounded-[32px] hover:bg-white/10 transition-all group">
                                <div className="flex justify-between items-start mb-4">
                                    <p className="text-xs font-black text-slate-400 uppercase tracking-widest">{metric.label}</p>
                                    <div className="p-2 bg-blue-500/20 rounded-xl text-blue-400 group-hover:scale-110 transition-transform">
                                        {key === 'revenue' && <TrendingUp size={18} />}
                                        {key === 'rides' && <Activity size={18} />}
                                        {key === 'fleet' && <Users size={18} />}
                                    </div>
                                </div>
                                <div className="flex items-end gap-3">
                                    <h3 className="text-4xl font-black text-white tracking-tight">
                                        {key === 'revenue' ? `GHS ${metric.current.toLocaleString()}` : 
                                         key === 'fleet' ? `${metric.utilization}%` : metric.active}
                                    </h3>
                                    {metric.growth && (
                                        <div className="flex items-center gap-1 text-green-400 text-xs font-black mb-1 p-1 px-2 bg-green-400/10 rounded-full">
                                            <ArrowUpRight size={14} />
                                            {metric.growth}%
                                        </div>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <main className="max-w-[1600px] mx-auto p-12 -mt-8 relative z-20">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    {/* Main Analytics Strip */}
                    <div className="lg:col-span-8 space-y-8">
                        {/* 1. Revenue Trends */}
                        <div className="bg-white rounded-[40px] border border-slate-200 p-10 shadow-2xl shadow-slate-200/50">
                            <div className="flex justify-between items-center mb-8">
                                <div>
                                    <h3 className="text-2xl font-black text-slate-800 tracking-tight">Financial Performance</h3>
                                    <p className="text-slate-400 text-sm font-medium">Aggregated gross revenue trends vs. historical projections.</p>
                                </div>
                                <div className="flex items-center gap-2 text-xs font-black text-blue-600 bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100 uppercase tracking-widest">
                                    <Zap size={14} /> LIVE ANALYTICS
                                </div>
                            </div>
                            <div className="h-[400px] w-full">
                                <ResponsiveContainer width="100%" height="100%">
                                    <AreaChart data={trends}>
                                        <defs>
                                            <linearGradient id="colorRevenue" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="5%" stopColor="#2563eb" stopOpacity={0.1}/>
                                                <stop offset="95%" stopColor="#2563eb" stopOpacity={0}/>
                                            </linearGradient>
                                        </defs>
                                        <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" />
                                        <XAxis 
                                            dataKey="date" 
                                            axisLine={false} 
                                            tickLine={false} 
                                            tick={{fill: '#94a3b8', fontWeight: 900, fontSize: 10}}
                                            tickFormatter={(val) => new Date(val).toLocaleDateString(undefined, {month: 'short', day: 'numeric'})}
                                        />
                                        <YAxis hide />
                                        <Tooltip 
                                            contentStyle={{borderRadius: '24px', border: 'none', boxShadow: '0 20px 25px -5px rgb(0 0 0 / 0.1)', padding: '16px'}}
                                            labelStyle={{fontWeight: 900, color: '#0f172a', marginBottom: '4px'}}
                                        />
                                        <Area 
                                            type="monotone" 
                                            dataKey="revenue" 
                                            stroke="#2563eb" 
                                            strokeWidth={4}
                                            fillOpacity={1} 
                                            fill="url(#colorRevenue)" 
                                            animationDuration={2000}
                                        />
                                    </AreaChart>
                                </ResponsiveContainer>
                            </div>
                        </div>

                        {/* 2. Geographic Heatmap Dashboard */}
                        <div className="bg-white rounded-[40px] border border-slate-200 overflow-hidden shadow-2xl shadow-slate-200/50">
                            <div className="p-10 border-b border-slate-100 flex justify-between items-center">
                                <div>
                                    <h3 className="text-2xl font-black text-slate-800 tracking-tight">Geographic Hub Density</h3>
                                    <p className="text-slate-400 text-sm font-medium">Real-time demand clusters and supply distribution heatmaps.</p>
                                </div>
                                <button className="p-4 bg-slate-50 text-slate-400 rounded-3xl hover:bg-slate-100 transition-all border border-slate-200 shadow-sm active:scale-95">
                                    <MapIcon size={24} />
                                </button>
                            </div>
                            <div className="h-[500px] relative">
                                {typeof window !== 'undefined' && (
                                    <MapContainer 
                                        center={[5.6037, -0.1870] as any} 
                                        zoom={13} 
                                        style={{ height: '100%', width: '100%' }}
                                        className="grayscale-[0.3]"
                                    >
                                        <TileLayer url="https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png" />
                                        <HeatmapLayer points={heatmapData} />
                                    </MapContainer>
                                )}
                                <div className="absolute bottom-8 left-8 right-8 bg-white/80 backdrop-blur-lg border border-white p-6 rounded-3xl shadow-2xl z-[1000] flex justify-between items-center">
                                    <div className="flex gap-8">
                                        <div>
                                            <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Density Peak</p>
                                            <p className="text-lg font-black text-slate-800">84% Capacity</p>
                                        </div>
                                        <div>
                                            <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Recommended Surge</p>
                                            <p className="text-lg font-black text-blue-600">1.4x Mult</p>
                                        </div>
                                    </div>
                                    <div className="flex gap-2">
                                        <div className="px-3 py-1 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-[10px] font-black rounded-lg">DEMAND ACTIVE</div>
                                        <div className="px-3 py-1 bg-white border border-slate-200 text-slate-400 text-[10px] font-black rounded-lg uppercase">Supply View</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Performance Slabs */}
                    <div className="lg:col-span-4 space-y-8">
                        {/* 1. Demand/Supply Pulse */}
                        {ratio && (
                            <div className="bg-white p-10 rounded-[40px] border border-slate-200 shadow-xl shadow-slate-200/50">
                                <h4 className="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Market Pulse Ratio</h4>
                                <div className="flex items-center justify-between gap-8 mb-8">
                                    <div className="text-center flex-1">
                                        <div className="p-4 bg-orange-50 text-orange-600 rounded-[28px] mb-3 border border-orange-100 flex items-center justify-center mx-auto w-16 h-16 shadow-lg shadow-orange-100/50">
                                            <MousePointer2 size={24} />
                                        </div>
                                        <p className="text-xs font-bold text-slate-500">Demand</p>
                                        <h5 className="text-2xl font-black text-slate-800">{ratio.demand} Req</h5>
                                    </div>
                                    <div className="text-2xl font-black text-slate-200">{ratio.ratio}:1</div>
                                    <div className="text-center flex-1">
                                        <div className="p-4 bg-blue-50 text-blue-600 rounded-[28px] mb-3 border border-blue-100 flex items-center justify-center mx-auto w-16 h-16 shadow-lg shadow-blue-100/50">
                                            <Navigation size={24} />
                                        </div>
                                        <p className="text-xs font-bold text-slate-500">Supply</p>
                                        <h5 className="text-2xl font-black text-slate-800">{ratio.supply} Avl</h5>
                                    </div>
                                </div>
                                <div className="p-5 bg-slate-50 rounded-3xl border border-slate-100">
                                    <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Intelligence Note</p>
                                    <p className="text-xs text-slate-600 font-medium leading-relaxed">
                                        Market load is {ratio.ratio > 2 ? 'critically high' : 'stable'}. Recommend increasing global multipliers by 0.2x in peak zones.
                                    </p>
                                </div>
                            </div>
                        )}

                        {/* 2. Driver Leaderboard */}
                        {leaderboards && (
                            <div className="bg-white p-10 rounded-[40px] border border-slate-200 shadow-xl shadow-slate-200/50">
                                <div className="flex justify-between items-center mb-8">
                                    <h4 className="text-xs font-black text-slate-400 uppercase tracking-widest">WADEX Elites</h4>
                                    <Trophy size={20} className="text-gold-500 text-[#EAB308]" />
                                </div>
                                <div className="space-y-6">
                                    {leaderboards.drivers.map((driver: any, idx: number) => (
                                        <div key={driver.id} className="flex items-center gap-4 group cursor-pointer">
                                            <div className="relative">
                                                <div className="w-12 h-12 rounded-2xl bg-slate-100 border-2 border-white shadow-sm flex items-center justify-center font-black text-slate-500 group-hover:scale-110 transition-transform">
                                                    {driver.name[0]}
                                                </div>
                                                <div className="absolute -top-1 -right-1 w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center text-[10px] font-bold text-white border-2 border-white">
                                                    {idx + 1}
                                                </div>
                                            </div>
                                            <div className="flex-1">
                                                <h5 className="text-sm font-black text-slate-800 uppercase italic tracking-tight">{driver.name}</h5>
                                                <p className="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{driver.rides} Trips</p>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-xs font-black text-blue-600">{driver.score}%</p>
                                                <p className="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Intel Score</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                                <button className="w-full mt-8 py-4 bg-slate-50 hover:bg-slate-100 text-slate-400 font-black text-[10px] uppercase tracking-widest rounded-3xl transition-all border border-slate-100 flex items-center justify-center gap-2 group italic">
                                    View Full Fleet ROI
                                    <ChevronRight size={14} className="group-hover:translate-x-1 transition-transform" />
                                </button>
                            </div>
                        )}

                        {/* 3. B2B Intelligence */}
                        {leaderboards && (
                            <div className="bg-[#0F172A] p-10 rounded-[40px] shadow-2xl shadow-blue-900/20 text-white relative overflow-hidden">
                                <div className="absolute right-0 top-0 w-32 h-32 bg-blue-500/10 blur-[80px]"></div>
                                <div className="flex justify-between items-center mb-8 relative z-10">
                                    <h4 className="text-xs font-black text-slate-500 uppercase tracking-widest">Enterprise ROI</h4>
                                    <Briefcase size={20} className="text-blue-500" />
                                </div>
                                <div className="space-y-5 relative z-10">
                                    {leaderboards.organizations.map((org: any) => (
                                        <div key={org.name} className="flex justify-between items-end border-b border-white/5 pb-4">
                                            <div>
                                                <h5 className="text-sm font-black uppercase tracking-tight">{org.name}</h5>
                                                <p className="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{org.trips} Corporate Trips</p>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-sm font-black text-blue-400">GHS {Math.round(org.trips * 12.5).toLocaleString()}</p>
                                                <p className="text-[10px] text-slate-500 font-bold uppercase tracking-widest italic">Est. Savings</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </main>
        </div>
    )
}

function ChevronRight({ size, className }: { size: number, className?: string }) {
    return <ArrowUpRight size={size} className={className} />
}
