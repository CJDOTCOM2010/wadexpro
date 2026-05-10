'use client'

import React, { useState } from 'react'
import { ChevronDown, HelpCircle } from 'lucide-react'

interface FAQItem {
    id: string
    question: string
    answer: string
}

interface FAQProps {
    title: string
    faqs: FAQItem[]
}

export const FAQSection: React.FC<FAQProps> = ({ title, faqs }) => {
    const [openIndex, setOpenIndex] = useState<number | null>(0)

    return (
        <section className="bg-zinc-50 py-24">
            <div className="section-container max-w-4xl">
                <div className="text-center space-y-4 mb-16">
                    <div className="h-12 w-12 bg-primary-gold/10 text-primary-gold rounded-full flex items-center justify-center mx-auto">
                        <HelpCircle className="h-6 w-6" />
                    </div>
                    <h2 className="text-4xl lg:text-5xl">{title}</h2>
                    <p className="text-zinc-500">Everything you need to know about getting started with WADEXP.</p>
                </div>

                <div className="space-y-4">
                    {faqs.map((faq, index) => (
                        <div 
                            key={faq.id} 
                            className={`bg-white rounded-3xl border transition-all duration-300 ${
                                openIndex === index ? 'border-blue-500/30 shadow-xl' : 'border-zinc-100 hover:border-zinc-200 shadow-sm'
                            }`}
                        >
                            <button 
                                onClick={() => setOpenIndex(openIndex === index ? null : index)}
                                className="w-full px-8 py-6 flex items-center justify-between text-left"
                            >
                                <span className={`font-bold text-lg transition-colors ${openIndex === index ? 'text-blue-600' : 'text-primary-navy'}`}>
                                    {faq.question}
                                </span>
                                <ChevronDown className={`h-5 w-5 text-zinc-400 transition-transform duration-300 ${openIndex === index ? 'rotate-180' : ''}`} />
                            </button>
                            
                            <div className={`overflow-hidden transition-all duration-300 ${openIndex === index ? 'max-h-96 opacity-100' : 'max-h-0 opacity-0'}`}>
                                <div className="px-8 pb-8 text-zinc-500 leading-relaxed border-t border-zinc-50 pt-4">
                                    {faq.answer}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                <div className="mt-16 p-8 bg-blue-600 rounded-[40px] text-center space-y-4">
                    <h3 className="text-2xl font-bold text-white">Still have questions?</h3>
                    <p className="text-blue-100 italic">Our support team is available 24/7 to assist you.</p>
                    <button className="bg-white text-blue-600 px-8 py-3 rounded-full font-bold hover:bg-zinc-100 transition-colors">
                        Contact Support
                    </button>
                </div>
            </div>
        </section>
    )
}
