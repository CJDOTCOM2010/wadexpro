'use client'

import React, { useEffect, useState } from 'react'
import { adminApi } from '@/lib/axios'
import { 
    Globe, 
    Plus, 
    Settings, 
    Map as MapIcon, 
    DollarSign, 
    Percents, 
    Clock, 
    CheckCircle2, 
    XCircle,
    ChevronRight,
    Edit2,
    Trash2,
    Activity
} from 'lucide-react'

export default function RegionsPage() {
    const [regions, setRegions] = useState<any[]>([])
    const [loading, setLoading] = useState(true)
    const [selectedRegion, setSelectedRegion] = useState<any>(null)
    const [isEditing, setIsEditing] = useState(false)

    useEffect(() => {
        loadRegions()
    }, [])

    const loadRegions = async () => {
        try {
            const response = await adminApi.getRegions()
            setRegions(response.data.data)
        } catch (error) {
            console.error('Failed to load regions:', error)
        } finally {
            setLoading(false)
        }
    }

    const toggleStatus = async (region: any) => {
        try {
            await adminApi.updateRegion(region.id, { is_active: !region.is_active })
            loadRegions()
        } catch (error) {
            console.error('Failed to toggle status:', error)
        }
    }

    return (
        <div className="min-h-screen bg-[#F8FAFC] p-8">
            {/* Header */}
            <div className="flex justify-between items-center mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-[#0F172A] flex items-center gap-2">
                        <Globe className="text-[#3B82F6]" />
                        Global Expansion Hub
                    </h1>
                    <p className="text-[#64748B]">Manage regional geofences, pricing, and localized settings.</p>
                </div>
                <button 
                    onClick={() => {
                        setSelectedRegion({
                            name: '',
                            slug: '',
                            currency_code: 'USD',
                            currency_symbol: '$',
                            tax_percentage: 0,
                            timezone: 'UTC',
                            is_active: true
                        })
                        setIsEditing(true)
                    }}
                    className="flex items-center gap-2 bg-[#3B82F6] text-white px-4 py-2 rounded-xl font-bold hover:bg-blue-600 transition-all shadow-lg shadow-blue-500/20"
                >
                    <Plus size={18} />
                    Add New Region
                </button>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Regions List */}
                <div className="lg:col-span-2 space-y-4">
                    {loading ? (
                        <div className="bg-white p-12 rounded-3xl border border-slate-200 flex flex-col items-center justify-center space-y-4">
                            <Activity className="animate-spin text-blue-500" size={32} />
                            <p className="text-slate-500 font-medium">Scanning Global Markets...</p>
                        </div>
                    ) : regions.length === 0 ? (
                        <div className="bg-white p-12 rounded-3xl border border-slate-200 text-center">
                            <Globe className="mx-auto text-slate-300 mb-4" size={48} />
                            <h3 className="text-lg font-bold text-slate-700">No Active Regions</h3>
                            <p className="text-slate-500 max-w-xs mx-auto mt-2">Expand your reach by defining your first operational zone.</p>
                        </div>
                    ) : (
                        regions.map((region) => (
                            <div 
                                key={region.id}
                                className={`group bg-white p-6 rounded-3xl border transition-all cursor-pointer ${
                                    selectedRegion?.id === region.id ? 'border-blue-500 ring-4 ring-blue-500/5' : 'border-slate-200 hover:border-blue-200 shadow-sm'
                                }`}
                                onClick={() => setSelectedRegion(region)}
                            >
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-4">
                                        <div className={`p-3 rounded-2xl ${region.is_active ? 'bg-blue-50 text-blue-600' : 'bg-slate-50 text-slate-400'}`}>
                                            <MapIcon size={24} />
                                        </div>
                                        <div>
                                            <h3 className="font-bold text-[#1E293B] text-lg">{region.name}</h3>
                                            <div className="flex items-center gap-3 mt-1">
                                                <span className="flex items-center gap-1 text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md uppercase">
                                                    {region.currency_code}
                                                </span>
                                                <span className="text-xs text-slate-400 flex items-center gap-1">
                                                    <Clock size={12} /> {region.timezone}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-3">
                                        <div className="text-right mr-4">
                                            <p className="text-xs font-bold text-slate-400 uppercase tracking-wider">Tax Rate</p>
                                            <p className="text-lg font-black text-slate-700">{region.tax_percentage}%</p>
                                        </div>
                                        <button 
                                            onClick={(e) => {
                                                e.stopPropagation()
                                                toggleStatus(region)
                                            }}
                                            className={`p-2 rounded-full transition-colors ${
                                                region.is_active ? 'text-green-500 hover:bg-green-50' : 'text-slate-300 hover:bg-slate-100'
                                            }`}
                                        >
                                            <CheckCircle2 size={24} />
                                        </button>
                                        <ChevronRight className="text-slate-300 group-hover:text-blue-400 transition-transform group-hover:translate-x-1" />
                                    </div>
                                </div>
                            </div>
                        ))
                    )}
                </div>

                {/* Sidebar Workspace */}
                <div className="space-y-6">
                    {selectedRegion ? (
                        <div className="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xl shadow-slate-200/50">
                            <div className="bg-[#0F172A] p-6 text-white text-center">
                                <h2 className="text-xl font-bold">{selectedRegion.name || 'New Region'}</h2>
                                <p className="text-slate-400 text-sm mt-1">Operational Parameters</p>
                            </div>
                            
                            <div className="p-6 space-y-6">
                                {/* Basic Info */}
                                <div className="space-y-4">
                                    <label className="block">
                                        <span className="text-sm font-bold text-slate-500 ml-1">Region Name</span>
                                        <input 
                                            type="text" 
                                            value={selectedRegion.name}
                                            onChange={(e) => setSelectedRegion({...selectedRegion, name: e.target.value})}
                                            className="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all font-medium"
                                            placeholder="e.g. Lagos, Nigeria"
                                        />
                                    </label>
                                    
                                    <div className="grid grid-cols-2 gap-4">
                                        <label className="block">
                                            <span className="text-sm font-bold text-slate-500 ml-1 text-[10px] uppercase">Currency Code</span>
                                            <input 
                                                type="text" 
                                                value={selectedRegion.currency_code}
                                                onChange={(e) => setSelectedRegion({...selectedRegion, currency_code: e.target.value})}
                                                className="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all font-medium"
                                                placeholder="NGN"
                                            />
                                        </label>
                                        <label className="block">
                                            <span className="text-sm font-bold text-slate-500 ml-1 text-[10px] uppercase">Tax %</span>
                                            <input 
                                                type="number" 
                                                value={selectedRegion.tax_percentage}
                                                onChange={(e) => setSelectedRegion({...selectedRegion, tax_percentage: e.target.value})}
                                                className="w-full mt-1 px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all font-medium"
                                                placeholder="7.5"
                                            />
                                        </label>
                                    </div>
                                </div>

                                {/* Placeholder for Map / Geofence */}
                                <div className="bg-slate-900 rounded-2xl aspect-video relative flex flex-col items-center justify-center p-6 text-center group">
                                    <div className="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                                    <MapIcon className="text-blue-400 mb-2 group-hover:scale-110 transition-transform" size={32} />
                                    <p className="text-white text-xs font-bold uppercase tracking-widest">Geofence Boundary</p>
                                    <p className="text-slate-400 text-[10px] mt-1">Multi-Polygon GeoJSON definition active</p>
                                    <button className="mt-4 text-[10px] bg-blue-500 text-white px-3 py-1.5 rounded-lg font-black hover:bg-blue-400">EDIT BOUNDARY</button>
                                </div>

                                {/* Pricing Strategy */}
                                <div className="space-y-4 pt-4 border-t border-slate-100">
                                    <div className="flex items-center justify-between">
                                        <h4 className="font-bold text-slate-800 flex items-center gap-2">
                                            <DollarSign size={16} className="text-green-500" />
                                            Base Pricing
                                        </h4>
                                        <button className="text-xs text-blue-500 font-bold hover:underline">Edit Individual Rates</button>
                                    </div>
                                    <div className="grid grid-cols-2 gap-4">
                                        <div className="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                            <p className="text-[10px] font-bold text-slate-400 uppercase">Min Fare</p>
                                            <p className="text-lg font-black text-slate-700">{selectedRegion.currency_symbol} 10.00</p>
                                        </div>
                                        <div className="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                            <p className="text-[10px] font-bold text-slate-400 uppercase">Per KM</p>
                                            <p className="text-lg font-black text-slate-700">{selectedRegion.currency_symbol} 2.50</p>
                                        </div>
                                    </div>
                                </div>

                                {/* Action Buttons */}
                                <div className="grid grid-cols-2 gap-4 pt-4">
                                    <button 
                                        className="py-3 px-4 bg-[#F1F5F9] text-[#475569] rounded-2xl font-bold hover:bg-slate-200 transition-all flex items-center justify-center gap-2"
                                        onClick={() => setSelectedRegion(null)}
                                    >
                                        <Trash2 size={18} />
                                        Discard
                                    </button>
                                    <button 
                                        className="py-3 px-4 bg-[#3B82F6] text-white rounded-2xl font-bold hover:bg-blue-600 transition-all shadow-lg shadow-blue-500/20 flex items-center justify-center gap-2"
                                        onClick={async () => {
                                            if (selectedRegion.id) {
                                                await adminApi.updateRegion(selectedRegion.id, selectedRegion)
                                            } else {
                                                await adminApi.createRegion(selectedRegion)
                                            }
                                            loadRegions()
                                            setSelectedRegion(null)
                                        }}
                                    >
                                        <CheckCircle2 size={18} />
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    ) : (
                        <div className="bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl p-12 text-center flex flex-col items-center justify-center">
                            <div className="bg-white p-4 rounded-full shadow-sm mb-4">
                                <Settings className="text-slate-400" size={32} />
                            </div>
                            <h4 className="font-bold text-slate-600">Select a Region</h4>
                            <p className="text-slate-400 text-sm max-w-[200px] mt-2">Choose a region from the list to view and edit its operational settings.</p>
                        </div>
                    )}

                    {/* Pro Tips / Stats */}
                    <div className="bg-gradient-to-br from-blue-600 to-blue-800 rounded-3xl p-6 text-white overflow-hidden relative">
                         <div className="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                         <h4 className="font-bold flex items-center gap-2 mb-2">
                             <Globe size={18} />
                             Global Tip
                         </h4>
                         <p className="text-blue-100 text-sm leading-relaxed">
                             Regional boundaries are enforced strictly via GeoFencing. Ensure your polygons cover all urban hubs to maximize pricing efficiency.
                         </p>
                    </div>
                </div>
            </div>
        </div>
    )
}
