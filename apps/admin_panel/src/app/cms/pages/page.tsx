'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import {
    Plus,
    Edit,
    Trash2,
    Eye,
    Globe,
    FileText,
    GripVertical,
    ArrowUpRight,
    Check,
    X
} from 'lucide-react'
import Link from 'next/link'

interface CmsPage {
    id: string
    title: string
    slug: string
    status: 'published' | 'draft' | 'archived'
    template: string
    region: string | null
    sort_order: number
    sections_count: number
    created_at: string
    updated_at: string
}

const statusBadge: Record<string, string> = {
    published: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400',
    draft: 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400',
    archived: 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-400',
}

export default function CmsPagesPage() {
    const [pages, setPages] = useState<CmsPage[]>([])
    const [loading, setLoading] = useState(true)
    const [showCreateModal, setShowCreateModal] = useState(false)
    const [newPage, setNewPage] = useState({ title: '', slug: '', status: 'draft', template: 'default' })

    const fetchPages = () => {
        setLoading(true)
        adminApi.getCmsPages()
            .then(res => setPages(res.data.data || []))
            .catch(console.error)
            .finally(() => setLoading(false))
    }

    useEffect(() => { fetchPages() }, [])

    const createPage = async () => {
        try {
            await adminApi.createCmsPage(newPage)
            setShowCreateModal(false)
            setNewPage({ title: '', slug: '', status: 'draft', template: 'default' })
            fetchPages()
        } catch (err) {
            console.error(err)
        }
    }

    const deletePage = async (id: string) => {
        if (!confirm('Are you sure you want to delete this page?')) return
        try {
            await adminApi.deleteCmsPage(id)
            fetchPages()
        } catch (err) {
            console.error(err)
        }
    }

    return (
        <DashboardLayout>
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-zinc-900 dark:text-white">CMS Pages</h1>
                        <p className="mt-1 text-sm text-zinc-500">Manage landing page content and sections</p>
                    </div>
                    <button
                        onClick={() => setShowCreateModal(true)}
                        className="flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
                    >
                        <Plus className="h-4 w-4" />
                        New Page
                    </button>
                </div>

                {/* Pages Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {loading ? (
                        Array.from({ length: 3 }).map((_, i) => (
                            <div key={i} className="animate-pulse rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
                                <div className="h-5 bg-zinc-200 dark:bg-zinc-700 rounded w-2/3 mb-3" />
                                <div className="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-1/2 mb-4" />
                                <div className="h-8 bg-zinc-200 dark:bg-zinc-700 rounded" />
                            </div>
                        ))
                    ) : pages.length === 0 ? (
                        <div className="col-span-full flex flex-col items-center py-16">
                            <FileText className="h-12 w-12 text-zinc-300 mb-4" />
                            <p className="text-zinc-500 text-lg font-medium">No pages yet</p>
                            <p className="text-zinc-400 text-sm mb-4">Create your first CMS page to get started</p>
                            <button
                                onClick={() => setShowCreateModal(true)}
                                className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
                            >
                                <Plus className="h-4 w-4" />
                                Create Page
                            </button>
                        </div>
                    ) : (
                        pages.map((page) => (
                            <div
                                key={page.id}
                                className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5 hover:shadow-md transition-all group"
                            >
                                <div className="flex items-start justify-between mb-3">
                                    <div className="flex-1 min-w-0">
                                        <h3 className="text-base font-semibold text-zinc-900 dark:text-white truncate">{page.title}</h3>
                                        <p className="text-xs text-zinc-400 mt-1">/{page.slug}</p>
                                    </div>
                                    <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize ${statusBadge[page.status]}`}>
                                        {page.status}
                                    </span>
                                </div>

                                <div className="flex items-center gap-3 text-xs text-zinc-500 mb-4">
                                    <span className="flex items-center gap-1">
                                        <GripVertical className="h-3 w-3" />
                                        {page.sections_count} sections
                                    </span>
                                    <span>Template: {page.template}</span>
                                    {page.region && (
                                        <span className="flex items-center gap-1">
                                            <Globe className="h-3 w-3" />
                                            {page.region}
                                        </span>
                                    )}
                                </div>

                                <div className="flex items-center gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                                    <Link
                                        href={`/cms/pages/${page.id}`}
                                        className="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                                    >
                                        <Edit className="h-3.5 w-3.5" />
                                        Edit
                                    </Link>
                                    {page.status === 'published' && (
                                        <a
                                            href={`/${page.slug}`}
                                            target="_blank"
                                            className="flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-zinc-600 bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-400 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                        >
                                            <Eye className="h-3.5 w-3.5" />
                                        </a>
                                    )}
                                    <button
                                        onClick={() => deletePage(page.id)}
                                        className="flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                                    >
                                        <Trash2 className="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </div>
                        ))
                    )}
                </div>

                {/* Create Modal */}
                {showCreateModal && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                        <div className="bg-white dark:bg-zinc-900 rounded-2xl p-6 w-full max-w-md border border-zinc-200 dark:border-zinc-800 shadow-xl">
                            <h2 className="text-lg font-bold text-zinc-900 dark:text-white mb-4">Create New Page</h2>

                            <div className="space-y-4">
                                <div>
                                    <label className="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Title</label>
                                    <input
                                        type="text"
                                        value={newPage.title}
                                        onChange={(e) => setNewPage({ ...newPage, title: e.target.value })}
                                        className="w-full px-3 py-2 text-sm rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Page Title"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Slug</label>
                                    <input
                                        type="text"
                                        value={newPage.slug}
                                        onChange={(e) => setNewPage({ ...newPage, slug: e.target.value })}
                                        className="w-full px-3 py-2 text-sm rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="page-slug (auto-generated if empty)"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Template</label>
                                    <select
                                        value={newPage.template}
                                        onChange={(e) => setNewPage({ ...newPage, template: e.target.value })}
                                        className="w-full px-3 py-2 text-sm rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="default">Default</option>
                                        <option value="landing">Landing Page</option>
                                        <option value="minimal">Minimal</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Status</label>
                                    <select
                                        value={newPage.status}
                                        onChange={(e) => setNewPage({ ...newPage, status: e.target.value })}
                                        className="w-full px-3 py-2 text-sm rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                    </select>
                                </div>
                            </div>

                            <div className="flex items-center gap-3 mt-6">
                                <button
                                    onClick={() => setShowCreateModal(false)}
                                    className="flex-1 px-4 py-2.5 text-sm font-medium text-zinc-600 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    onClick={createPage}
                                    disabled={!newPage.title}
                                    className="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
                                >
                                    <Check className="h-4 w-4" />
                                    Create
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </DashboardLayout>
    )
}
