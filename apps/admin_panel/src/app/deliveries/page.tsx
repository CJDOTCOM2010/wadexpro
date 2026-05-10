'use client'

import React, { useEffect, useState } from 'react'
import { adminApi } from '@/lib/axios'
import { 
    Package, 
    Truck, 
    MapPin, 
    Clock, 
    CheckCircle2, 
    AlertCircle, 
    ChevronRight,
    Search,
    Filter,
    User,
    Phone,
    MoreHorizontal
} from 'lucide-react'
import { format } from 'date-fns'

export default function DeliveriesPage() {
    const [deliveries, setDeliveries] = useState<any[]>([])
    const [loading, setLoading] = useState(true)
    const [searchTerm, setSearchTerm] = useState('')
    const [selectedDelivery, setSelectedDelivery] = useState<any>(null)

    const fetchDeliveries = async () => {
        setLoading(true)
        try {
            const res = await adminApi.getDeliveries()
            setDeliveries(res.data.data.data)
        } catch (error) {
            console.error('Failed to fetch deliveries:', error)
        } finally {
            setLoading(false)
        }
    }

    useEffect(() => {
        fetchDeliveries()
    }, [])

    const getStatusStyle = (status: string) => {
        switch (status) {
            case 'pending': return 'bg-amber-100 text-amber-700 border-amber-200'
            case 'assigned': return 'bg-blue-100 text-blue-700 border-blue-200'
            case 'picked_up': return 'bg-indigo-100 text-indigo-700 border-indigo-200'
            case 'in_transit': return 'bg-purple-100 text-purple-700 border-purple-200'
            case 'delivered': return 'bg-emerald-100 text-emerald-700 border-emerald-200'
            case 'cancelled': return 'bg-rose-100 text-rose-700 border-rose-200'
            default: return 'bg-zinc-100 text-zinc-700 border-zinc-200'
        }
    }

    return (
        <div className="flex h-screen bg-zinc-50 dark:bg-zinc-950">
            {/* Delivery List Panel */}
            <div className="w-full md:w-[450px] flex flex-col border-r border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm z-10">
                <div className="p-6 border-b border-zinc-100 dark:border-zinc-800 space-y-4">
                    <h1 className="text-2xl font-black tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <Package className="w-6 h-6 text-blue-600" />
                        Logistics Orchestrator
                    </h1>
                    <div className="relative">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" />
                        <input 
                            type="text"
                            placeholder="Search by ID, customer, or phone..."
                            className="w-full pl-10 pr-4 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                        />
                    </div>
                    <div className="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                        {['All', 'Pending', 'In Transit', 'Delivered'].map((f) => (
                            <button key={f} className="px-3 py-1.5 whitespace-nowrap bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase rounded-full border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-200 transition-colors">
                                {f}
                            </button>
                        ))}
                    </div>
                </div>

                <div className="flex-1 overflow-y-auto p-4 space-y-3">
                    {deliveries.map((d) => (
                        <div 
                            key={d.id}
                            onClick={() => setSelectedDelivery(d)}
                            className={`p-4 rounded-2xl border cursor-pointer transition-all ${
                                selectedDelivery?.id === d.id 
                                ? 'bg-blue-50/50 dark:bg-blue-900/10 border-blue-200 dark:border-blue-800 ring-1 ring-blue-500' 
                                : 'bg-white dark:bg-zinc-900 border-zinc-100 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700'
                            }`}
                        >
                            <div className="flex justify-between items-start mb-3">
                                <span className="text-[10px] font-mono font-black text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded uppercase">
                                    {d.reference}
                                </span>
                                <span className={`px-2 py-0.5 rounded-full text-[9px] font-black uppercase border ${getStatusStyle(d.status)}`}>
                                    {d.status.replace('_', ' ')}
                                </span>
                            </div>
                            <div className="space-y-2">
                                <div className="flex items-center gap-3">
                                    <div className="w-1.5 h-1.5 rounded-full bg-blue-500" />
                                    <p className="text-xs font-bold text-zinc-900 dark:text-white truncate">{d.pickup_address}</p>
                                </div>
                                <div className="ml-[3px] border-l border-dashed border-zinc-300 dark:border-zinc-700 h-4" />
                                <div className="flex items-center gap-3">
                                    <MapPin className="w-3 h-3 text-rose-500" />
                                    <p className="text-xs font-bold text-zinc-500 truncate">
                                        {d.stops?.length > 0 ? d.stops[d.stops.length - 1].address : 'No destination'}
                                    </p>
                                </div>
                            </div>
                            <div className="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <div className="w-6 h-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[10px] font-black text-zinc-400">
                                        {d.customer?.name.charAt(0)}
                                    </div>
                                    <span className="text-[10px] font-bold text-zinc-600 dark:text-zinc-400">{d.customer?.name}</span>
                                </div>
                                <div className="text-[10px] font-bold text-zinc-400 flex items-center gap-1">
                                    <Clock className="w-3 h-3" />
                                    {format(new Date(d.created_at), 'HH:mm')}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {/* Delivery Details Panel */}
            <div className="flex-1 overflow-y-auto bg-zinc-50 dark:bg-zinc-950 p-8">
                {selectedDelivery ? (
                    <div className="max-w-4xl mx-auto space-y-8">
                        {/* Summary Header */}
                        <div className="bg-white dark:bg-zinc-900 p-8 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div>
                                <h2 className="text-3xl font-black text-zinc-900 dark:text-white">{selectedDelivery.reference}</h2>
                                <p className="text-zinc-500 mt-2 flex items-center gap-2">
                                    Product: <span className="font-bold text-zinc-900 dark:text-zinc-300">{selectedDelivery.package_description || 'General Package'}</span>
                                    • Weight: <span className="font-bold text-zinc-900 dark:text-zinc-300">{selectedDelivery.package_weight_kg}kg</span>
                                </p>
                            </div>
                            <div className="text-right">
                                <p className="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Total Value</p>
                                <p className="text-3xl font-black text-blue-600 italic">
                                    {selectedDelivery.total_amount} <span className="text-sm not-italic opacity-50">GHS</span>
                                </p>
                            </div>
                        </div>

                        {/* Stop Timeline */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div className="bg-white dark:bg-zinc-900 p-8 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-lg space-y-6">
                                <h3 className="text-lg font-black italic flex items-center gap-2">
                                    <Truck className="w-5 h-5 text-blue-600" />
                                    Routing Sequence
                                </h3>
                                <div className="space-y-0 relative">
                                    <div className="absolute left-[15px] top-4 bottom-4 w-0.5 bg-zinc-100 dark:bg-zinc-800" />
                                    
                                    {/* Pickup Stop */}
                                    <div className="relative pl-10 pb-8">
                                        <div className="absolute left-0 top-0 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 border-2 border-blue-600 flex items-center justify-center z-10">
                                            <Package className="w-4 h-4 text-blue-600" />
                                        </div>
                                        <div>
                                            <h4 className="text-xs font-black uppercase tracking-widest text-zinc-400">Pickup Origin</h4>
                                            <p className="text-sm font-bold text-zinc-900 dark:text-white mt-1">{selectedDelivery.pickup_address}</p>
                                            <div className="mt-2 flex items-center gap-4">
                                                <span className="flex items-center gap-1 text-[10px] font-bold text-zinc-500">
                                                    <User className="w-3 h-3" /> {selectedDelivery.customer?.name}
                                                </span>
                                                <span className="flex items-center gap-1 text-[10px] font-bold text-zinc-500">
                                                    <Phone className="w-3 h-3" /> {selectedDelivery.customer?.phone || 'N/A'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Delivery Stops */}
                                    {selectedDelivery.stops?.map((stop: any, index: number) => (
                                        <div key={stop.id} className="relative pl-10 pb-8">
                                            <div className={`absolute left-0 top-0 w-8 h-8 rounded-full border-2 flex items-center justify-center z-10 transition-all ${
                                                stop.status === 'delivered' 
                                                ? 'bg-emerald-100 dark:bg-emerald-900/30 border-emerald-500' 
                                                : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700'
                                            }`}>
                                                {stop.status === 'delivered' ? (
                                                    <CheckCircle2 className="w-4 h-4 text-emerald-500" />
                                                ) : (
                                                    <span className="text-[10px] font-black">{index + 1}</span>
                                                )}
                                            </div>
                                            <div className={stop.status === 'delivered' ? 'opacity-50' : ''}>
                                                <h4 className="text-xs font-black uppercase tracking-widest text-zinc-400">
                                                    Stop #{index + 1} • {stop.stop_type || 'Dropoff'}
                                                </h4>
                                                <p className="text-sm font-bold text-zinc-900 dark:text-white mt-1">{stop.address}</p>
                                                <div className="mt-2 flex items-center justify-between">
                                                    <div className="flex items-center gap-4">
                                                        <span className="flex items-center gap-1 text-[10px] font-bold text-zinc-500">
                                                            <User className="w-3 h-3" /> {stop.contact_name}
                                                        </span>
                                                        <span className="flex items-center gap-1 text-[10px] font-bold text-zinc-500">
                                                            <Phone className="w-3 h-3" /> {stop.contact_phone}
                                                        </span>
                                                    </div>
                                                    <span className={`text-[9px] font-black uppercase px-2 py-0.5 rounded-full border ${getStatusStyle(stop.status)}`}>
                                                        {stop.status}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <div className="space-y-8">
                                {/* Driver Info Card */}
                                <div className="bg-zinc-900 dark:bg-zinc-900 p-8 rounded-3xl border border-zinc-800 shadow-2xl relative overflow-hidden group">
                                    <div className="absolute top-0 right-0 p-10 opacity-10 rotate-12 group-hover:rotate-45 transition-transform">
                                        <Truck className="w-32 h-32 text-blue-500" />
                                    </div>
                                    <h3 className="text-lg font-black text-white italic mb-6">Assigned Carrier</h3>
                                    {selectedDelivery.driver ? (
                                        <div className="space-y-6 relative z-10">
                                            <div className="flex items-center gap-4">
                                                <div className="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center text-2xl font-black text-white shadow-lg shadow-blue-500/20">
                                                    {selectedDelivery.driver.user?.name.charAt(0)}
                                                </div>
                                                <div>
                                                    <p className="text-xl font-black text-white">{selectedDelivery.driver.user?.name}</p>
                                                    <p className="text-sm font-bold text-blue-400 uppercase tracking-widest">WadExp Platinum Carrier</p>
                                                </div>
                                            </div>
                                            <div className="grid grid-cols-2 gap-4">
                                                <div className="bg-white/5 p-4 rounded-xl border border-white/10">
                                                    <p className="text-[10px] font-black uppercase text-zinc-500">Vehicle</p>
                                                    <p className="text-xs font-bold text-white mt-1">Toyota Hilux (Courier-Spec)</p>
                                                </div>
                                                <div className="bg-white/5 p-4 rounded-xl border border-white/10">
                                                    <p className="text-[10px] font-black uppercase text-zinc-500">Rating</p>
                                                    <p className="text-xs font-bold text-white mt-1">4.92 ★</p>
                                                </div>
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="flex flex-col items-center justify-center py-10 space-y-4">
                                            <AlertCircle className="w-12 h-12 text-amber-500 animate-pulse" />
                                            <p className="text-zinc-500 font-bold italic">Awaiting carrier assignment...</p>
                                            <button className="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl transition-all">
                                                FORCE ASSIGN
                                            </button>
                                        </div>
                                    )}
                                </div>

                                {/* Logistics Audit Log */}
                                <div className="bg-white dark:bg-zinc-900 p-8 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-lg">
                                    <h3 className="text-lg font-black italic flex items-center gap-2 mb-6">
                                        <Clock className="w-5 h-5 text-zinc-400" />
                                        Logistics Audit
                                    </h3>
                                    <div className="space-y-4">
                                        {selectedDelivery.trackingEvents?.slice(0, 5).map((e: any) => (
                                            <div key={e.id} className="flex gap-4">
                                                <div className="mt-1 w-2 h-2 rounded-full bg-zinc-300" />
                                                <div>
                                                    <p className="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-tight">{e.event_type.replace('_', ' ')}</p>
                                                    <p className="text-[10px] text-zinc-400">{format(new Date(e.recorded_at), 'MMM dd, HH:mm:ss')}</p>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ) : (
                    <div className="h-full flex flex-col items-center justify-center text-center space-y-6">
                        <div className="w-24 h-24 rounded-full bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center border-2 border-zinc-200 dark:border-zinc-800 animate-bounce">
                            <Package className="w-12 h-12 text-zinc-300 dark:text-zinc-700" />
                        </div>
                        <div className="space-y-2">
                            <h2 className="text-2xl font-black text-zinc-900 dark:text-white italic uppercase tracking-tighter">Command Center Standby</h2>
                            <p className="text-zinc-500 font-medium max-w-xs mx-auto">
                                Select a delivery from the left panel to begin real-time logistics orchestration.
                            </p>
                        </div>
                    </div>
                )}
            </div>
        </div>
    )
}
