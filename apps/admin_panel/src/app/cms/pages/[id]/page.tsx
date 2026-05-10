'use client'

import { useState, useEffect } from 'react'
import { useParams, useRouter } from 'next/navigation'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import {
    Plus,
    Trash2,
    Save,
    ArrowLeft,
    GripVertical,
    ChevronDown,
    ChevronUp,
    Settings2,
    Globe,
    Layout,
    Type,
    Image as ImageIcon,
    ExternalLink,
    Clock,
    Check
} from 'lucide-react'
import { Button } from '@/components/ui/button'

interface Translation {
    [key: string]: string
}

interface CmsBlock {
    id: string
    section_id: string
    type: string
    key: string | null
    content: Translation | null
    media_url: string | null
    link_url: string | null
    link_text: Translation | null
    sort_order: number
    properties: any
    updated_at: string
}

interface CmsSection {
    id: string
    page_id: string
    type: string
    title: string | null
    sort_order: number
    is_visible: boolean
    settings: any
    blocks: CmsBlock[]
}

interface CmsPage {
    id: string
    title: Translation
    slug: string
    meta_description: Translation | null
    status: string
    template: string
    sections: CmsSection[]
}

const SUPPORTED_LOCALES = ['en', 'fr', 'es']

export default function CmsPageEditor() {
    const params = useParams()
    const router = useRouter()
    const pageId = params.id as string

    const [page, setPage] = useState<CmsPage | null>(null)
    const [loading, setLoading] = useState(true)
    const [activeLocale, setActiveLocale] = useState('en')
    const [saving, setSaving] = useState(false)
    const [sectionTypes, setSectionTypes] = useState<Record<string, string>>({})
    const [blockTypes, setBlockTypes] = useState<Record<string, string>>({})

    const fetchPage = async () => {
        try {
            const res = await adminApi.getCmsPage(pageId)
            setPage(res.data.data)
        } catch (err) {
            console.error(err)
        } finally {
            setLoading(false)
        }
    }

    const fetchTypes = async () => {
        try {
            const res = await adminApi.getSectionTypes()
            setSectionTypes(res.data.data.section_types)
            setBlockTypes(res.data.data.block_types)
        } catch (err) {
            console.error(err)
        }
    }

    useEffect(() => {
        fetchPage()
        fetchTypes()
    }, [pageId])

    const handleUpdatePageField = (field: keyof CmsPage, value: any, isTranslatable = false) => {
        if (!page) return
        if (isTranslatable) {
            setPage({
                ...page,
                [field]: { ...(page[field] as any || {}), [activeLocale]: value }
            })
        } else {
            setPage({ ...page, [field]: value })
        }
    }

    const handleUpdateBlockContent = (sectionId: string, blockId: string, value: string) => {
        if (!page) return
        const newSections = page.sections.map(sec => {
            if (sec.id !== sectionId) return sec
            return {
                ...sec,
                blocks: sec.blocks.map(block => {
                    if (block.id !== blockId) return block
                    return {
                        ...block,
                        content: { ...(block.content || {}), [activeLocale]: value }
                    }
                })
            }
        })
        setPage({ ...page, sections: newSections })
    }

    const handleUpdateBlockField = (sectionId: string, blockId: string, field: keyof CmsBlock, value: any) => {
        if (!page) return
        const newSections = page.sections.map(sec => {
            if (sec.id !== sectionId) return sec
            return {
                ...sec,
                blocks: sec.blocks.map(block => {
                    if (block.id !== blockId) return block
                    return { ...block, [field]: value }
                })
            }
        })
        setPage({ ...page, sections: newSections })
    }

    const savePageDetails = async () => {
        if (!page) return
        setSaving(true)
        try {
            await adminApi.updateCmsPage(pageId, {
                title: page.title,
                meta_description: page.meta_description,
                status: page.status,
                template: page.template
            })
            // Success toast?
        } catch (err) {
            console.error(err)
        } finally {
            setSaving(false)
        }
    }

    const deleteSection = async (sectionId: string) => {
        if (!confirm('Delete this section and all its blocks?')) return
        try {
            await adminApi.deleteSection(sectionId)
            fetchPage()
        } catch (err) {
            console.error(err)
        }
    }

    const addSection = async () => {
        const type = prompt('Section type (hero, services, features, etc.):', 'features')
        if (!type) return
        try {
            await adminApi.createSection(pageId, { type, title: 'New Section' })
            fetchPage()
        } catch (err) {
            console.error(err)
        }
    }

    const addBlock = async (sectionId: string) => {
        const type = prompt('Block type (heading, paragraph, image, button):', 'paragraph')
        if (!type) return
        try {
            await adminApi.createBlock(sectionId, { type, content: { [activeLocale]: 'New block content' } })
            fetchPage()
        } catch (err) {
            console.error(err)
        }
    }

    const saveBlock = async (block: CmsBlock) => {
        try {
            await adminApi.updateBlock(block.id, {
                content: block.content,
                media_url: block.media_url,
                link_url: block.link_url,
                link_text: block.link_text,
                properties: block.properties
            })
        } catch (err) {
            console.error(err)
        }
    }

    if (loading) return (
        <DashboardLayout>
            <div className="flex h-[400px] items-center justify-center">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>
        </DashboardLayout>
    )

    if (!page) return <div>Page not found</div>

    return (
        <DashboardLayout>
            <div className="space-y-6 max-w-5xl mx-auto pb-20">
                {/* Header Actions */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-0 bg-[#0a0a0a]/80 backdrop-blur-md z-40 py-4 -mx-4 px-4 border-b border-zinc-800">
                    <div className="flex items-center gap-3">
                        <button onClick={() => router.push('/cms/pages')} className="p-2 hover:bg-zinc-800 rounded-full transition-colors">
                            <ArrowLeft className="h-5 w-5" />
                        </button>
                        <div>
                            <h1 className="text-xl font-bold truncate">
                                {page.title[activeLocale] || page.title['en'] || 'Untitled Page'}
                            </h1>
                            <p className="text-xs text-zinc-500 font-mono">/{page.slug}</p>
                        </div>
                    </div>
                    
                    <div className="flex items-center gap-2">
                        {/* Locale Switcher */}
                        <div className="flex bg-zinc-900 border border-zinc-800 rounded-lg p-1 mr-2">
                            {SUPPORTED_LOCALES.map(loc => (
                                <button
                                    key={loc}
                                    onClick={() => setActiveLocale(loc)}
                                    className={`px-3 py-1 text-xs font-bold rounded-md uppercase transition-all ${
                                        activeLocale === loc 
                                        ? 'bg-blue-600 text-white' 
                                        : 'text-zinc-500 hover:text-zinc-300'
                                    }`}
                                >
                                    {loc}
                                </button>
                            ))}
                        </div>

                        <Button 
                            onClick={savePageDetails} 
                            disabled={saving}
                            className="bg-emerald-600 hover:bg-emerald-700 text-white gap-2"
                        >
                            <Save className={`h-4 w-4 ${saving ? 'animate-pulse' : ''}`} />
                            {saving ? 'Saving...' : 'Save Meta'}
                        </Button>
                    </div>
                </div>

                {/* Page Meta Settings */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6 bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-sm">
                    <div className="md:col-span-2 space-y-4">
                        <div>
                            <label className="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1.5 block">Page Title ({activeLocale})</label>
                            <input
                                type="text"
                                value={page.title[activeLocale] || ''}
                                onChange={(e) => handleUpdatePageField('title', e.target.value, true)}
                                className="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-2.5 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all"
                            />
                        </div>
                        <div>
                            <label className="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1.5 block">Meta Description ({activeLocale})</label>
                            <textarea
                                rows={2}
                                value={page.meta_description?.[activeLocale] || ''}
                                onChange={(e) => handleUpdatePageField('meta_description', e.target.value, true)}
                                className="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-2.5 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all resize-none"
                            />
                        </div>
                    </div>
                    <div className="space-y-4">
                        <div>
                            <label className="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1.5 block">Status</label>
                            <select
                                value={page.status}
                                onChange={(e) => handleUpdatePageField('status', e.target.value)}
                                className="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-2.5 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50 appearance-none transition-all"
                            >
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div>
                            <label className="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1.5 block">Template</label>
                            <select
                                value={page.template}
                                onChange={(e) => handleUpdatePageField('template', e.target.value)}
                                className="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-2.5 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50 appearance-none transition-all"
                            >
                                <option value="landing">Landing Page</option>
                                <option value="minimal">Minimal</option>
                                <option value="auth">Auth Template</option>
                            </select>
                        </div>
                    </div>
                </div>

                {/* Sections List */}
                <div className="space-y-8">
                    <div className="flex items-center justify-between border-b border-zinc-800 pb-2">
                        <h2 className="text-lg font-bold flex items-center gap-2">
                            <Layout className="h-5 w-5 text-blue-500" />
                            Page Sections
                        </h2>
                        <Button onClick={addSection} size="sm" variant="outline" className="gap-2 text-blue-500 border-blue-500/30 hover:bg-blue-500/10">
                            <Plus className="h-4 w-4" />
                            Add Section
                        </Button>
                    </div>

                    {page.sections.map((section) => (
                        <div key={section.id} className="group relative bg-zinc-950 border border-zinc-800 rounded-2xl overflow-hidden animate-in fade-in slide-in-from-bottom-2 duration-300">
                            {/* Section Header */}
                            <div className="bg-zinc-900 px-6 py-3 flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    <GripVertical className="h-4 w-4 text-zinc-600 cursor-move" />
                                    <span className="px-2 py-0.5 bg-zinc-800 rounded text-[10px] font-bold uppercase text-zinc-400">
                                        {section.type}
                                    </span>
                                    <h3 className="font-semibold text-sm text-zinc-300">{section.title}</h3>
                                </div>
                                <div className="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <Button size="icon" variant="ghost" className="h-8 w-8 text-zinc-500 hover:text-white">
                                        <Settings2 className="h-4 w-4" />
                                    </Button>
                                    <Button 
                                        size="icon" 
                                        variant="ghost" 
                                        onClick={() => deleteSection(section.id)}
                                        className="h-8 w-8 text-zinc-500 hover:text-red-500"
                                    >
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>

                            {/* Blocks in Section */}
                            <div className="p-6 space-y-4">
                                {section.blocks.map((block) => (
                                    <div key={block.id} className="bg-zinc-900/50 border border-zinc-800/50 rounded-xl p-4 hover:border-blue-500/30 transition-all">
                                        <div className="flex items-center justify-between mb-3">
                                            <div className="flex items-center gap-2">
                                                {block.type === 'heading' && <Type className="h-3.5 w-3.5 text-blue-400" />}
                                                {block.type === 'image' && <ImageIcon className="h-3.5 w-3.5 text-emerald-400" />}
                                                {block.type === 'button' && <ExternalLink className="h-3.5 w-3.5 text-amber-400" />}
                                                <span className="text-[10px] font-bold text-zinc-500 tracking-widest uppercase">{block.type}</span>
                                                {block.key && <span className="text-[10px] text-zinc-600 font-mono">({block.key})</span>}
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <span className="text-[10px] text-zinc-600 flex items-center gap-1">
                                                    <Clock className="h-3 w-3" />
                                                    {new Date(block.updated_at).toLocaleTimeString()}
                                                </span>
                                                <button 
                                                    onClick={() => saveBlock(block)} 
                                                    className="p-1 hover:bg-zinc-800 rounded-md text-emerald-500 transition-colors"
                                                >
                                                    <Check className="h-3.5 w-3.5" />
                                                </button>
                                            </div>
                                        </div>

                                        {/* Block Editor Fields */}
                                        <div className="space-y-3">
                                            {/* Content textarea for heading/paragraph */}
                                            {(['heading', 'paragraph', 'rich_text', 'icon_card', 'step', 'stat'].includes(block.type)) && (
                                                <textarea
                                                    rows={block.type === 'paragraph' ? 3 : 1}
                                                    value={block.content?.[activeLocale] || ''}
                                                    onChange={(e) => handleUpdateBlockContent(section.id, block.id, e.target.value)}
                                                    placeholder={`Enter ${block.type} content in ${activeLocale}...`}
                                                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2 text-sm text-zinc-200 focus:outline-none focus:ring-1 focus:ring-blue-500/50 transition-all"
                                                />
                                            )}

                                            {/* Media URL for images */}
                                            {block.type === 'image' && (
                                                <div className="flex gap-2">
                                                    <input
                                                        type="text"
                                                        value={block.media_url || ''}
                                                        onChange={(e) => handleUpdateBlockField(section.id, block.id, 'media_url', e.target.value)}
                                                        placeholder="Image URL..."
                                                        className="flex-1 bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2 text-sm text-zinc-200"
                                                    />
                                                    {block.media_url && (
                                                        <img src={block.media_url} alt="Preview" className="h-10 w-10 rounded object-cover border border-zinc-800" />
                                                    )}
                                                </div>
                                            )}

                                            {/* Button fields */}
                                            {block.type === 'button' && (
                                                <div className="grid grid-cols-2 gap-3">
                                                    <input
                                                        type="text"
                                                        value={block.content?.[activeLocale] || ''}
                                                        onChange={(e) => handleUpdateBlockContent(section.id, block.id, e.target.value)}
                                                        placeholder="Button Text"
                                                        className="bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2 text-sm text-zinc-200"
                                                    />
                                                    <input
                                                        type="text"
                                                        value={block.link_url || ''}
                                                        onChange={(e) => handleUpdateBlockField(section.id, block.id, 'link_url', e.target.value)}
                                                        placeholder="Link URL (e.g. /auth/login)"
                                                        className="bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2 text-sm text-zinc-200"
                                                    />
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                ))}

                                <button 
                                    onClick={() => addBlock(section.id)}
                                    className="w-full py-3 border border-dashed border-zinc-800 rounded-xl text-xs font-medium text-zinc-500 hover:border-blue-500/50 hover:text-blue-400 transition-all group/add"
                                >
                                    <div className="flex items-center justify-center gap-2">
                                        <Plus className="h-3.5 w-3.5 group-hover/add:scale-110 transition-transform" />
                                        Add Content Block
                                    </div>
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </DashboardLayout>
    )
}
