'use client'

import React, { useEffect, useState } from 'react'
import { MapContainer, TileLayer, Marker, Popup, useMap } from 'react-leaflet'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'
import 'leaflet.heat'
import { useSearchParams } from 'next/navigation'
import { adminApi } from '@/lib/axios'

// Fix for default marker icons in Leaflet + Next.js
const DefaultIcon = L.icon({
  iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
})
L.Marker.prototype.options.icon = DefaultIcon

interface Driver {
  driverId: string
  lat: number
  lng: number
  name: string
  vehicleType?: string
}

interface LiveTrackingMapProps {
    drivers: Driver[]
}

export function LiveTrackingMap({ drivers }: LiveTrackingMapProps) {
  const searchParams = useSearchParams()
  const focusLat = searchParams.get('lat')
  const focusLng = searchParams.get('lng')

  const [showHeatmap, setShowHeatmap] = useState(false)
  const [heatmapData, setHeatmapData] = useState<[number, number, number][]>([])

  const fetchHeatmap = async () => {
    try {
      const res = await adminApi.getDemandHeatmap()
      setHeatmapData(res.data.data)
    } catch (err) {
      console.error('Heatmap fetch failed:', err)
    }
  }

  useEffect(() => {
    if (showHeatmap) {
      fetchHeatmap()
      const interval = setInterval(fetchHeatmap, 30000) // Refresh every 30s
      return () => clearInterval(interval)
    }
  }, [showHeatmap])

  return (
    <div className="h-full w-full rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 shadow-inner bg-zinc-100 dark:bg-zinc-950 relative">
      <div className="absolute top-4 right-4 z-[1000] flex flex-col gap-2">
        <button 
          onClick={(e) => { e.stopPropagation(); setShowHeatmap(!showHeatmap); }}
          className={`flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black shadow-xl transition-all border ${
            showHeatmap 
            ? 'bg-amber-500 text-white border-amber-400 hover:bg-amber-600' 
            : 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800'
          }`}
        >
          {showHeatmap ? '🔥 Hide Demand Density' : '⚡ Show Demand Hotspots'}
        </button>
      </div>

      <MapContainer 
        center={focusLat ? [parseFloat(focusLat), parseFloat(focusLng!)] : [5.6037, -0.1870]}
        zoom={focusLat ? 16 : 13} 
        style={{ height: '100%', width: '100%' }}
        scrollWheelZoom={true}
      >
        <TileLayer
          attribution='&copy; OpenStreetMap'
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        />
        
        {showHeatmap && heatmapData.length > 0 && <HeatLayer points={heatmapData} />}

        {drivers.map((driver) => (
          <Marker 
            key={driver.driverId} 
            position={[driver.lat, driver.lng]}
          >
            <Popup>
              <div className="p-1">
                <p className="font-bold text-zinc-900">{driver.name}</p>
                <p className="text-xs text-zinc-500 uppercase tracking-widest">{driver.vehicleType || 'Active'}</p>
              </div>
            </Popup>
          </Marker>
        ))}

        <MapUpdater focusLat={focusLat} focusLng={focusLng} />
      </MapContainer>
    </div>
  )
}

function HeatLayer({ points }: { points: [number, number, number][] }) {
  const map = useMap()
  
  useEffect(() => {
    const layer = (L as any).heatLayer(points, { 
      radius: 25, 
      blur: 15, 
      maxZoom: 17,
      gradient: { 0.4: 'blue', 0.65: 'lime', 1: 'red' }
    })
    
    layer.addTo(map)
    return () => {
      map.removeLayer(layer)
    }
  }, [map, points])

  return null
}

function MapUpdater({ focusLat, focusLng }: { focusLat: string | null, focusLng: string | null }) {
  const map = useMap()
  
  useEffect(() => {
    if (focusLat && focusLng) {
      map.setView([parseFloat(focusLat), parseFloat(focusLng)], 16, { animate: true })
    }
  }, [focusLat, focusLng, map])

  return null
}
