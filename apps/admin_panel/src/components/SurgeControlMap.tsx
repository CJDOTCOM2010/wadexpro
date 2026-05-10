'use client'

import { useEffect, useState } from 'react'
import { MapContainer, TileLayer, Circle, Popup, useMap, useMapEvents } from 'react-leaflet'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'

// Fix for Leaflet default icon issues in Next.js
const setupLeafletMarkers = () => {
    delete (L.Icon.Default.prototype as any)._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconRetinaUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon-2x.png',
        iconUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png',
        shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
    });
};

interface SurgeControlMapProps {
    zones: any[]
    selectedZoneId?: string
}

function MapUpdater({ selectedZone }: { selectedZone: any }) {
    const map = useMap();
    useEffect(() => {
        if (selectedZone) {
            map.setView([selectedZone.center_lat, selectedZone.center_lng], 14, { animate: true });
        }
    }, [selectedZone, map]);
    return null;
}

export default function SurgeControlMap({ zones, selectedZoneId }: SurgeControlMapProps) {
    const [mounted, setMounted] = useState(false);
    const selectedZone = zones.find(z => z.id === selectedZoneId);

    useEffect(() => {
        setupLeafletMarkers();
        setMounted(true);
    }, []);

    if (!mounted) return null;

    return (
        <MapContainer
            center={[5.6037, -0.1870]}
            zoom={13}
            style={{ height: '100%', width: '100%' }}
            className="z-0"
        >
            <TileLayer
                attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            />
            
            {zones.map((zone) => {
                const isSelected = zone.id === selectedZoneId;
                const isSurging = zone.current_multiplier > 1.0;
                
                return (
                    <Circle
                        key={zone.id}
                        center={[parseFloat(zone.center_lat), parseFloat(zone.center_lng)]}
                        radius={zone.radius_km * 1000}
                        pathOptions={{
                            color: isSelected ? '#2563eb' : (isSurging ? '#f59e0b' : '#3b82f6'),
                            fillColor: isSelected ? '#3b82f6' : (isSurging ? '#fbbf24' : '#60a5fa'),
                            fillOpacity: isSelected ? 0.3 : 0.15,
                            weight: isSelected ? 3 : 1.5,
                            dashArray: isSurging ? '' : '5, 10'
                        }}
                    >
                        <Popup className="custom-popup">
                            <div className="p-1">
                                <h3 className="font-bold text-zinc-900 border-b pb-1 mb-1">{zone.name}</h3>
                                <div className="flex items-center justify-between gap-4 mt-2">
                                    <span className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Multiplier</span>
                                    <span className={`text-sm font-black ${isSurging ? 'text-amber-600' : 'text-blue-600'}`}>
                                        {zone.current_multiplier}x
                                    </span>
                                </div>
                                <div className="text-[10px] text-zinc-400 mt-1">
                                    Coordinates: {zone.center_lat}, {zone.center_lng}
                                </div>
                            </div>
                        </Popup>
                    </Circle>
                );
            })}

            <MapUpdater selectedZone={selectedZone} />
        </MapContainer>
    );
}

// Custom CSS for Leaflet popups to match branding
const customStyle = `
    .leaflet-popup-content-wrapper {
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }
    .leaflet-popup-tip {
        background: rgba(255, 255, 255, 0.95);
    }
    .dark .leaflet-popup-content-wrapper {
        background: rgba(24, 24, 27, 0.95);
        border-color: rgba(255,255,255,0.1);
        color: white;
    }
`;
if (typeof document !== 'undefined') {
    const styleTag = document.createElement('style');
    styleTag.innerHTML = customStyle;
    document.head.appendChild(styleTag);
}
