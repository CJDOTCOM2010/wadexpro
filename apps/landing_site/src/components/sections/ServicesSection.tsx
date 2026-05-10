import React from 'react'

interface ServiceItem {
    id: string
    title: string
    description: string
    icon: string
}

interface ServicesProps {
    title: string
    services: ServiceItem[]
}

export const ServicesSection: React.FC<ServicesProps> = ({ title, services }) => {
    return (
        <section className="bg-zinc-50 py-24">
            <div className="section-container">
                <div className="text-center space-y-4 mb-16">
                    <h2 className="text-4xl lg:text-5xl">{title}</h2>
                    <div className="h-1.5 w-24 bg-primary-gold mx-auto rounded-full" />
                    <p className="text-zinc-500 max-w-2xl mx-auto">
                        Whether you need to get across town, send a document, or move bulk goods, WADEXP has the perfect solution.
                    </p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {services.map((service, index) => (
                        <div 
                            key={service.id} 
                            className="group bg-white p-8 rounded-[32px] border border-zinc-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500"
                        >
                            <div className="h-16 w-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-500">
                                {/* Simple Icon Mapping Placeholder */}
                                <ServiceIcon name={service.icon} />
                            </div>
                            <h3 className="text-2xl mb-4 text-primary-navy">{service.title}</h3>
                            <p className="text-zinc-500 leading-relaxed mb-6">
                                {service.description}
                            </p>
                            <button className="flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors uppercase tracking-widest">
                                Learn More
                                <ArrowRight className="h-4 w-4" />
                            </button>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    )
}

const ServiceIcon = ({ name }: { name: string }) => {
    // Icons mapping
    return (
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            {name === 'car' && <><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></>}
            {name === 'package' && <><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></>}
            {name === 'bike' && <><circle cx="18.5" cy="17.5" r="3.5"/><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="15" cy="5" r="1"/><path d="M12 17.5V14l-3-3 4-3 2 3h2"/></>}
        </svg>
    )
}

const ArrowRight = ({ className }: { className?: string }) => (
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
)
