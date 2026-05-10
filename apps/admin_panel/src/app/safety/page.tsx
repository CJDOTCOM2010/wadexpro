'use client'

import React, { useEffect, useState } from 'react'
import { adminApi } from '@/lib/axios'
import { 
    ShieldAlert, 
    ShieldCheck, 
    Map as MapIcon, 
    AlertTriangle, 
    Fingerprint, 
    navigation as Navigation, 
    Activity,
    ChevronRight,
    Search,
    MessageSquare,
    RotateCcw,
    CheckCircle2,
    XCircle,
    Info,
    Phone
} from 'lucide-react'

export default function SafetyHUD() {
    const [alerts, setAlerts] = useState<any[]>([])
    const [stats, setStats] = useState<any>(null)
    const [loading, setLoading] = useState(true)
    const [selectedAlert, setSelectedAlert] = useState<any>(null)
    const [isResolving, setIsResolving] = useState(false)
    const [resolutionNotes, setResolutionNotes] = useState('')

    useEffect(() => {
        loadSafetyData()
        const interval = setInterval(loadSafetyData, 10000) // Poll every 10s for real-time feel
        return () => clearInterval(interval)
    }, [])

    const loadSafetyData = async () => {
        try {
            const [alertsRes, statsRes] = await Promise.all([
                adminApi.getSafetyAlerts('PENDING'),
                adminApi.getSafetyStats()
            ])
            setAlerts(alertsRes.data.data.data)
            setStats(statsRes.data.data)
        } catch (error) {
            console.error('Failed to load safety data:', error)
        } finally {
            setLoading(false)
        }
    }

    const handleResolve = async (status: string) => {
        if (!resolutionNotes) return
        try {
            await adminApi.resolveSafetyAlert(selectedAlert.id, {
                status,
                notes: resolutionNotes
            })
            setSelectedAlert(null)
            setResolutionNotes('')
            setIsResolving(false)
            loadSafetyData()
        } catch (error) {
            console.error('Failed to resolve alert:', error)
        }
    }

    const getSeverityColor = (severity: string) => {
        switch (severity) {
            case 'CRITICAL': return 'text-red-500 bg-red-50 border-red-100';
            case 'HIGH': return 'text-orange-500 bg-orange-50 border-orange-100';
            case 'MEDIUM': return 'text-yellow-600 bg-yellow-50 border-yellow-100';
            default: return 'text-blue-500 bg-blue-50 border-blue-100';
        }
    }

    return (
        <div className="min-h-screen bg-[#F1F5F9] pb-12">
            {/* Safety Header */}
            <div className="bg-[#0F172A] text-white p-8 relative overflow-hidden">
                <div className="absolute right-0 top-0 w-1/3 h-full bg-gradient-to-l from-blue-500/10 to-transparent skew-x-12 transform translate-x-20"></div>
                <div className="relative z-10 flex justify-between items-end">
                    <div>
                        <div className="flex items-center gap-3 mb-2">
                            <div className="bg-blue-500 p-2 rounded-xl">
                                <ShieldAlert size={24} />
                            </div>
                            <h1 className="text-3xl font-black tracking-tight">WADEX-GUARD</h1>
                        </div>
                        <p className="text-slate-400 font-medium">Real-time Safety Telemetry & Incident Orchestration</p>
                    </div>
                    {stats && (
                        <div className="flex gap-8">
                            <div className="text-right">
                                <p className="text-xs font-bold text-slate-500 uppercase">Active SOS</p>
                                <p className="text-2xl font-black text-red-400">{stats.active_sos}</p>
                            </div>
                            <div className="text-right">
                                <p className="text-xs font-bold text-slate-500 uppercase">Critical Anomalies</p>
                                <p className="text-2xl font-black text-orange-400">{stats.pending_critical}</p>
                            </div>
                            <div className="text-right">
                                <p className="text-xs font-bold text-slate-500 uppercase">Total Today</p>
                                <p className="text-2xl font-black text-blue-400">{stats.total_today}</p>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            <div className="max-w-[1600px] mx-auto px-8 -mt-6 relative z-20">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    {/* Active Incident Feed */}
                    <div className="lg:col-span-4 space-y-4">
                        <div className="flex items-center justify-between mb-4 px-2">
                            <h3 className="font-bold text-slate-800 flex items-center gap-2">
                                <Activity size={18} className="text-blue-500" />
                                Incident Queue
                            </h3>
                            <span className="text-xs font-bold bg-white px-2 py-1 rounded-lg border border-slate-200 text-slate-400">
                                {alerts.length} PENDING
                            </span>
                        </div>

                        {loading ? (
                            <div className="bg-white rounded-3xl p-12 text-center border border-slate-200">
                                <RotateCcw className="animate-spin text-blue-500 mx-auto" size={32} />
                                <p className="text-slate-500 mt-4 font-medium">Scanning Network Layer...</p>
                            </div>
                        ) : alerts.length === 0 ? (
                            <div className="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm">
                                <ShieldCheck className="text-green-500 mx-auto mb-4" size={48} />
                                <h4 className="font-bold text-slate-800">All Systems Normal</h4>
                                <p className="text-slate-500 text-sm mt-2">No proactive security alerts triggered currently.</p>
                            </div>
                        ) : (
                            alerts.map((alert) => (
                                <div 
                                    key={alert.id}
                                    onClick={() => setSelectedAlert(alert)}
                                    className={`bg-white rounded-2xl border-2 transition-all cursor-pointer p-5 group shadow-sm ${
                                        selectedAlert?.id === alert.id ? 'border-blue-500 ring-4 ring-blue-500/5 translate-x-2' : 'border-white hover:border-slate-200 shadow-slate-200/50'
                                    }`}
                                >
                                    <div className="flex justify-between items-start mb-3">
                                        <div className={`text-[10px] font-black px-2 py-0.5 rounded-full border ${getSeverityColor(alert.severity)}`}>
                                            {alert.severity}
                                        </div>
                                        <p className="text-[10px] font-bold text-slate-400">{new Date(alert.created_at).toLocaleTimeString()}</p>
                                    </div>
                                    <h4 className="font-bold text-slate-800 flex items-center gap-2">
                                        {alert.type === 'DEVIATION' && <Navigation className="text-orange-500" size={16} />}
                                        {alert.type === 'FRAUD' && <Fingerprint className="text-purple-500" size={16} />}
                                        {alert.type === 'SOS' && <AlertTriangle className="text-red-500" size={16} />}
                                        {alert.type.replace('_', ' ')}
                                    </h4>
                                    <p className="text-xs text-slate-500 mt-1 line-clamp-1">
                                        Ride ID: {alert.ride_id.substring(0, 8)}...
                                    </p>
                                    <div className="mt-4 flex items-center justify-between border-t border-slate-50 pt-3">
                                        <div className="flex -space-x-2">
                                            <div className="w-6 h-6 rounded-full bg-blue-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-blue-600">C</div>
                                            <div className="w-6 h-6 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-slate-600">D</div>
                                        </div>
                                        <ChevronRight size={16} className="text-slate-300 group-hover:text-blue-500 transition-transform group-hover:translate-x-1" />
                                    </div>
                                </div>
                            ))
                        )}
                    </div>

                    {/* Tactical Workspace */}
                    <div className="lg:col-span-8">
                        {selectedAlert ? (
                            <div className="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-2xl shadow-slate-300/50 flex flex-col h-[750px]">
                                {/* Detail Header */}
                                <div className="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                                    <div className="flex items-center gap-4">
                                        <div className={`p-3 rounded-2xl ${getSeverityColor(selectedAlert.severity)}`}>
                                            <ShieldAlert size={24} />
                                        </div>
                                        <div>
                                            <h3 className="font-black text-xl text-slate-800 tracking-tight">{selectedAlert.type.replace('_', ' ')}</h3>
                                            <p className="text-slate-500 text-sm font-medium">Incident Review #{selectedAlert.id.substring(0, 8)}</p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-3">
                                        <button className="flex items-center gap-2 bg-slate-100 text-slate-700 px-4 py-2 rounded-xl font-bold hover:bg-slate-200 transition-all text-sm">
                                            <Phone size={16} />
                                            Call Support
                                        </button>
                                        <button 
                                            onClick={() => setIsResolving(true)}
                                            className="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 text-sm"
                                        >
                                            <CheckCircle2 size={16} />
                                            Resolve Incident
                                        </button>
                                    </div>
                                </div>

                                <div className="flex-1 overflow-y-auto p-8 grid grid-cols-1 md:grid-cols-2 gap-8 content-start">
                                    {/* Map Visualization Placeholder */}
                                    <div className="col-span-full h-[300px] bg-slate-900 rounded-3xl relative overflow-hidden group">
                                        <div className="absolute inset-0 opacity-40 bg-[url('https://api.mapbox.com/styles/v1/mapbox/dark-v10/static/0,0,1/800x400?access_token=pk.placeholder')] bg-cover"></div>
                                        <div className="absolute inset-0 flex flex-col items-center justify-center p-12 text-center group-hover:scale-105 transition-transform duration-700">
                                            <MapIcon size={48} className="text-blue-400 mb-4 drop-shadow-[0_0_15px_rgba(59,130,246,0.5)]" />
                                            <h4 className="text-white font-black tracking-widest uppercase text-sm">Tactical Telemetry View</h4>
                                            <p className="text-slate-400 text-xs mt-2 max-w-xs">Real-time path tracking vs. projected polyline.</p>
                                        </div>
                                        <div className="absolute top-4 right-4 bg-red-600 text-white text-[10px] font-black px-3 py-1 rounded-full animate-pulse shadow-lg shadow-red-600/40">
                                            LIVE SIGNAL ACTIVE
                                        </div>
                                    </div>

                                    {/* Telemetry Data */}
                                    <div className="space-y-6">
                                        <div>
                                            <h5 className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Anomaly Metadata</h5>
                                            <div className="space-y-2">
                                                {Object.entries(selectedAlert.metadata).map(([key, value]: [string, any]) => (
                                                    <div key={key} className="flex justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                                        <span className="text-xs font-bold text-slate-500 uppercase">{key.replace('_', ' ')}</span>
                                                        <span className="text-xs font-black text-slate-800">{Array.isArray(value) ? value.join(', ') : value.toString()}</span>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>

                                        <div className="p-5 bg-blue-50/50 rounded-2xl border border-blue-100">
                                            <h5 className="text-xs font-black text-blue-800 flex items-center gap-2 mb-2">
                                                <Info size={14} />
                                                WADEX-Guard Insight
                                            </h5>
                                            <p className="text-xs text-blue-700 leading-relaxed font-medium">
                                                {selectedAlert.type === 'DEVIATION' ? 
                                                    'The driver has diverged from the recommended route by more than 400m. This could indicate localized traffic or an unauthorized detour.' :
                                                    'Heuristic patterns indicate potential account sharing or suspicious booking behavior. Verify ID before proceeding.'}
                                            </p>
                                        </div>
                                    </div>

                                    {/* Related Entities */}
                                    <div className="space-y-6">
                                        <div>
                                            <h5 className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Entities Involved</h5>
                                            <div className="space-y-3">
                                                <div className="flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-2xl">
                                                    <div className="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-600">C</div>
                                                    <div>
                                                        <p className="text-xs font-black text-slate-800 uppercase">Customer</p>
                                                        <p className="text-sm font-bold text-slate-600">{selectedAlert.customer_name || 'Anonymous User'}</p>
                                                    </div>
                                                    <button className="ml-auto p-2 bg-blue-50 text-blue-600 rounded-lg"><Phone size={14} /></button>
                                                </div>
                                                <div className="flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-2xl">
                                                    <div className="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-600">D</div>
                                                    <div>
                                                        <p className="text-xs font-black text-slate-800 uppercase">Driver</p>
                                                        <p className="text-sm font-bold text-slate-600">{selectedAlert.driver_name || 'WADEX Driver'}</p>
                                                    </div>
                                                    <button className="ml-auto p-2 bg-blue-50 text-blue-600 rounded-lg"><Phone size={14} /></button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button className="w-full py-4 bg-[#0F172A] text-white rounded-2xl font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2 hover:bg-slate-800 transition-all">
                                            <ShieldAlert size={16} />
                                            Open Full Audit Trail
                                        </button>
                                    </div>
                                </div>

                                {isResolving && (
                                    <div className="p-6 bg-slate-50 border-t border-slate-200">
                                        <h5 className="font-bold text-slate-800 mb-2">Resolution Log</h5>
                                        <textarea 
                                            value={resolutionNotes}
                                            onChange={(e) => setResolutionNotes(e.target.value)}
                                            className="w-full p-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all text-sm mb-4"
                                            placeholder="Document the resolution steps, driver explanation, or support actions taken..."
                                            rows={2}
                                        ></textarea>
                                        <div className="flex gap-4">
                                            <button 
                                                onClick={() => handleResolve('RESOLVED')}
                                                className="flex-1 bg-green-600 text-white py-3 rounded-xl font-bold hover:bg-green-700 transition-all flex items-center justify-center gap-2 shadow-lg shadow-green-600/20"
                                            >
                                                <ShieldCheck size={18} />
                                                RESOLVE INCIDENT
                                            </button>
                                            <button 
                                                onClick={() => handleResolve('DISMISSED')}
                                                className="flex-1 bg-slate-200 text-slate-700 py-3 rounded-xl font-bold hover:bg-slate-300 transition-all flex items-center justify-center gap-2"
                                            >
                                                <XCircle size={18} />
                                                DISMISS AS FALSE ALARM
                                            </button>
                                        </div>
                                    </div>
                                )}
                            </div>
                        ) : (
                            <div className="h-[750px] bg-white border-2 border-dashed border-slate-200 rounded-3xl flex flex-col items-center justify-center text-center p-12">
                                <div className="p-6 bg-slate-50 rounded-full mb-6">
                                    <ShieldAlert size={64} className="text-slate-300" />
                                </div>
                                <h3 className="text-2xl font-black text-slate-800 mb-2 tracking-tight">TACTICAL READY</h3>
                                <p className="text-slate-500 max-w-sm font-medium">Select an active incident from the queue to initiate real-time audit and response orchestration.</p>
                                <div className="mt-8 flex gap-4">
                                    <div className="flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-500 uppercase tracking-widest border border-slate-200">
                                        <Activity size={12} />
                                        Encryption Active
                                    </div>
                                    <div className="flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-500 uppercase tracking-widest border border-slate-200">
                                        <RotateCcw size={12} />
                                        Auto-Syncing
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    )
}
