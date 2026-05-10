'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { 
    MapPin, 
    Plus, 
    Trash2, 
    Zap, 
    Settings2, 
    TrendingUp, 
    Users, 
    Car,
    AlertCircle,
    CheckCircle2
} from 'lucide-react'
import dynamic from 'next/dynamic'

// Dynamically import the map to avoid SSR issues
const SurgeMap = dynamic(() => import('@/components/SurgeControlMap'), {
    ssr: false,
    loading: () => <div className="h-full w-full bg-zinc-100 dark:bg-zinc-800 animate-pulse rounded-xl" />
})

export default function SurgeManagementPage() {
    const [zones, setZones] = useState<any[]>([])
    const [loading, setLoading] = useState(true)
    const [showCreateModal, setShowCreateModal] = useState(false)
    const [selectedZone, setSelectedZone] = useState<any>(null)
    const [newZone, setNewZone] = useState({
        name: '',
        center_lat: 5.6037,
        center_lng: -0.1870,
        radius_km: 1.5,
        min_multiplier: 1.0,
        max_multiplier: 4.0
    })

    const fetchZones = async () => {
        setLoading(true)
        try {
            const res = await adminApi.getSurgeZones()
            setZones(res.data)
        } catch (err) {
            console.error(err)
        }
        setLoading(false)
    }

    useEffect(() => { fetchZones() }, [])

    const handleCreateZone = async (e: React.FormEvent) => {
        e.preventDefault()
        try {
            await adminApi.createSurgeZone(newZone)
            setShowCreateModal(false)
            fetchZones()
        } catch (err) {
            alert('Failed to create zone')
        }
    }

    const handleDeleteZone = async (id: string) => {
        if (!confirm('Are you sure you want to remove this surge zone?')) return
        try {
            await adminApi.deleteSurgeZone(id)
            fetchZones()
        } catch (err) {
            alert('Failed to delete zone')
        }
    }

    return (
        <DashboardLayout>
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-zinc-900 dark:text-white">Surge Command Center</h1>
                        <p className="mt-1 text-sm text-zinc-500">Manage spatial dynamic pricing and market demand zones</p>
                    </div>
                    <button
                        onClick={() => setShowCreateModal(true)}
                        className="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-lg shadow-blue-600/20"
                    >
                        <Plus className="h-4 w-4" />
                        Create New Zone
                    </button>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-210px)]">
                    {/* Zone List */}
                    <div className="lg:col-span-1 flex flex-col gap-4 overflow-y-auto pr-2 custom-scrollbar">
                        {loading ? (
                            Array.from({ length: 3 }).map((_, i) => (
                                <div key={i} className="h-32 bg-zinc-100 dark:bg-zinc-800 animate-pulse rounded-xl" />
                            ))
                        ) : zones.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-12 bg-white dark:bg-zinc-900 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800">
                                <MapPin className="h-10 w-10 text-zinc-300 mb-3" />
                                <p className="text-zinc-500 text-sm">No active surge zones</p>
                            </div>
                        ) : (
                            zones.map((zone) => (
                                <div 
                                    key={zone.id}
                                    onClick={() => setSelectedZone(zone)}
                                    className={`p-5 rounded-2xl border transition-all cursor-pointer group ${
                                        selectedZone?.id === zone.id 
                                            ? 'bg-blue-50 border-blue-200 dark:bg-blue-900/10 dark:border-blue-800' 
                                            : 'bg-white border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800 hover:border-blue-300'
                                    }`}
                                >
                                    <div className="flex items-start justify-between mb-3">
                                        <div className="flex items-center gap-3">
                                            <div className={`p-2 rounded-lg ${zone.current_multiplier > 1.0 ? 'bg-amber-100 text-amber-600' : 'bg-zinc-100 text-zinc-500'}`}>
                                                <Zap className="h-4 w-4" />
                                            </div>
                                            <div>
                                                <h3 className="text-sm font-bold text-zinc-900 dark:text-white capitalize">{zone.name}</h3>
                                                <p className="text-[10px] text-zinc-500 uppercase tracking-widest">{zone.radius_km}km Radius</p>
                                            </div>
                                        </div>
                                        <button 
                                            onClick={(e) => { e.stopPropagation(); handleDeleteZone(zone.id); }}
                                            className="opacity-0 group-hover:opacity-100 p-1.5 text-zinc-400 hover:text-red-500 transition-all"
                                        >
                                            <Trash2 className="h-3.5 w-3.5" />
                                        </button>
                                    </div>

                                    <div className="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-800">
                                        <div className="flex flex-col">
                                            <span className="text-[10px] font-bold text-zinc-400 uppercase tracking-tighter">Current Multiplier</span>
                                            <span className={`text-lg font-black ${zone.current_multiplier > 1.0 ? 'text-blue-600' : 'text-zinc-400'}`}>
                                                {zone.current_multiplier}x
                                            </span>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <div className="h-1.5 w-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)] animate-pulse" />
                                            <span className="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Running</span>
                                        </div>
                                    </div>
                                </div>
                            ))
                        )}
                    </div>

                    {/* Integrated Map View */}
                    <div className="lg:col-span-2 relative rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden bg-zinc-50 dark:bg-zinc-950">
                        <SurgeMap zones={zones} selectedZoneId={selectedZone?.id} />
                        
                        {/* Selected Zone Overlay Card */}
                        {selectedZone && (
                            <div className="absolute top-4 right-4 w-72 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-2xl z-20">
                                <div className="flex items-center justify-between mb-4">
                                    <h4 className="text-sm font-bold dark:text-white">Active Rules</h4>
                                    <span className="text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full uppercase italic">Live Recalc</span>
                                </div>
                                <div className="space-y-3">
                                    {selectedZone.rules?.length > 0 ? (
                                        selectedZone.rules.map((rule: any, i: number) => (
                                            <div key={i} className="flex items-center justify-between p-2 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-100 dark:border-zinc-700">
                                                <div className="flex flex-col">
                                                    <span className="text-[10px] text-zinc-400 flex items-center gap-1">
                                                        <Users className="h-2.5 w-2.5" /> D:{rule.demand_threshold} <Car className="h-2.5 w-2.5 ml-1" /> S:{rule.supply_threshold}
                                                    </span>
                                                </div>
                                                <span className="text-xs font-bold text-blue-600">{rule.multiplier}x</span>
                                            </div>
                                        ))
                                    ) : (
                                        <p className="text-[10px] text-zinc-500 italic text-center py-2">No custom rules defined</p>
                                    )}
                                </div>
                                <button className="w-full mt-4 flex items-center justify-center gap-2 py-2 text-[10px] font-bold uppercase tracking-wider text-zinc-500 hover:text-blue-600 border border-dashed border-zinc-300 dark:border-zinc-700 rounded-lg transition-colors">
                                    <Plus className="h-3 w-3" />
                                    Define Rules
                                </button>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Create Zone Modal */}
            {showCreateModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/60 backdrop-blur-sm p-4">
                    <div className="w-full max-w-md bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                        <div className="p-6 border-b border-zinc-100 dark:border-zinc-800">
                            <h2 className="text-xl font-bold dark:text-white">Deploy Surge Zone</h2>
                            <p className="text-sm text-zinc-500 mt-1">Configure spatial pricing parameters</p>
                        </div>
                        <form onSubmit={handleCreateZone} className="p-6 space-y-4">
                            <div>
                                <label className="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-1.5">Zone Name</label>
                                <input 
                                    type="text" 
                                    required
                                    value={newZone.name}
                                    onChange={e => setNewZone({...newZone, name: e.target.value})}
                                    placeholder="e.g. Kotoka Airport Peak"
                                    className="w-full px-4 py-2 bg-zinc-50 dark:bg-zinc-800 border-none rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-zinc-900 dark:text-white"
                                />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-1.5">Radius (KM)</label>
                                    <input 
                                        type="number" step="0.1" required
                                        value={newZone.radius_km}
                                        onChange={e => setNewZone({...newZone, radius_km: parseFloat(e.target.value)})}
                                        className="w-full px-4 py-2 bg-zinc-50 dark:bg-zinc-800 border-none rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-zinc-900 dark:text-white"
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-1.5">Max Multiplier</label>
                                    <input 
                                        type="number" step="0.1" required
                                        value={newZone.max_multiplier}
                                        onChange={e => setNewZone({...newZone, max_multiplier: parseFloat(e.target.value)})}
                                        className="w-full px-4 py-2 bg-zinc-50 dark:bg-zinc-800 border-none rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-zinc-900 dark:text-white"
                                    />
                                </div>
                            </div>
                            <div className="p-4 bg-blue-50 dark:bg-blue-900/10 rounded-xl border border-blue-100 dark:border-blue-900/30 flex items-start gap-3">
                                <AlertCircle className="h-5 w-5 text-blue-600 shrink-0" />
                                <p className="text-[10px] text-blue-800 dark:text-blue-400 leading-relaxed font-medium">
                                    The zone will be created at your current map center. You can click and drag on the map (coming soon) to reposition. Multipliers are recalculated every 2 minutes.
                                </p>
                            </div>
                            <div className="flex items-center gap-3 pt-4">
                                <button
                                    type="button"
                                    onClick={() => setShowCreateModal(false)}
                                    className="flex-1 px-4 py-2.5 text-sm font-bold text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    className="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors shadow-lg shadow-blue-600/20"
                                >
                                    Deploy Zone
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </DashboardLayout>
    )
}
