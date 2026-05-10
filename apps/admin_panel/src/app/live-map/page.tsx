'use client'

import { useEffect, useRef, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { Car, Wifi, WifiOff, RefreshCw, Maximize2, MapPin } from 'lucide-react'
import dynamic from 'next/dynamic'
import { useSocket } from '@/hooks/useSocket'
import { adminApi } from '@/lib/axios'

// Dynamically import the map component to avoid SSR issues with Leaflet
const LiveMap = dynamic(() => import('@/components/LiveTrackingMap'), {
    ssr: false,
    loading: () => (
        <div className="flex items-center justify-center h-full bg-zinc-100 dark:bg-zinc-800 rounded-xl">
            <div className="flex flex-col items-center gap-3">
                <div className="h-8 w-8 animate-spin rounded-full border-4 border-blue-600 border-t-transparent" />
                <p className="text-sm text-zinc-500">Loading map...</p>
            </div>
        </div>
    ),
})

interface DriverPosition {
    driverId: string
    name: string
    lat: number
    lng: number
    heading?: number
    vehicleType?: string
    lastUpdate?: number
}

export default function LiveMapPage() {
    const { connected: socketConnected, socket } = useSocket()
    const [connected, setConnected] = useState(false)
    const [driverPositions, setDriverPositions] = useState<DriverPosition[]>([])
    const [selectedDriver, setSelectedDriver] = useState<DriverPosition | null>(null)
    const [isFullscreen, setIsFullscreen] = useState(false)
    const containerRef = useRef<HTMLDivElement>(null)

    const [error, setError] = useState<string | null>(null)

    const fetchDrivers = async () => {
        try {
            const res = await adminApi.getLiveMapDrivers()
            setDriverPositions(res.data.data || [])
            setConnected(true)
            setError(null)
        } catch (err) {
            console.error('Failed to fetch live driver positions:', err)
            setConnected(false)
            setError('Telemetry link interrupted. Reconnecting...')
        }
    }

    useEffect(() => {
        fetchDrivers() // Initial full fetch
        
        if (!socket) return

        const handleLocationUpdate = (data: DriverPosition) => {
            setDriverPositions(prev => {
                const index = prev.findIndex(d => d.driverId === data.driverId)
                if (index !== -1) {
                    const newPositions = [...prev]
                    newPositions[index] = { ...newPositions[index], ...data, lastUpdate: Date.now() }
                    return newPositions
                }
                return [...prev, { ...data, lastUpdate: Date.now() }]
            })
        }

        socket.on('driver:location_update', handleLocationUpdate)

        return () => {
            socket.off('driver:location_update', handleLocationUpdate)
        }
    }, [socket])

    const toggleFullscreen = () => {
        if (!document.fullscreenElement) {
            containerRef.current?.requestFullscreen()
            setIsFullscreen(true)
        } else {
            document.exitFullscreen()
            setIsFullscreen(false)
        }
    }

    return (
        <DashboardLayout>
            <div ref={containerRef} className="space-y-4 h-full">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-zinc-900 dark:text-white">Live Map</h1>
                        <p className="mt-1 text-sm text-zinc-500">
                            Real-time driver positions and ride tracking
                        </p>
                    </div>
                    <div className="flex items-center gap-3">
                        <div className={`flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium ${
                            connected
                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
                                : 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400'
                        }`}>
                            {connected ? <Wifi className="h-3.5 w-3.5" /> : <WifiOff className="h-3.5 w-3.5" />}
                            {connected ? 'Live' : 'Disconnected'}
                        </div>
                        <span className="text-xs text-zinc-500">
                            {driverPositions.length} drivers online
                        </span>
                        <button
                            onClick={toggleFullscreen}
                            className="p-2 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                        >
                            <Maximize2 className="h-4 w-4 text-zinc-500" />
                        </button>
                    </div>
                </div>

                {/* Map + Driver Sidebar */}
                <div className="flex gap-4 h-[calc(100vh-220px)]">
                    {/* Map */}
                    <div className="flex-1 rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800">
                        <LiveMap drivers={driverPositions} />
                    </div>

                    {/* Driver List Panel */}
                    <div className="w-72 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden flex flex-col">
                        <div className="p-4 border-b border-zinc-200 dark:border-zinc-800">
                            <h3 className="text-sm font-semibold text-zinc-900 dark:text-white">Online Drivers</h3>
                        </div>
                        <div className="flex-1 overflow-y-auto p-2 space-y-1">
                            {driverPositions.map((driver) => (
                                <button
                                    key={driver.driverId}
                                    onClick={() => setSelectedDriver(driver)}
                                    className={`w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-left transition-colors ${
                                        selectedDriver?.driverId === driver.driverId
                                            ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400'
                                            : 'hover:bg-zinc-50 dark:hover:bg-zinc-800'
                                    }`}
                                >
                                    <div className="relative">
                                        <Car className="h-5 w-5 text-zinc-400" />
                                        <div className="absolute -top-0.5 -right-0.5 w-2 h-2 bg-emerald-500 rounded-full" />
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <p className="text-sm font-medium text-zinc-900 dark:text-white truncate">{driver.name}</p>
                                        <p className="text-[10px] text-zinc-500 capitalize">{driver.vehicleType}</p>
                                    </div>
                                </button>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}
