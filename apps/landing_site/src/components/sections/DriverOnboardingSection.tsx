import React from 'react'
import { TrendingUp, Users, Calendar, ArrowRight } from 'lucide-react'

interface Stat {
    id: string
    label: string
    value: string
}

interface DriverOnboardingProps {
    title: string
    subtitle: string
    stats: Stat[]
    cta_text: string
    cta_url: string
}

export const DriverOnboardingSection: React.FC<DriverOnboardingProps> = ({ title, subtitle, stats, cta_text, cta_url }) => {
    return (
        <section className="bg-primary-navy py-24 relative overflow-hidden">
            {/* Background Image/Pattern portal */}
            <div className="absolute top-0 right-0 w-1/3 h-full bg-zinc-900 -z-0 opacity-50 clip-path-swoosh" />
            
            <div className="section-container relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div className="space-y-8">
                    <div className="inline-flex items-center gap-2 px-3 py-1 bg-primary-gold/20 text-primary-gold rounded-full text-xs font-bold tracking-wider uppercase">
                        Partnership Opportunity
                    </div>
                    <h2 className="text-4xl lg:text-5xl text-white leading-tight">
                        {title}
                    </h2>
                    <p className="text-xl text-zinc-400 leading-relaxed max-w-xl">
                        {subtitle}
                    </p>
                    
                    <div className="grid grid-cols-2 gap-8 pt-4">
                        {stats.map(stat => (
                            <div key={stat.id} className="space-y-1">
                                <div className="text-3xl font-black text-white">{stat.value}</div>
                                <div className="text-xs text-zinc-500 uppercase font-bold tracking-widest">{stat.label}</div>
                            </div>
                        ))}
                    </div>

                    <div className="pt-4">
                        <button className="btn-accent flex items-center gap-3 group">
                            {cta_text}
                            <ArrowRight className="h-5 w-5 group-hover:translate-x-1 transition-transform" />
                        </button>
                    </div>
                </div>

                <div className="relative">
                    <div className="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[40px] p-8 space-y-8 shadow-2xl">
                        <div className="space-y-2">
                            <h4 className="text-white font-bold text-xl">Quick Registration</h4>
                            <p className="text-zinc-500 text-sm">Start earning in as little as 24 hours.</p>
                        </div>
                        
                        <div className="space-y-4">
                            {[
                                { icon: <Users className="h-5 w-5" />, text: "Upload your driver's license" },
                                { icon: <Calendar className="h-5 w-5" />, text: "Complete our safety orientation" },
                                { icon: <TrendingUp className="h-5 w-5" />, text: "Get active and start earning" }
                            ].map((item, i) => (
                                <div key={i} className="flex items-center gap-4 p-4 bg-white/5 rounded-2xl hover:bg-white/10 transition-colors">
                                    <div className="h-10 w-10 bg-blue-600 rounded-xl flex items-center justify-center text-white">
                                        {item.icon}
                                    </div>
                                    <span className="text-zinc-300 font-medium">{item.text}</span>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    )
}

// Inline CSS for the swoosh clip path would be better in global.css, but using a div for visual effect now.
