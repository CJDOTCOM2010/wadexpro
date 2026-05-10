'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { AlertTriangle, MapPin, Phone, User, CheckCircle, Clock, XCircle } from 'lucide-react'

interface SosEvent {
    id: string
    user_id: string
    user_name: string
    user_phone: string
    ride_request_id: string | null
    lat: string
    lng: string
    status: string
    notes: string | null
    created_at: string
    acknowledged_at: string | null
    resolved_at: string | null
}

const statusConfig: Record<string, { color: string; icon: typeof AlertTriangle; label: string }> = {
    triggered: { color: 'text-red-600 bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800', icon: AlertTriangle, label: 'Active' },
    acknowledged: { color: 'text-amber-600 bg-amber-50 border-amber-200 dark:bg-amber-900/20 dark:border-amber-800', icon: Clock, label: 'Acknowledged' },
    resolved: { color: 'text-emerald-600 bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800', icon: CheckCircle, label: 'Resolved' },
    false_alarm: { color: 'text-zinc-600 bg-zinc-50 border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700', icon: XCircle, label: 'False Alarm' },
}

export default function SosPage() {
    const [events, setEvents] = useState<SosEvent[]>([])
    const [loading, setLoading] = useState(true)

    useEffect(() => {
        adminApi.getSosEvents()
            .then(res => setEvents(res.data.data || res.data || []))
            .catch(console.error)
            .finally(() => setLoading(false))
    }, [])

    const handleAction = async (id: string, status: string, notes: string = '') => {
        try {
            await adminApi.updateSosEvent(id, { status, notes })
            // Re-fetch or update local state
            const res = await adminApi.getSosEvents()
            setEvents(res.data.data || res.data || [])
        } catch (err) {
            console.error('Failed to update SOS event:', err)
        }
    }

    const triggerTestSos = async () => {
        try {
            await adminApi.triggerSos({ lat: 5.6037, lng: -0.1870 })
            const res = await adminApi.getSosEvents()
            setEvents(res.data.data || res.data || [])
        } catch (err) {
            console.error('Failed to trigger test SOS:', err)
        }
    }

    const activeEvents = events.filter(e => ['triggered', 'acknowledged'].includes(e.status))
    const resolvedEvents = events.filter(e => ['resolved', 'false_alarm'].includes(e.status))

    return (
        <DashboardLayout>
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-zinc-900 dark:text-white">SOS Emergency Alerts</h1>
                        <p className="mt-1 text-sm text-zinc-500">Monitor and respond to emergency alerts</p>
                    </div>
                    <div className="flex items-center gap-4">
                        <button 
                            onClick={triggerTestSos}
                            className="px-4 py-2 text-xs font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors"
                        >
                            Trigger Test SOS
                        </button>
                        {activeEvents.length > 0 && (
                            <div className="flex items-center gap-2 px-4 py-2 rounded-full bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 animate-pulse border border-red-200 dark:border-red-800 shadow-sm">
                                <AlertTriangle className="h-4 w-4" />
                                <span className="text-sm font-bold">{activeEvents.length} Active Alert{activeEvents.length > 1 ? 's' : ''}</span>
                            </div>
                        )}
                    </div>
                </div>

                {loading ? (
                    <div className="flex justify-center py-12">
                        <div className="h-8 w-8 animate-spin rounded-full border-4 border-blue-600 border-t-transparent" />
                    </div>
                ) : activeEvents.length === 0 && resolvedEvents.length === 0 ? (
                    <div className="flex flex-col items-center py-16 bg-white dark:bg-zinc-900 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800">
                        <AlertTriangle className="h-12 w-12 text-zinc-300 mb-4" />
                        <p className="text-zinc-500 text-lg font-medium">No SOS events</p>
                        <p className="text-zinc-400 text-sm">All clear — no emergency alerts at this time</p>
                    </div>
                ) : (
                    <>
                        {/* Active Events */}
                        {activeEvents.length > 0 && (
                            <div className="space-y-3">
                                <h2 className="text-sm font-bold text-red-600 uppercase tracking-wider px-2">Active Emergencies</h2>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {activeEvents.map((event) => {
                                        const config = statusConfig[event.status] || statusConfig.triggered
                                        return (
                                            <div
                                                key={event.id}
                                                className={`rounded-xl border-2 p-5 ${config.color} transition-all shadow-lg ring-4 ring-offset-4 ring-red-500/10`}
                                            >
                                                <div className="flex items-start justify-between mb-4">
                                                    <div className="flex items-center gap-3">
                                                        <config.icon className="h-7 w-7 animate-pulse" />
                                                        <div>
                                                            <h3 className="text-lg font-black tracking-tight">{config.label}</h3>
                                                            <p className="text-xs font-bold opacity-70">
                                                                {new Date(event.created_at).toLocaleString()}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    {event.status === 'triggered' && (
                                                        <button 
                                                            onClick={() => handleAction(event.id, 'acknowledged')}
                                                            className="px-3 py-1.5 bg-amber-600 text-white rounded-lg text-xs font-bold hover:bg-amber-700 shadow-md transition-colors"
                                                        >
                                                            Acknowledge
                                                        </button>
                                                    )}
                                                    {event.status === 'acknowledged' && (
                                                        <div className="flex gap-2">
                                                            <button 
                                                                onClick={() => handleAction(event.id, 'resolved', 'Emergency handled by desk.')}
                                                                className="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 shadow-md transition-colors"
                                                            >
                                                                Resolve
                                                            </button>
                                                            <button 
                                                                onClick={() => handleAction(event.id, 'false_alarm', 'Admin confirmed false alarm.')}
                                                                className="px-3 py-1.5 bg-zinc-600 text-white rounded-lg text-xs font-bold hover:bg-zinc-700 shadow-md transition-colors"
                                                            >
                                                                False Alarm
                                                            </button>
                                                        </div>
                                                    )}
                                                </div>

                                                <div className="grid grid-cols-1 gap-2 mb-6 bg-white/40 dark:bg-black/20 p-4 rounded-lg">
                                                    <div className="flex items-center gap-3">
                                                        <div className="p-1.5 bg-white/50 dark:bg-white/10 rounded-md">
                                                            <User className="h-4 w-4 opacity-80" />
                                                        </div>
                                                        <span className="text-sm font-black">{event.user_name}</span>
                                                    </div>
                                                    <div className="flex items-center gap-3">
                                                        <div className="p-1.5 bg-white/50 dark:bg-white/10 rounded-md">
                                                            <Phone className="h-4 w-4 opacity-80" />
                                                        </div>
                                                        <span className="text-sm font-bold tracking-wider">{event.user_phone}</span>
                                                    </div>
                                                    <div className="flex items-center gap-3">
                                                        <div className="p-1.5 bg-white/50 dark:bg-white/10 rounded-md">
                                                            <MapPin className="h-4 w-4 opacity-80" />
                                                        </div>
                                                        <span className="text-xs font-medium font-mono">{event.lat}, {event.lng}</span>
                                                    </div>
                                                </div>

                                                <div className="flex gap-3">
                                                    <a
                                                        href={`tel:${event.user_phone}`}
                                                        className="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white rounded-xl text-sm font-bold border shadow-md hover:shadow-xl transition-all active:scale-95"
                                                    >
                                                        <Phone className="h-4 w-4" />
                                                        Call User
                                                    </a>
                                                    <a
                                                        href={`https://www.google.com/maps/search/?api=1&query=${event.lat},${event.lng}`}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white rounded-xl text-sm font-bold border shadow-md hover:shadow-xl transition-all active:scale-95"
                                                    >
                                                        <MapPin className="h-4 w-4" />
                                                        Open Map
                                                    </a>
                                                </div>
                                            </div>
                                        )
                                    })}
                                </div>
                            </div>
                        )}

                        {/* Resolved Events */}
                        {resolvedEvents.length > 0 && (
                            <div className="space-y-3">
                                <h2 className="text-sm font-bold text-zinc-500 uppercase tracking-wider">Resolved Events</h2>
                                <div className="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                                    <table className="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                                        <thead className="bg-zinc-50 dark:bg-zinc-800/50">
                                            <tr>
                                                <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">User</th>
                                                <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Status</th>
                                                <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Notes</th>
                                                <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Created</th>
                                                <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Resolved</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-zinc-100 dark:divide-zinc-800">
                                            {resolvedEvents.map((event) => (
                                                <tr key={event.id}>
                                                    <td className="px-4 py-3 text-sm text-zinc-900 dark:text-white">{event.user_name}</td>
                                                    <td className="px-4 py-3">
                                                        <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${
                                                            event.status === 'resolved'
                                                                ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400'
                                                                : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-400'
                                                        }`}>
                                                            {event.status.replace('_', ' ')}
                                                        </span>
                                                    </td>
                                                    <td className="px-4 py-3 text-sm text-zinc-500 max-w-[200px] truncate">{event.notes || '--'}</td>
                                                    <td className="px-4 py-3 text-xs text-zinc-500">{new Date(event.created_at).toLocaleString()}</td>
                                                    <td className="px-4 py-3 text-xs text-zinc-500">{event.resolved_at ? new Date(event.resolved_at).toLocaleString() : '--'}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        )}
                    </>
                )}
            </div>
        </DashboardLayout>
    )
}
