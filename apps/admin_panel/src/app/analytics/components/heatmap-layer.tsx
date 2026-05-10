'use client'

import { useEffect } from 'react'
import { useMap } from 'react-leaflet'
import L from 'leaflet'
import 'leaflet.heat'

interface HeatmapLayerProps {
    points: [number, number, number][] // [lat, lng, intensity]
}

export default function HeatmapLayer({ points }: HeatmapLayerProps) {
    const map = useMap()

    useEffect(() => {
        if (!map || !points.length) return

        // @ts-ignore
        const heatLayer = L.heatLayer(points, {
            radius: 25,
            blur: 15,
            maxZoom: 17,
            gradient: {
                0.2: '#3b82f6', // blue
                0.4: '#3b82f6', 
                0.6: '#eab308', // yellow
                0.8: '#f97316', // orange
                1.0: '#ef4444'  // red
            }
        }).addTo(map)

        return () => {
            map.removeLayer(heatLayer)
        }
    }, [map, points])

    return null
}
