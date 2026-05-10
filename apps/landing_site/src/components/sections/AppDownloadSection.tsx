import React from 'react'
import { Smartphone, Apple, Play } from 'lucide-react'

interface AppDownloadProps {
    title: string
    subtitle: string
}

export const AppDownloadSection: React.FC<AppDownloadProps> = ({ title, subtitle }) => {
    return (
        <section className="bg-primary-navy py-24 overflow-hidden relative">
            {/* Decorative background circles */}
            <div className="absolute top-0 right-0 h-96 w-96 bg-blue-600/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2" />
            <div className="absolute bottom-0 left-0 h-96 w-96 bg-primary-gold/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2" />

            <div className="section-container relative z-10 text-center lg:text-left grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div className="space-y-8">
                    <h2 className="text-4xl lg:text-5xl text-white leading-tight">
                        {title}
                    </h2>
                    <p className="text-lg text-zinc-400 max-w-xl">
                        {subtitle}
                    </p>
                    <div className="flex flex-wrap justify-center lg:justify-start gap-4">
                        <button className="flex items-center gap-4 bg-zinc-900 border border-zinc-800 px-6 py-3 rounded-2xl hover:bg-zinc-800 transition-all group">
                            <Apple className="h-8 w-8 text-white group-hover:scale-110 transition-transform" />
                            <div className="text-left">
                                <div className="text-[10px] text-zinc-500 uppercase font-bold tracking-widest">Download on the</div>
                                <div className="text-lg font-bold text-white leading-tight">App Store</div>
                            </div>
                        </button>
                        <button className="flex items-center gap-4 bg-zinc-900 border border-zinc-800 px-6 py-3 rounded-2xl hover:bg-zinc-800 transition-all group">
                            <div className="h-8 w-8 flex items-center justify-center">
                                <svg viewBox="0 0 24 24" className="h-full w-full group-hover:scale-110 transition-transform" fill="white">
                                    <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.61 3,21.09 3,20.5M16.81,15.12L18.66,14.05C20.44,13.03 20.44,10.97 18.66,9.95L16.81,8.88L14.89,12L16.81,15.12M4.6,2.15L15.39,8.32L12.5,12L4.6,2.15M4.6,21.85L12.5,12L15.39,15.68L4.6,21.85Z" />
                                </svg>
                            </div>
                            <div className="text-left">
                                <div className="text-[10px] text-zinc-500 uppercase font-bold tracking-widest">Get it on</div>
                                <div className="text-lg font-bold text-white leading-tight">Google Play</div>
                            </div>
                        </button>
                    </div>
                </div>

                <div className="relative flex justify-center">
                     {/* Floating iPhone mockups */}
                     <div className="relative h-[500px] w-full max-w-[400px]">
                         <div className="absolute top-0 right-0 h-[450px] w-[220px] bg-zinc-800 rounded-[40px] border-4 border-zinc-700 shadow-2xl rotate-6 z-20 overflow-hidden">
                              <div className="h-full w-full bg-gradient-to-br from-blue-600 to-indigo-700 p-4 pt-10">
                                  <div className="h-full w-full bg-white/10 rounded-2xl animate-pulse" />
                              </div>
                         </div>
                         <div className="absolute bottom-0 left-0 h-[450px] w-[220px] bg-zinc-800 rounded-[40px] border-4 border-zinc-700 shadow-2xl -rotate-12 z-10 overflow-hidden opacity-80">
                              <div className="h-full w-full bg-zinc-900 flex items-center justify-center">
                                  <div className="h-10 w-10 text-white/20">
                                      <Smartphone className="h-full w-full" />
                                  </div>
                              </div>
                         </div>
                     </div>
                </div>
            </div>
        </section>
    )
}
