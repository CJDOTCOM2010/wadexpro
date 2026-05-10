import React from 'react'

interface BenefitItem {
    id: string
    title: string
    description: string
    icon: string
}

interface BenefitsProps {
    title: string
    benefits: BenefitItem[]
}

export const BenefitsSection: React.FC<BenefitsProps> = ({ title, benefits }) => {
    return (
        <section className="bg-white py-24">
            <div className="section-container">
                <div className="flex flex-col lg:flex-row items-center gap-16">
                    <div className="lg:w-1/2 space-y-8">
                        <h2 className="text-4xl lg:text-5xl leading-tight">
                            {title}
                        </h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-8">
                            {benefits.map((benefit) => (
                                <div key={benefit.id} className="space-y-4">
                                    <div className="h-12 w-12 bg-zinc-100 rounded-xl flex items-center justify-center text-primary-gold">
                                         <BenefitIcon name={benefit.icon} />
                                    </div>
                                    <h4 className="text-xl font-bold">{benefit.title}</h4>
                                    <p className="text-zinc-500 text-sm leading-relaxed">
                                        {benefit.description}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                    
                    <div className="lg:w-1/2 relative">
                         <div className="aspect-square bg-zinc-50 rounded-[64px] overflow-hidden">
                             {/* Placeholder for benefit illustration */}
                             <div className="h-full w-full bg-gradient-to-tr from-blue-600/10 to-primary-gold/10 flex items-center justify-center p-12">
                                  <div className="h-full w-full bg-white rounded-3xl shadow-2xl flex flex-col p-8 space-y-4">
                                       <div className="h-8 w-1/2 bg-zinc-100 rounded" />
                                       <div className="h-20 w-full bg-zinc-50 rounded" />
                                       <div className="flex-1 w-full bg-zinc-100 rounded-xl" />
                                  </div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </section>
    )
}

const BenefitIcon = ({ name }: { name: string }) => {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            {name === 'shield' && <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>}
            {name === 'wallet' && <><path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"/><path d="M4 6v12c0 1.1.9 2 2 2h14v-4"/><path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4Z"/></>}
            {name === 'clock' && <><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></>}
            {name === 'map-pin' && <><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></>}
        </svg>
    )
}
