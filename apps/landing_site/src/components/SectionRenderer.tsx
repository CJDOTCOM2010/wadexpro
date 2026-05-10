import { HeroSection } from './sections/HeroSection'
import { ServicesSection } from './sections/ServicesSection'
import { BenefitsSection } from './sections/BenefitsSection'
import { HowItWorksSection } from './sections/HowItWorksSection'
import { FAQSection } from './sections/FAQSection'
import { BusinessSection } from './sections/BusinessSection'
import { DriverOnboardingSection } from './sections/DriverOnboardingSection'
import { TestimonialsSection } from './sections/TestimonialsSection'
import { RegionContentSection } from './sections/RegionContentSection'
import { AppDownloadSection } from './sections/AppDownloadSection'

interface SectionProps {
    type: string
    title: string | null
    blocks: any[]
}

export const SectionRenderer: React.FC<SectionProps> = ({ type, title, blocks }) => {
    // Helper to get block content
    const getBlockContent = (key: string) => {
        const block = blocks.find(b => b.key === key)
        return block?.content || ''
    }

    switch (type) {
        case 'hero':
            return (
                <HeroSection
                    title={getBlockContent('title')}
                    subtitle={getBlockContent('subtitle')}
                    cta_primary={{ 
                        text: getBlockContent('cta_ride'), 
                        url: blocks.find(b => b.key === 'cta_ride')?.link_url || '#' 
                    }}
                    cta_secondary={{ 
                        text: getBlockContent('cta_driver'), 
                        url: blocks.find(b => b.key === 'cta_driver')?.link_url || '#' 
                    }}
                />
            )

        case 'services':
            const services = blocks.filter(b => b.type === 'icon_card').map(b => ({
                id: b.id,
                title: b.content,
                description: b.properties?.description || '',
                icon: b.properties?.icon || 'package'
            }))
            return <ServicesSection title={title || 'Our Services'} services={services} />

        case 'benefits':
            const benefits = blocks.filter(b => b.type === 'icon_card').map(b => ({
                id: b.id,
                title: b.content,
                description: b.properties?.description || '',
                icon: b.properties?.icon || 'shield'
            }))
            return <BenefitsSection title={title || 'Why Choose WADEXP'} benefits={benefits} />

        case 'how_it_works':
            const steps = blocks.filter(b => b.type === 'step').map(b => ({
                id: b.id,
                number: b.properties?.step_number || 1,
                title: b.content,
                description: b.properties?.description || ''
            }))
            return <HowItWorksSection title={title || 'How It Works'} steps={steps} />

        case 'driver_onboarding':
            const stats = blocks.filter(b => b.type === 'stat').map(b => ({
                id: b.id,
                label: b.properties?.label || '',
                value: b.content
            }))
            return (
                <DriverOnboardingSection 
                    title={getBlockContent('title')}
                    subtitle={getBlockContent('subtitle')}
                    stats={stats}
                    cta_text={getBlockContent('cta') || 'Join Now'}
                    cta_url={blocks.find(b => b.key === 'cta')?.link_url || '#'}
                />
            )

        case 'business':
            return (
                <BusinessSection 
                    title={getBlockContent('title')}
                    description={getBlockContent('description') || getBlockContent('paragraph')}
                    cta_text={getBlockContent('cta') || 'Contact Sales'}
                    cta_url={blocks.find(b => b.key === 'cta')?.link_url || '#'}
                />
            )

        case 'faq':
            const faqs = blocks.filter(b => b.type === 'faq_item').map(b => ({
                id: b.id,
                question: b.content,
                answer: b.properties?.answer || ''
            }))
            return <FAQSection title={title || 'Frequently Asked Questions'} faqs={faqs} />

        case 'testimonials':
            const testimonials = blocks.filter(b => b.type === 'testimonial').map(b => ({
                id: b.id,
                name: b.content,
                role: b.properties?.role || '',
                content: b.properties?.content || '',
                avatar: b.properties?.avatar
            }))
            return <TestimonialsSection title={title || 'What Our Users Say'} testimonials={testimonials} />

        case 'region_content':
            return (
                <RegionContentSection 
                    title={title || 'Available Globally'}
                    content={getBlockContent('cities') || getBlockContent('paragraph')}
                    payment_info={getBlockContent('payment_info') || ''}
                />
            )

        case 'app_download':
            return (
                <AppDownloadSection
                    title={getBlockContent('title')}
                    subtitle={getBlockContent('subtitle')}
                />
            )

        // Add more section types as needed (benefits, how-it-works, etc.)
        default:
            return (
                <div className="section-container border-t border-zinc-100">
                    <h2 className="text-2xl font-bold mb-4">{title || 'Unimplemented Section'}</h2>
                    <p className="text-zinc-500">Section type: <code className="bg-zinc-100 px-1 rounded">{type}</code></p>
                </div>
            )
    }
}
