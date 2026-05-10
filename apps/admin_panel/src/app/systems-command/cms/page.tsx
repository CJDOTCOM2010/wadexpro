'use client'

import { useState, useEffect } from 'react'
import DashboardLayout from '@/components/DashboardLayout'
import axios from '@/lib/axios'
import { Globe, Plus, Save, Trash2, Layout, Image as ImageIcon, Layers, Languages, Monitor, Smartphone, CheckCircle2, AlertCircle } from 'lucide-react'
import { Button } from '@/components/ui/button'
import StepUpGuard from '@/components/StepUpGuard'

export default function CmsOrchestratorPage() {
    const [regions, setRegions] = useState<any[]>([])
    const [selectedRegion, setSelectedRegion] = useState<any>(null)
    const [sections, setSections] = useState<any[]>([])
    const [isSaving, setIsSaving] = useState(false)
    const [notification, setNotification] = useState<{ type: 'success' | 'error', message: string } | null>(null)

    useEffect(() => {
        fetchRegions()
    }, [])

    const fetchRegions = async () => {
        try {
            const res = await axios.get('/api/v1/orchestrator/cms/regions')
            setRegions(res.data)
            if (res.data.length > 0 && !selectedRegion) {
                setSelectedRegion(res.data[0])
                fetchSections(res.data[0].id)
            }
        } catch (err) {
            console.error('Failed to fetch regions', err)
        }
    }

    const fetchSections = async (regionId: number) => {
        try {
            const res = await axios.get(`/api/v1/orchestrator/cms/sections?region_id=${regionId}`)
            setSections(res.data)
        } catch (err) {
            console.error('Failed to fetch sections', err)
        }
    }

    const handleRegionSelect = (region: any) => {
        setSelectedRegion(region)
        fetchSections(region.id)
    }

    const updateSection = (index: number, content: any) => {
        const newSections = [...sections]
        newSections[index].content = content
        setSections(newSections)
    }

    const saveChanges = async () => {
        setIsSaving(true)
        try {
            for (const section of sections) {
                await axios.post('/api/v1/orchestrator/cms/sections', section)
            }
            setNotification({ type: 'success', message: 'CMS content synchronized successfully!' })
        } catch (err) {
            setNotification({ type: 'error', message: 'Failed to synchronize content. Please check logs.' })
        } finally {
            setIsSaving(false)
            setTimeout(() => setNotification(null), 3000)
        }
    }

    return (
        <DashboardLayout>
            <StepUpGuard>
                <div className="max-w-7xl mx-auto space-y-8 pb-20">
                    <header className="flex justify-between items-end border-b border-zinc-200 dark:border-zinc-800 pb-8">
                        <div>
                             <div className="flex items-center gap-2 mb-2">
                                 <span className="px-2 py-0.5 bg-indigo-600 text-[10px] font-bold text-white rounded uppercase tracking-tighter">Design Orchestrator</span>
                                 <span className="text-zinc-400 text-xs">/ Systems Command</span>
                            </div>
                            <h1 className="text-4xl font-black tracking-tight text-zinc-900 dark:text-zinc-50 flex items-center gap-3">
                                <Layout className="w-10 h-10 text-indigo-600" />
                                CMS Orchestrator
                            </h1>
                            <p className="mt-2 text-lg text-zinc-600 dark:text-zinc-400">
                                Dynamically manage landing page content, regional branding, and service segments.
                            </p>
                        </div>
                        <div className="flex gap-4">
                             {notification && (
                                <div className={`flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold ${notification.type === 'success' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200'}`}>
                                    {notification.type === 'success' ? <CheckCircle2 className="w-4 h-4" /> : <AlertCircle className="w-4 h-4" />}
                                    {notification.message}
                                </div>
                            )}
                            <Button 
                                onClick={saveChanges}
                                disabled={isSaving}
                                className="bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl h-14 px-10 font-black uppercase tracking-widest text-xs shadow-2xl shadow-indigo-500/20"
                            >
                                <Save className="w-4 h-4 mr-2" />
                                {isSaving ? 'Synchronizing...' : 'Sync to Public'}
                            </Button>
                        </div>
                    </header>

                    <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
                        {/* Sidebar: Regions */}
                        <div className="lg:col-span-1 space-y-6">
                            <div className="bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm">
                                <div className="flex items-center justify-between mb-6">
                                    <h3 className="text-sm font-black text-zinc-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                                        <Globe className="w-4 h-4 text-indigo-500" />
                                        Regions
                                    </h3>
                                    <Button size="icon" variant="ghost" className="rounded-full w-8 h-8">
                                        <Plus className="w-4 h-4" />
                                    </Button>
                                </div>
                                <div className="space-y-2">
                                    {regions.map(region => (
                                        <button
                                            key={region.id}
                                            onClick={() => handleRegionSelect(region)}
                                            className={`w-full flex items-center justify-between p-4 rounded-2xl border transition-all ${selectedRegion?.id === region.id ? 'bg-indigo-50 border-indigo-200 dark:bg-indigo-900/20 dark:border-indigo-800 text-indigo-600' : 'bg-zinc-50 border-zinc-100 dark:bg-zinc-800/50 dark:border-zinc-800 text-zinc-500'}`}
                                        >
                                            <span className="text-xs font-black uppercase tracking-tighter">{region.name} ({region.code})</span>
                                            <div className={`w-2 h-2 rounded-full ${region.is_active ? 'bg-emerald-500' : 'bg-zinc-300'}`} />
                                        </button>
                                    ))}
                                </div>
                            </div>

                            <div className="bg-zinc-950 rounded-[2rem] border border-zinc-800 p-6 shadow-2xl space-y-4">
                                <h3 className="text-[10px] font-black text-zinc-500 uppercase tracking-[.2em]">Deployment Info</h3>
                                <div className="space-y-3">
                                    <div className="flex justify-between items-center text-[10px]">
                                        <span className="text-zinc-500">Public CDN</span>
                                        <span className="font-bold text-white uppercase">Cloudflare Edge</span>
                                    </div>
                                    <div className="flex justify-between items-center text-[10px]">
                                        <span className="text-zinc-500">Last Sync</span>
                                        <span className="font-bold text-indigo-400">14:02 Today</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Content Editor */}
                        <div className="lg:col-span-3 space-y-8">
                            {sections.map((section, idx) => (
                                <div key={section.id} className="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 p-10 shadow-sm space-y-8 relative group">
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-4">
                                            <div className="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl text-indigo-600">
                                                <Layers className="w-6 h-6" />
                                            </div>
                                            <div>
                                                <h2 className="text-xl font-black text-zinc-900 dark:text-white uppercase tracking-tighter">
                                                    {section.section_key.replace('_', ' ')} Section
                                                </h2>
                                                <p className="text-xs text-zinc-500">Mapping: /{selectedRegion?.code}/{section.lang_code}/{section.section_key}</p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <Button variant="ghost" size="icon" className="rounded-full"><Monitor className="w-4 h-4" /></Button>
                                            <Button variant="ghost" size="icon" className="rounded-full"><Smartphone className="w-4 h-4" /></Button>
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                                        {/* Dynamic Fields based on section_key */}
                                        <div className="space-y-4">
                                            <label className="text-[10px] font-black text-zinc-500 uppercase tracking-widest px-1">Headline Content</label>
                                            <input 
                                                value={section.content.headline || ''} 
                                                onChange={(e) => updateSection(idx, { ...section.content, headline: e.target.value })}
                                                className="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-800 p-4 rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none"
                                            />
                                        </div>
                                        <div className="space-y-4">
                                            <label className="text-[10px] font-black text-zinc-500 uppercase tracking-widest px-1">Primary CTA Link</label>
                                            <input 
                                                value={section.content.cta_link || ''} 
                                                onChange={(e) => updateSection(idx, { ...section.content, cta_link: e.target.value })}
                                                className="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-800 p-4 rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none"
                                            />
                                        </div>
                                        <div className="md:col-span-2 space-y-4">
                                            <label className="text-[10px] font-black text-zinc-500 uppercase tracking-widest px-1">Hero / Background Image URL</label>
                                            <div className="flex gap-4">
                                                <input 
                                                    value={section.content.image_url || ''} 
                                                    onChange={(e) => updateSection(idx, { ...section.content, image_url: e.target.value })}
                                                    className="flex-1 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-800 p-4 rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none"
                                                />
                                                <Button variant="outline" className="rounded-xl h-14 w-14 p-0 border-zinc-200 dark:border-zinc-800">
                                                    <ImageIcon className="w-5 h-5" />
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}

                            {sections.length === 0 && (
                                <div className="bg-zinc-50 dark:bg-zinc-900/50 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-20 text-center space-y-6">
                                    <div className="w-20 h-20 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto text-zinc-400">
                                        <Layers className="w-10 h-10" />
                                    </div>
                                    <div>
                                        <h3 className="text-xl font-bold text-zinc-900 dark:text-white tracking-tighter">No Sections Configured</h3>
                                        <p className="text-zinc-500 text-sm mt-3 max-w-sm mx-auto">Select a region or add a new content segment to start orchestrating your public presence.</p>
                                    </div>
                                    <Button variant="outline" className="rounded-2xl h-12 px-8 font-black uppercase tracking-widest text-[10px] border-zinc-200 dark:border-zinc-800">
                                        Initialize Default Segments
                                    </Button>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </StepUpGuard>
        </DashboardLayout>
    )
}
