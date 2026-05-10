'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { 
    Zap, 
    Plus, 
    Search, 
    Trash2, 
    Settings2, 
    ArrowRight, 
    AlertCircle,
    Save,
    TrendingUp,
    Users,
    Car
} from 'lucide-react'
import dynamic from 'next/dynamic'

const SurgeMap = dynamic(() => import('@/components/SurgeControlMap'), { ssr: false })

export default function SurgeLabPage() {
    const [zones, setZones] = useState<any[]>([])
    const [loading, setLoading] = useState(true)
    const [selectedZoneId, setSelectedZoneId] = useState<string | null>(null)
    const [isCreating, setIsCreating] = useState(false)
    
    const [newZoneData, setNewZoneData] = useState({
        name: '',
        center_lat: 5.6037,
        center_lng: -0.1870,
        radius_km: 2.0,
        min_multiplier: 1.0,
        max_multiplier: 3.0
    })

    const fetchZones = async () => {
        try {
            const res = await adminApi.getSurgeZones()
            setZones(res.data.data)
        } catch (err) {
            console.error('Failed to fetch surge zones:', err)
        } finally {
            setLoading(false)
        }
    }

    useEffect(() => {
        fetchZones()
    }, [])

    const handleCreateZone = async () => {
        try {
            await adminApi.createSurgeZone(newZoneData)
            setIsCreating(false)
            fetchZones()
        } catch (err) {
            console.error('Failed to create zone:', err)
        }
    }

    const selectedZone = zones.find(z => z.id === selectedZoneId)

    return (
        <DashboardLayout>
            <div className="flex flex-col h-full gap-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-zinc-900 dark:text-white">Surge Intelligence Lab</h1>
                        <p className="text-sm text-zinc-500">Manage real-time dynamic pricing and demand zones</p>
                    </div>
                    <button 
                        onClick={() => setIsCreating(true)}
                        className="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-lg active:scale-95"
                    >
                        <Plus className="h-4 w-4" />
                        NEW PRICE ZONE
                    </button>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 min-h-[600px]">
                    {/* Sidebar: Zone List & Config */}
                    <div className="space-y-6 flex flex-col">
                        {/* Zone List */}
                        <div className="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden flex-1">
                            <div className="p-4 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/20">
                                <h3 className="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Active Policy Zones</h3>
                            </div>
                            <div className="overflow-y-auto max-h-[400px] p-2 space-y-1">
                                {zones.map(zone => (
                                    <button
                                        key={zone.id}
                                        onClick={() => {
                                            setSelectedZoneId(zone.id)
                                            setIsCreating(false)
                                        }}
                                        className={`w-full flex items-center justify-between p-4 rounded-xl text-left transition-all ${
                                            selectedZoneId === zone.id 
                                            ? 'bg-blue-50 border-blue-200 ring-1 ring-blue-500/20 dark:bg-blue-900/10' 
                                            : 'hover:bg-zinc-50 dark:hover:bg-zinc-800'
                                        }`}
                                    >
                                        <div>
                                            <p className="text-sm font-bold text-zinc-900 dark:text-white">{zone.name}</p>
                                            <p className="text-[10px] text-zinc-400 capitalize">{zone.radius_km}km Radius • {zone.rules?.length || 0} Rules</p>
                                        </div>
                                        <div className={`px-2 py-1 rounded-lg text-xs font-black ${
                                            zone.current_multiplier > 1.0 ? 'bg-amber-100 text-amber-700' : 'bg-zinc-100 text-zinc-500'
                                        }`}>
                                            {zone.current_multiplier}x
                                        </div>
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* Zone Detail / Editor */}
                        <div className="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 flex-shrink-0">
                            {isCreating ? (
                                <div className="space-y-4">
                                    <h3 className="text-sm font-bold text-zinc-900 dark:text-white mb-4">Create New Zone</h3>
                                    <div className="space-y-3">
                                        <div>
                                            <label className="text-[10px] font-bold text-zinc-400 uppercase">Zone Name</label>
                                            <input 
                                                type="text" 
                                                value={newZoneData.name}
                                                onChange={e => setNewZoneData({...newZoneData, name: e.target.value})}
                                                placeholder="e.g. Accra Mall Peak" 
                                                className="w-full mt-1 bg-zinc-50 dark:bg-zinc-800 border-none rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500"
                                            />
                                        </div>
                                        <div className="grid grid-cols-2 gap-3">
                                            <div>
                                                <label className="text-[10px] font-bold text-zinc-400 uppercase">Radius (KM)</label>
                                                <input 
                                                    type="number" 
                                                    step="0.5"
                                                    value={newZoneData.radius_km}
                                                    onChange={e => setNewZoneData({...newZoneData, radius_km: parseFloat(e.target.value)})}
                                                    className="w-full mt-1 bg-zinc-50 dark:bg-zinc-800 border-none rounded-lg p-2 text-sm"
                                                />
                                            </div>
                                            <div>
                                                <label className="text-[10px] font-bold text-zinc-400 uppercase">Max Multiplier</label>
                                                <input 
                                                    type="number" 
                                                    step="0.1"
                                                    value={newZoneData.max_multiplier}
                                                    onChange={e => setNewZoneData({...newZoneData, max_multiplier: parseFloat(e.target.value)})}
                                                    className="w-full mt-1 bg-zinc-50 dark:bg-zinc-800 border-none rounded-lg p-2 text-sm"
                                                />
                                            </div>
                                        </div>
                                        <div className="flex gap-2 pt-2">
                                            <button 
                                                onClick={handleCreateZone}
                                                className="flex-1 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 py-2 rounded-xl text-sm font-bold"
                                            >
                                                SAVE ZONE
                                            </button>
                                            <button 
                                                onClick={() => setIsCreating(false)}
                                                className="px-4 py-2 rounded-xl text-sm font-bold border border-zinc-200"
                                            >
                                                CANCEL
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ) : selectedZone ? (
                                <div className="space-y-4">
                                    <div className="flex items-center justify-between mb-4">
                                        <h3 className="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-tight">{selectedZone.name}</h3>
                                        <Zap className={`h-4 w-4 ${selectedZone.current_multiplier > 1.0 ? 'text-amber-500' : 'text-zinc-300'}`} />
                                    </div>
                                    
                                    <div className="grid grid-cols-2 gap-3">
                                        <div className="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl">
                                            <p className="text-[10px] font-bold text-zinc-400 uppercase">Multiplier Range</p>
                                            <p className="text-sm font-black mt-1">{selectedZone.min_multiplier}x - {selectedZone.max_multiplier}x</p>
                                        </div>
                                        <div className="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl">
                                            <p className="text-[10px] font-bold text-zinc-400 uppercase">Active Rules</p>
                                            <p className="text-sm font-black mt-1">{selectedZone.rules?.length || 0}</p>
                                        </div>
                                    </div>

                                    <div className="space-y-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                                        <p className="text-[10px] font-bold text-zinc-400 uppercase mb-2">Rule Configuration</p>
                                        {selectedZone.rules?.map((rule: any, i: number) => (
                                            <div key={i} className="flex items-center justify-between p-2 rounded-lg border border-dashed border-zinc-200 dark:border-zinc-700">
                                                <div className="flex items-center gap-2 overflow-hidden">
                                                    <Users className="h-3 w-3 text-zinc-400 flex-shrink-0" />
                                                    <span className="text-xs font-bold whitespace-nowrap">{rule.demand_threshold}+ Demand</span>
                                                </div>
                                                <ArrowRight className="h-3 w-3 text-zinc-300 flex-shrink-0" />
                                                <div className="flex items-center gap-2 overflow-hidden">
                                                    <Car className="h-3 w-3 text-zinc-400 flex-shrink-0" />
                                                    <span className="text-xs font-bold whitespace-nowrap">{rule.supply_threshold}- Drivers</span>
                                                </div>
                                                <span className="text-xs font-black text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded ml-2">
                                                    {rule.multiplier}x
                                                </span>
                                            </div>
                                        ))}
                                        <button className="w-full flex items-center justify-center gap-2 py-2 text-xs font-bold text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/10 rounded-lg transition-colors">
                                            <Settings2 className="h-3 w-3" />
                                            MANAGE RULE MATRIX
                                        </button>
                                    </div>
                                </div>
                            ) : (
                                <div className="flex flex-col items-center justify-center py-10 text-zinc-400 text-center">
                                    <TrendingUp className="h-10 w-10 mb-2 opacity-10" />
                                    <p className="text-xs">Select a zone to view policy details or edit rules</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Map Visualizer */}
                    <div className="lg:col-span-2 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden relative shadow-inner shadow-zinc-100 dark:shadow-black/20">
                        <SurgeMap 
                            zones={zones} 
                            selectedZoneId={selectedZoneId || undefined} 
                        />
                        
                        {/* Map HUD */}
                        <div className="absolute top-4 left-4 z-[1000] p-4 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md rounded-xl border border-white/20 shadow-xl pointer-events-none">
                            <div className="flex items-center gap-3">
                                <div className="h-3 w-3 rounded-full bg-emerald-500 animate-pulse"></div>
                                <span className="text-[10px] font-black tracking-widest uppercase">Intelligence Layer: Supply-Demand Correlation</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}
