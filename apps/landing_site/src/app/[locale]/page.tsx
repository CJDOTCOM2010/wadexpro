import { Navigation } from '@/components/Navigation'
import { SectionRenderer } from '@/components/SectionRenderer'
import { cmsApi } from '@/lib/cms'
import { notFound } from 'next/navigation'

interface PageProps {
    params: {
        locale: string
    }
}

export default async function LandingPage({ params }: PageProps) {
    const { locale } = params

    // Fetch the 'home' page data from the CMS
    let pageData = null;
    try {
        const res = await cmsApi.getPage('home', locale)
        pageData = res.data.data
    } catch (err) {
        console.error('Failed to fetch CMS page:', err)
        // Fallback or 404
    }

    if (!pageData) {
        // Return a basic fallback hero if the backend is not seeded yet
        return (
            <div className="min-h-screen">
                <Navigation locale={locale} />
                <div className="section-container pt-40 text-center space-y-8">
                    <h1 className="text-6xl">WADEXP Mobility</h1>
                    <p className="text-zinc-500 text-xl">The platform is currently initializing...</p>
                    <div className="flex justify-center gap-4">
                        <div className="btn-accent px-8 py-3">Launching Soon</div>
                    </div>
                </div>
            </div>
        )
    }

    return (
        <div className="min-h-screen">
            <Navigation locale={locale} />
            
            <main>
                {pageData.sections.map((section: any) => (
                    <SectionRenderer 
                        key={section.id}
                        type={section.type}
                        title={section.title}
                        blocks={section.blocks}
                    />
                ))}
            </main>

            <footer className="bg-primary-navy border-t border-zinc-800 py-20">
                <div className="section-container grid grid-cols-1 md:grid-cols-4 gap-12">
                    <div className="col-span-1 md:col-span-1 space-y-6">
                         <div className="h-12 w-12 bg-white/10 rounded-xl flex items-center justify-center text-primary-gold font-black text-2xl">
                             W
                         </div>
                         <p className="text-zinc-500 text-sm leading-relaxed">
                             Nigeria & Ghana's premier enterprise-grade logistics and mobility platform. Go anywhere, send anything.
                         </p>
                    </div>
                    {['Product', 'Company', 'Support'].map(group => (
                        <div key={group} className="space-y-6">
                            <h4 className="text-white font-bold uppercase tracking-widest text-xs">{group}</h4>
                            <ul className="space-y-4 text-sm text-zinc-500">
                                <li><a href="#" className="hover:text-blue-500 transition-colors">Link Item 1</a></li>
                                <li><a href="#" className="hover:text-blue-500 transition-colors">Link Item 2</a></li>
                                <li><a href="#" className="hover:text-blue-500 transition-colors">Link Item 3</a></li>
                            </ul>
                        </div>
                    ))}
                </div>
                <div className="section-container pt-12 mt-12 border-t border-zinc-800 text-center">
                    <p className="text-xs text-zinc-600">
                        © {new Date().getFullYear()} WADEXP Enterprise Mobility Platform. All rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    )
}
