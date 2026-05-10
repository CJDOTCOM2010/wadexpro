import React from 'react'
import { Map, Pin, CheckCircle2 } from 'lucide-react'

interface RegionContentProps {
    title: string
    content: string
    payment_info: string
}

export const RegionContentSection: React.FC<RegionContentProps> = ({ title, content, payment_info }) => {
    return (
        <section className="bg-zinc-950 py-24 relative overflow-hidden">
             {/* Abstract Map Background */}
             <div className="absolute inset-0 opacity-10 pointer-events-none">
                 <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                     <defs>
                         <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                             <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" strokeWidth="0.5"/>
                         </pattern>
                     </defs>
                     <rect width="100%" height="100%" fill="url(#grid)" />
                 </svg>
             </div>

            <div className="section-container relative z-10">
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div className="space-y-8">
                         <div className="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/20 text-emerald-500 rounded-full text-xs font-bold tracking-wider uppercase">
                            Operational in Ghana
                        </div>
                        <h2 className="text-4xl lg:text-5xl text-white leading-tight">
                            {title}
                        </h2>
                        <p className="text-xl text-zinc-400 leading-relaxed">
                            {content}
                        </p>
                        
                        <div className="p-6 bg-white/5 border border-white/10 rounded-2xl space-y-4">
                            <h4 className="text-white font-bold flex items-center gap-2">
                                <CheckCircle2 className="h-5 w-5 text-emerald-500" />
                                Local Payment Support
                            </h4>
                            <p className="text-sm text-zinc-500">
                                {payment_info}
                            </p>
                        </div>
                    </div>

                    <div className="relative">
                         <div className="aspect-[4/3] bg-zinc-900 rounded-[48px] border-8 border-zinc-800 shadow-2xl overflow-hidden group">
                              {/* Visual Representation of cities/map */}
                              <div className="h-full w-full bg-gradient-to-br from-zinc-800 to-zinc-950 p-12 relative">
                                   <div className="absolute top-1/4 left-1/4 animate-ping h-4 w-4 bg-primary-gold rounded-full" />
                                   <div className="absolute top-1/2 left-1/2 animate-ping h-4 w-4 bg-primary-gold rounded-full" />
                                   <div className="absolute bottom-1/4 right-1/4 animate-ping h-4 w-4 bg-blue-500 rounded-full" />
                                   
                                   <div className="grid grid-cols-2 gap-4">
                                       {['Accra', 'Kumasi', 'Takoradi', 'Tamale'].map(city => (
                                           <div key={city} className="bg-white/5 border border-white/10 p-4 rounded-xl flex items-center gap-3 group-hover:bg-white/10 transition-colors">
                                               <Pin className="h-4 w-4 text-primary-gold" />
                                               <span className="text-white font-bold">{city}</span>
                                           </div>
                                       ))}
                                   </div>
                              </div>
                         </div>
                    </div>
                </div>
            </div>
        </section>
    )
}
