import React from 'react'
import { Quote, Star } from 'lucide-react'

interface Testimonial {
    id: string
    name: string
    role: string
    content: string
    avatar?: string
}

interface TestimonialsProps {
    title: string
    testimonials: Testimonial[]
}

export const TestimonialsSection: React.FC<TestimonialsProps> = ({ title, testimonials }) => {
    return (
        <section className="bg-white py-24 overflow-hidden">
            <div className="section-container">
                <div className="text-center space-y-4 mb-16">
                    <h2 className="text-4xl lg:text-5xl">{title}</h2>
                    <div className="flex items-center justify-center gap-1">
                        {[1, 2, 3, 4, 5].map(i => <Star key={i} className="h-5 w-5 fill-primary-gold text-primary-gold" />)}
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {testimonials.map((t) => (
                        <div key={t.id} className="bg-zinc-50 p-8 rounded-[40px] relative group hover:bg-white hover:shadow-2xl hover:border-zinc-100 border border-transparent transition-all duration-500">
                             <Quote className="absolute top-6 right-8 h-10 w-10 text-zinc-200 group-hover:text-blue-500/20 transition-colors" />
                             <p className="text-zinc-600 leading-relaxed mb-8 relative z-10 italic">
                                 "{t.content}"
                             </p>
                             <div className="flex items-center gap-4">
                                 <div className="h-12 w-12 rounded-full bg-zinc-200 overflow-hidden">
                                     {t.avatar ? (
                                         <img src={t.avatar} alt={t.name} className="h-full w-full object-cover" />
                                     ) : (
                                         <div className="h-full w-full bg-gradient-to-br from-blue-500 to-indigo-600" />
                                     )}
                                 </div>
                                 <div>
                                     <div className="font-bold text-primary-navy">{t.name}</div>
                                     <div className="text-xs text-zinc-500 uppercase tracking-widest font-bold">{t.role}</div>
                                 </div>
                             </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    )
}
