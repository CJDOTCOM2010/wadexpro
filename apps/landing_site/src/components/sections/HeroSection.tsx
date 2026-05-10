import React from 'react'
import Link from 'next/link'
import Image from 'next/image'

interface HeroProps {
    title: string
    subtitle: string
    cta_primary: { text: string; url: string }
    cta_secondary: { text: string; url: string }
}

export const HeroSection: React.FC<HeroProps> = ({ title, subtitle, cta_primary, cta_secondary }) => {
    return (
        <section className="relative overflow-hidden bg-white pt-16 pb-32">
            {/* Background Swoosh */}
            <div className="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[120%] h-[120%] bg-zinc-50 rounded-[100%] -z-10" />
            
            <div className="section-container grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div className="space-y-8 animate-in fade-in slide-in-from-left-8 duration-700">
                    <div className="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold tracking-wider uppercase">
                        <span className="relative flex h-2 w-2">
                          <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                          <span className="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                        </span>
                        Enterprise Mobility Suite
                    </div>
                    
                    <h1 className="text-5xl lg:text-7xl group">
                        {title}
                        <span className="block h-1 w-20 bg-primary-gold mt-4 group-hover:w-40 transition-all duration-500" />
                    </h1>
                    
                    <p className="text-xl text-zinc-500 leading-relaxed max-w-xl">
                        {subtitle}
                    </p>
                    
                    <div className="flex flex-col sm:flex-row gap-4">
                        <Link href={cta_primary.url} className="btn-accent flex items-center justify-center gap-2 px-8 py-4 text-lg">
                            {cta_primary.text}
                        </Link>
                        <Link href={cta_secondary.url} className="btn-primary border-2 border-transparent hover:border-primary-gold/30 flex items-center justify-center gap-2 px-8 py-4 text-lg">
                            {cta_secondary.text}
                        </Link>
                    </div>

                    <div className="flex items-center gap-6 pt-4">
                        <div className="flex -space-x-4">
                            {[1, 2, 3, 4].map(i => (
                                <div key={i} className="h-12 w-12 rounded-full border-4 border-white bg-zinc-100 overflow-hidden">
                                     <div className="h-full w-full bg-gradient-to-br from-zinc-200 to-zinc-300" />
                                </div>
                            ))}
                            <div className="h-12 w-12 rounded-full border-4 border-white bg-blue-600 flex items-center justify-center text-[10px] font-bold text-white">
                                10k+
                            </div>
                        </div>
                        <div className="text-sm font-medium text-zinc-500">
                            Trusted by <span className="font-bold text-zinc-900">10,000+</span> daily riders in Ghana
                        </div>
                    </div>
                </div>

                <div className="relative animate-in fade-in zoom-in-95 duration-1000">
                     {/* Circular Portal for App Mockup */}
                     <div className="relative aspect-square w-full max-w-lg mx-auto bg-gradient-to-br from-blue-600 to-primary-navy rounded-[40px] rotate-3 overflow-hidden shadow-2xl shadow-blue-500/20">
                         {/* Placeholder for complex SVG or Image */}
                         <div className="absolute inset-0 flex items-center justify-center -rotate-3">
                             <div className="w-64 h-[500px] bg-zinc-900 rounded-[40px] border-8 border-zinc-800 shadow-2xl relative">
                                 <div className="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-zinc-800 rounded-b-2xl" />
                                 <div className="p-6 pt-12 space-y-4">
                                     <div className="h-4 w-3/4 bg-zinc-800 rounded" />
                                     <div className="aspect-[9/16] bg-zinc-800 rounded-2xl animate-pulse" />
                                 </div>
                             </div>
                         </div>
                     </div>
                     {/* Floating Badge */}
                     <div className="absolute -bottom-6 -left-6 bg-white p-6 rounded-2xl shadow-xl border border-zinc-100 animate-float">
                         <div className="flex items-center gap-4">
                             <div className="h-12 w-12 bg-primary-gold rounded-xl flex items-center justify-center text-white">
                                 <Zap className="h-6 w-6" />
                             </div>
                             <div>
                                 <div className="text-xs text-zinc-500 font-bold uppercase tracking-wider">Arrival Time</div>
                                 <div className="text-2xl font-black text-zinc-900">3.5 Mins</div>
                             </div>
                         </div>
                     </div>
                </div>
            </div>
        </section>
    )
}

const Zap = ({ className }: { className?: string }) => (
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
)
