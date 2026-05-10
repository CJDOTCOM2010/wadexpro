import React from 'react'
import { MapPin, UserCheck, ShieldCheck, CheckCircle } from 'lucide-react'

interface Step {
    id: string
    number: number
    title: string
    description: string
}

interface HowItWorksProps {
    title: string
    steps: Step[]
}

export const HowItWorksSection: React.FC<HowItWorksProps> = ({ title, steps }) => {
    return (
        <section className="bg-zinc-50 py-24 relative overflow-hidden">
             {/* Background Line */}
             <div className="hidden lg:block absolute top-[60%] left-[10%] right-[10%] h-1 bg-zinc-200 -z-0" />

            <div className="section-container relative z-10">
                <div className="text-center space-y-4 mb-20">
                    <h2 className="text-4xl lg:text-5xl">{title}</h2>
                    <p className="text-zinc-500 max-w-2xl mx-auto italic">Getting from A to B has never been this seamless.</p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                    {steps.map((step, index) => (
                        <div key={step.id} className="relative bg-white p-8 rounded-[32px] border border-zinc-100 shadow-sm hover:shadow-xl transition-all group">
                            <div className="h-16 w-16 bg-primary-gold text-white rounded-2xl flex items-center justify-center text-2xl font-black mb-8 group-hover:scale-110 transition-transform">
                                {step.number}
                            </div>
                            <h3 className="text-2xl mb-4 font-bold text-primary-navy">{step.title}</h3>
                            <p className="text-zinc-500 leading-relaxed">
                                {step.description}
                            </p>
                            
                            {/* Decorative Checkmark */}
                            <div className="absolute -top-3 -right-3 h-8 w-8 bg-emerald-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <CheckCircle className="h-4 w-4" />
                            </div>
                        </div>
                    ))}
                </div>

                <div className="mt-20 text-center">
                    <div className="inline-flex items-center gap-3 px-6 py-3 bg-white border border-zinc-200 rounded-full text-sm font-bold shadow-sm">
                        <ShieldCheck className="h-5 w-5 text-emerald-500" />
                        Safety verified at every step of your journey
                    </div>
                </div>
            </div>
        </section>
    )
}
