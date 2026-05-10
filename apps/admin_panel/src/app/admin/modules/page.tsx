'use client'

import { useState, useEffect } from 'react'
import DashboardLayout from '@/components/DashboardLayout'
import axios from '@/lib/axios'
import { Boxes, CheckCircle2, XCircle, Settings2, Power } from 'lucide-react'
import { Button } from '@/components/ui/button'

interface Module {
    name: string
    slug: string
    description: string
    is_enabled: boolean
    version: string
}

export default function ModuleManagerPage() {
    const [modules, setModules] = useState<Module[]>([])
    const [isLoading, setIsLoading] = useState(true)

    useEffect(() => {
        fetchModules()
    }, [])

    const fetchModules = async () => {
        try {
            const response = await axios.get('/admin/modules')
            setModules(response.data.data)
        } catch (error) {
            console.error('Failed to load orchestration registry.')
        } finally {
            setIsLoading(false)
        }
    }

    const toggleModule = async (slug: string, currentStatus: boolean) => {
        try {
            await axios.patch(`/admin/modules/${slug}/toggle`, {
                is_enabled: !currentStatus
            })
            fetchModules()
        } catch (error) {
            alert('Failed to toggle module status. Ensure you have Super Admin privilege.')
        }
    }

    return (
        <DashboardLayout>
            <div className="max-w-7xl mx-auto space-y-8">
                <header className="flex justify-between items-end">
                    <div>
                        <h1 className="text-4xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 flex items-center gap-3">
                            <Boxes className="w-10 h-10 text-blue-600" />
                            Module Orchestra
                        </h1>
                        <p className="mt-2 text-lg text-zinc-600 dark:text-zinc-400">
                            Core System Registry. Enable or disable enterprise features in real-time.
                        </p>
                    </div>
                </header>

                {isLoading ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {[1, 2, 3].map(i => (
                            <div key={i} className="h-48 bg-zinc-100 dark:bg-zinc-900 animate-pulse rounded-3xl" />
                        ))}
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {modules.map((module) => (
                            <div 
                                key={module.slug}
                                className={`p-8 rounded-3xl border transition-all duration-300 ${
                                    module.is_enabled 
                                    ? 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 shadow-sm' 
                                    : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 opacity-75 grayscale-[0.5]'
                                }`}
                            >
                                <div className="flex justify-between items-start mb-6">
                                    <div className={`p-3 rounded-2xl ${module.is_enabled ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500'}`}>
                                        <Settings2 className="w-6 h-6" />
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <span className={`text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-full ${module.is_enabled ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-200 text-zinc-600'}`}>
                                            v{module.version}
                                        </span>
                                    </div>
                                </div>

                                <h3 className="text-xl font-bold text-zinc-900 dark:text-white mb-2">{module.name}</h3>
                                <p className="text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2 mb-8">
                                    {module.description || 'Enterprise grade orchestration logic for the logistics ecosystem.'}
                                </p>

                                <div className="flex items-center justify-between pt-6 border-t border-zinc-100 dark:border-zinc-800">
                                    <div className="flex items-center gap-2">
                                        {module.is_enabled ? (
                                            <CheckCircle2 className="w-4 h-4 text-emerald-500" />
                                        ) : (
                                            <XCircle className="w-4 h-4 text-zinc-400" />
                                        )}
                                        <span className="text-xs font-semibold text-zinc-600 dark:text-zinc-400">
                                            {module.is_enabled ? 'Active' : 'Inactive'}
                                        </span>
                                    </div>
                                    
                                    <button 
                                        onClick={() => toggleModule(module.slug, module.is_enabled)}
                                        className={`p-2 rounded-xl transition-all ${
                                            module.is_enabled 
                                            ? 'text-red-500 hover:bg-red-50 bg-red-50/50' 
                                            : 'text-blue-600 hover:bg-blue-50 bg-blue-50/50'
                                        }`}
                                        title={module.is_enabled ? 'Deactivate Module' : 'Activate Module'}
                                    >
                                        <Power className="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </DashboardLayout>
    )
}
