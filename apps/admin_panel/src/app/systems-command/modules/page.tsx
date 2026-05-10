'use client'

import { useState, useEffect } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import {
    Cpu,
    CheckCircle2,
    XCircle,
    Settings2,
    Info,
    AlertTriangle,
    RefreshCw,
    ToggleLeft,
    ToggleRight
} from 'lucide-react'
import { Switch } from '@/components/ui/switch'
import { Button } from '@/components/ui/button'

interface Module {
    slug: string
    name: string
    description: string
    is_enabled: boolean
    version: string
    dependencies: string[]
    last_updated: string
}

export default function ModulesPage() {
    const [modules, setModules] = useState<Module[]>([])
    const [loading, setLoading] = useState(true)
    const [toggling, setToggling] = useState<string | null>(null)

    const fetchModules = async () => {
        try {
            const res = await adminApi.getModules()
            setModules(res.data.data)
        } catch (err) {
            console.error(err)
        } finally {
            setLoading(false)
        }
    }

    useEffect(() => {
        fetchModules()
    }, [])

    const handleToggle = async (slug: string) => {
        setToggling(slug)
        try {
            await adminApi.toggleModule(slug)
            await fetchModules()
        } catch (err) {
            console.error(err)
        } finally {
            setToggling(null)
        }
    }

    return (
        <DashboardLayout>
            <div className="space-y-6 max-w-6xl mx-auto">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">System Modules</h1>
                        <p className="text-sm text-zinc-500 mt-1">Orchestrate platform features and micro-services</p>
                    </div>
                    <Button onClick={fetchModules} variant="outline" size="sm" className="gap-2">
                        <RefreshCw className={`h-4 w-4 ${loading ? 'animate-spin' : ''}`} />
                        Refresh
                    </Button>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {loading ? (
                        Array.from({ length: 6 }).map((_, i) => (
                            <div key={i} className="h-48 bg-zinc-900 border border-zinc-800 rounded-2xl animate-pulse" />
                        ))
                    ) : modules.map((mod) => (
                        <div 
                            key={mod.slug} 
                            className={`group relative bg-zinc-950 border transition-all duration-300 rounded-2xl p-6 ${
                                mod.is_enabled ? 'border-zinc-800 hover:border-blue-500/30' : 'border-zinc-900 opacity-75'
                            }`}
                        >
                            <div className="flex items-start justify-between mb-4">
                                <div className={`p-3 rounded-xl ${
                                    mod.is_enabled ? 'bg-blue-600/10 text-blue-500' : 'bg-zinc-800 text-zinc-500'
                                }`}>
                                    <Cpu className="h-6 w-6" />
                                </div>
                                <div className="flex items-center gap-2">
                                    <span className="text-[10px] font-mono text-zinc-600 uppercase tracking-widest">{mod.version}</span>
                                    <Switch 
                                        checked={mod.is_enabled} 
                                        onCheckedChange={() => handleToggle(mod.slug)}
                                        disabled={toggling === mod.slug}
                                    />
                                </div>
                            </div>

                            <div className="space-y-1">
                                <h3 className="font-bold text-zinc-100 flex items-center gap-2">
                                    {mod.name}
                                    {mod.is_enabled ? (
                                        <CheckCircle2 className="h-3.5 w-3.5 text-emerald-500" />
                                    ) : (
                                        <XCircle className="h-3.5 w-3.5 text-zinc-600" />
                                    )}
                                </h3>
                                <p className="text-xs text-zinc-500 line-clamp-2 leading-relaxed">
                                    {mod.description || 'Core system functionality module.'}
                                </p>
                            </div>

                            <div className="mt-6 pt-6 border-t border-zinc-900 flex items-center justify-between">
                                <div className="flex gap-1.5">
                                    {mod.dependencies?.slice(0, 2).map(dep => (
                                        <span key={dep} className="px-2 py-0.5 bg-zinc-900 text-[10px] text-zinc-500 rounded border border-zinc-800">
                                            {dep}
                                        </span>
                                    ))}
                                </div>
                                <button className="text-zinc-500 hover:text-white transition-colors">
                                    <Settings2 className="h-4 w-4" />
                                </button>
                            </div>

                            {!mod.is_enabled && (
                                <div className="absolute inset-0 bg-zinc-950/20 backdrop-grayscale rounded-2xl pointer-events-none" />
                            )}
                        </div>
                    ))}
                </div>

                <div className="bg-amber-900/10 border border-amber-900/30 rounded-2xl p-6 flex gap-4 mt-8">
                    <div className="h-10 w-10 rounded-full bg-amber-900/20 flex items-center justify-center shrink-0">
                        <AlertTriangle className="h-5 w-5 text-amber-500" />
                    </div>
                    <div>
                        <h4 className="font-bold text-amber-500 text-sm">Caution: Modular Integrity</h4>
                        <p className="text-xs text-zinc-400 mt-1 leading-relaxed">
                            Disabling core modules like **Logistics** or **Payments** will immediately suspend associated API endpoints and mobile application functionalities. Ensure you have evaluated dependency impacts before toggling.
                        </p>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}
