import React from 'react'
import { Briefcase, BarChart3, Clock, Rocket } from 'lucide-react'

interface BusinessProps {
    title: string
    description: string
    cta_text: string
    cta_url: string
}

export const BusinessSection: React.FC<BusinessProps> = ({ title, description, cta_text, cta_url }) => {
    return (
        <section className="bg-white py-24 border-t border-zinc-100">
            <div className="section-container">
                <div className="flex flex-col lg:flex-row items-center gap-16">
                    <div className="lg:w-1/2 relative order-2 lg:order-1">
                         <div className="bg-zinc-950 rounded-[48px] p-8 lg:p-12 shadow-2xl relative overflow-hidden group">
                             {/* Abstract Chart/Dashboard UI Mockup */}
                             <div className="space-y-6 opacity-80">
                                 <div className="flex items-center justify-between">
                                     <div className="h-6 w-32 bg-zinc-800 rounded" />
                                     <div className="h-6 w-12 bg-blue-600 rounded" />
                                 </div>
                                 <div className="grid grid-cols-3 gap-4">
                                     <div className="h-24 bg-zinc-900 rounded-3xl" />
                                     <div className="h-24 bg-zinc-900 rounded-3xl" />
                                     <div className="h-24 bg-zinc-900 rounded-3xl border border-blue-500/30" />
                                 </div>
                                 <div className="h-48 bg-zinc-900 rounded-[32px] overflow-hidden relative">
                                      {/* Simple SVG Chart */}
                                      <svg className="absolute bottom-0 w-full h-1/2 text-primary-gold" viewBox="0 0 100 20">
                                          <path d="M0,20 L10,15 L20,18 L30,10 L40,12 L50,5 L60,8 L70,3 L80,5 L90,1 L100,0 L100,20 Z" fill="currentColor" fillOpacity="0.1" stroke="currentColor" strokeWidth="1" />
                                      </svg>
                                 </div>
                             </div>
                             
                             {/* Floating Success Notification */}
                             <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white p-4 rounded-2xl shadow-2xl border border-zinc-100 flex items-center gap-3 animate-float whitespace-nowrap">
                                  <div className="h-10 w-10 bg-emerald-500 rounded-xl flex items-center justify-center text-white">
                                      <Rocket className="h-6 w-6" />
                                  </div>
                                  <div className="text-sm font-bold text-zinc-900">Efficiency optimized by 42%</div>
                             </div>
                         </div>
                    </div>

                    <div className="lg:w-1/2 space-y-8 order-1 lg:order-2">
                        <div className="h-12 w-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                            <Briefcase className="h-6 w-6" />
                        </div>
                        <h2 className="text-4xl lg:text-5xl leading-tight text-primary-navy">
                            {title}
                        </h2>
                        <p className="text-xl text-zinc-500 leading-relaxed">
                            {description}
                        </p>
                        
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-8 py-4">
                            <div className="flex gap-4">
                                <div className="h-10 w-10 bg-zinc-50 text-zinc-400 rounded-lg flex items-center justify-center shrink-0">
                                    <BarChart3 className="h-5 w-5" />
                                </div>
                                <div className="space-y-1">
                                    <h4 className="font-bold text-zinc-900 text-sm">Detailed Reporting</h4>
                                    <p className="text-xs text-zinc-500">Track and manage expenses centrally.</p>
                                </div>
                            </div>
                            <div className="flex gap-4">
                                <div className="h-10 w-10 bg-zinc-50 text-zinc-400 rounded-lg flex items-center justify-center shrink-0">
                                    <Clock className="h-5 w-5" />
                                </div>
                                <div className="space-y-1">
                                    <h4 className="font-bold text-zinc-900 text-sm">Priority Support</h4>
                                    <p className="text-xs text-zinc-500">Dedicated account management 24/7.</p>
                                </div>
                            </div>
                        </div>

                        <div className="pt-4">
                            <button className="btn-primary px-8 py-4 text-lg">
                                {cta_text}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    )
}
