'use client'

import { useEffect, useState } from 'react'
import { useSocket } from '@/hooks/useSocket'
import { AlertTriangle, X, MapPin, Phone, User } from 'lucide-react'
import { motion, AnimatePresence } from 'framer-motion'
import Link from 'next/link'

interface SOSAlert {
    userId: string
    userName: string
    rideId?: string
    lat: number
    lng: number
    timestamp: string
}

export const GlobalSOSOverlay = () => {
    const { socket } = useSocket()
    const [activeAlert, setActiveAlert] = useState<SOSAlert | null>(null)

    useEffect(() => {
        if (!socket) return

        const handleSOS = (data: SOSAlert) => {
            console.log('CRITICAL: SOS Alert Received', data)
            // Play alert sound if needed
            setActiveAlert(data)
        }

        socket.on('sos:alert', handleSOS)

        return () => {
            socket.off('sos:alert', handleSOS)
        }
    }, [socket])

    const dismiss = () => setActiveAlert(null)

    return (
        <AnimatePresence>
            {activeAlert && (
                <motion.div
                    initial={{ opacity: 0, y: 100, scale: 0.9 }}
                    animate={{ opacity: 1, y: 0, scale: 1 }}
                    exit={{ opacity: 0, scale: 0.9 }}
                    className="fixed bottom-8 right-8 z-[100] w-96 overflow-hidden rounded-2xl bg-red-600 shadow-2xl ring-4 ring-red-500/50"
                >
                    {/* Header with pulsing alert */}
                    <div className="flex items-center justify-between bg-red-700 px-6 py-4">
                        <div className="flex items-center gap-3">
                            <div className="flex h-10 w-10 animate-pulse items-center justify-center rounded-full bg-white text-red-600">
                                <AlertTriangle className="h-6 w-6" />
                            </div>
                            <div>
                                <h3 className="text-lg font-black text-white">EMERGENCY SOS</h3>
                                <p className="text-[10px] uppercase tracking-widest text-red-100">Immediate Action Required</p>
                            </div>
                        </div>
                        <button onClick={dismiss} className="rounded-full p-1 hover:bg-red-800 text-white/80">
                            <X className="h-5 w-5" />
                        </button>
                    </div>

                    {/* Content */}
                    <div className="p-6 space-y-4">
                        <div className="flex items-center gap-4 text-white">
                            <div className="rounded-lg bg-red-700/50 p-2">
                                <User className="h-5 w-5" />
                            </div>
                            <div>
                                <p className="text-xs text-red-100 mb-0.5">Affected Individual</p>
                                <p className="font-bold">{activeAlert.userName}</p>
                            </div>
                        </div>

                        <div className="flex items-center gap-4 text-white">
                            <div className="rounded-lg bg-red-700/50 p-2">
                                <MapPin className="h-5 w-5" />
                            </div>
                            <div>
                                <p className="text-xs text-red-100 mb-0.5">Reported Coordinates</p>
                                <p className="font-mono text-sm">{activeAlert.lat.toFixed(6)}, {activeAlert.lng.toFixed(6)}</p>
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="grid grid-cols-2 gap-3 pt-2">
                            <Link
                                href={`/live-map?lat=${activeAlert.lat}&lng=${activeAlert.lng}`}
                                onClick={dismiss}
                                className="flex items-center justify-center gap-2 rounded-xl bg-white py-3 text-sm font-bold text-red-600 transition-transform active:scale-95"
                            >
                                <MapPin className="h-4 w-4" />
                                TRACK LIVE
                            </Link>
                            <button className="flex items-center justify-center gap-2 rounded-xl bg-red-800 py-3 text-sm font-bold text-white transition-transform active:scale-95">
                                <Phone className="h-4 w-4" />
                                CALL USER
                            </button>
                        </div>
                    </div>

                    <div className="h-1 bg-red-400/30 w-full overflow-hidden">
                        <motion.div 
                            initial={{ width: "0%" }}
                            animate={{ width: "100%" }}
                            transition={{ duration: 30, ease: "linear" }}
                            className="bg-white h-full"
                        />
                    </div>
                </motion.div>
            )}
        </AnimatePresence>
    )
}
