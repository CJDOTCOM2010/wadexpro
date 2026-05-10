'use client'

import { useState, useEffect } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import {
    Settings,
    Save,
    RefreshCw,
    Search,
    ShieldCheck,
    Coins,
    Zap,
    Globe,
    Bell,
    CheckCircle2
} from 'lucide-react'
import { Button } from '@/components/ui/button'

interface Setting {
    id: string
    group: string
    key: string
    value: string
    type: string
    description: string
}

type GroupedSettings = Record<string, Setting[]>

export default function SettingsPage() {
    const [settings, setSettings] = useState<GroupedSettings>({})
    const [loading, setLoading] = useState(true)
    const [saving, setSaving] = useState<string | null>(null)
    const [search, setSearch] = useState('')
    const [activeGroup, setActiveGroup] = useState<string>('general')

    const fetchSettings = async () => {
        try {
            const res = await adminApi.getSettings()
            setSettings(res.data.data)
            // Default to first group if general doesn't exist
            if (!res.data.data['general'] && Object.keys(res.data.data).length > 0) {
                setActiveGroup(Object.keys(res.data.data)[0])
            }
        } catch (err) {
            console.error(err)
        } finally {
            setLoading(false)
        }
    }

    useEffect(() => {
        fetchSettings()
    }, [])

    const handleUpdateValue = (group: string, key: string, value: string) => {
        setSettings(prev => ({
            ...prev,
            [group]: prev[group].map(s => s.key === key ? { ...s, value } : s)
        }))
    }

    const saveGroup = async (group: string) => {
        setSaving(group)
        try {
            const groupSettings: Record<string, string> = {}
            settings[group].forEach(s => {
                groupSettings[s.key] = s.value
            })
            await adminApi.updateSettings(group, groupSettings)
            // Success alert?
        } catch (err) {
            console.error(err)
        } finally {
            setSaving(null)
        }
    }

    const groupIcons: Record<string, any> = {
        general: <Settings className="h-4 w-4" />,
        pricing: <Coins className="h-4 w-4" />,
        surge: <Zap className="h-4 w-4" />,
        logistics: <Globe className="h-4 w-4" />,
        notifications: <Bell className="h-4 w-4" />,
        security: <ShieldCheck className="h-4 w-4" />,
    }

    const filteredGroups = Object.keys(settings).filter(g => 
        g.toLowerCase().includes(search.toLowerCase()) || 
        settings[g].some(s => s.key.toLowerCase().includes(search.toLowerCase()) || s.description.toLowerCase().includes(search.toLowerCase()))
    )

    return (
        <DashboardLayout>
            <div className="space-y-6 max-w-6xl mx-auto">
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-bold">Global Configuration</h1>
                        <p className="text-sm text-zinc-500 mt-1">Manage system-wide variables and business logic parameters</p>
                    </div>
                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-zinc-500" />
                            <input
                                type="text"
                                placeholder="Search settings..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="pl-10 pr-4 py-2 bg-zinc-900 border border-zinc-800 rounded-xl text-sm text-zinc-200 focus:outline-none focus:ring-2 focus:ring-blue-500/50 w-64"
                            />
                        </div>
                        <Button onClick={fetchSettings} variant="outline" size="icon" className="h-10 w-10">
                            <RefreshCw className={`h-4 w-4 ${loading ? 'animate-spin' : ''}`} />
                        </Button>
                    </div>
                </div>

                <div className="flex flex-col lg:flex-row gap-8">
                    {/* Groups Sidebar */}
                    <div className="w-full lg:w-64 space-y-1">
                        {loading ? (
                            Array.from({ length: 5 }).map((_, i) => (
                                <div key={i} className="h-10 bg-zinc-900 border border-zinc-800 rounded-lg animate-pulse" />
                            ))
                        ) : filteredGroups.map(group => (
                            <button
                                key={group}
                                onClick={() => setActiveGroup(group)}
                                className={`w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all ${
                                    activeGroup === group 
                                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' 
                                    : 'text-zinc-500 hover:bg-zinc-900 hover:text-zinc-300'
                                }`}
                            >
                                <div className="flex items-center gap-3">
                                    {groupIcons[group] || <Settings className="h-4 w-4" />}
                                    <span className="text-sm font-semibold capitalize">{group}</span>
                                </div>
                                {activeGroup === group && <CheckCircle2 className="h-4 w-4" />}
                            </button>
                        ))}
                    </div>

                    {/* Settings Form */}
                    <div className="flex-1 space-y-6">
                        {loading ? (
                            Array.from({ length: 3 }).map((_, i) => (
                                <div key={i} className="h-32 bg-zinc-900 border border-zinc-800 rounded-2xl animate-pulse" />
                            ))
                        ) : activeGroup && settings[activeGroup] ? (
                            <div className="bg-zinc-950 border border-zinc-800 rounded-2xl overflow-hidden animate-in fade-in slide-in-from-right-2 duration-300">
                                <div className="bg-zinc-900 px-6 py-4 flex items-center justify-between border-b border-zinc-800">
                                    <div>
                                        <h3 className="font-bold text-zinc-100 capitalize">{activeGroup} Settings</h3>
                                        <p className="text-xs text-zinc-500">Configuration for the {activeGroup} subsystem</p>
                                    </div>
                                    <Button 
                                        onClick={() => saveGroup(activeGroup)} 
                                        disabled={saving === activeGroup}
                                        className="bg-emerald-600 hover:bg-emerald-700 text-white gap-2"
                                    >
                                        <Save className={`h-4 w-4 ${saving === activeGroup ? 'animate-pulse' : ''}`} />
                                        {saving === activeGroup ? 'Saving...' : 'Save Group'}
                                    </Button>
                                </div>
                                <div className="p-6 space-y-8">
                                    {settings[activeGroup].map((setting) => (
                                        <div key={setting.key} className="space-y-2">
                                            <div className="flex justify-between items-start">
                                                <div className="space-y-0.5">
                                                    <label className="text-sm font-bold text-zinc-300 font-mono tracking-tight">{setting.key}</label>
                                                    <p className="text-xs text-zinc-500 max-w-lg">{setting.description}</p>
                                                </div>
                                                <span className="px-2 py-0.5 bg-zinc-900 text-[10px] text-zinc-500 rounded border border-zinc-800 uppercase font-bold">
                                                    {setting.type}
                                                </span>
                                            </div>
                                            
                                            <div className="relative">
                                                {setting.type === 'boolean' ? (
                                                    <div className="flex items-center gap-3 bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3">
                                                       <button 
                                                            onClick={() => handleUpdateValue(activeGroup, setting.key, setting.value === '1' || setting.value === 'true' ? 'false' : 'true')}
                                                            className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                                                                (setting.value === '1' || setting.value === 'true') ? 'bg-blue-600' : 'bg-zinc-700'
                                                            }`}
                                                       >
                                                            <span className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
                                                                (setting.value === '1' || setting.value === 'true') ? 'translate-x-6' : 'translate-x-1'
                                                            }`} />
                                                       </button>
                                                       <span className="text-sm text-zinc-300">{(setting.value === '1' || setting.value === 'true') ? 'Enabled' : 'Disabled'}</span>
                                                    </div>
                                                ) : setting.type === 'number' || setting.type === 'integer' || setting.type === 'decimal' ? (
                                                    <input
                                                        type="number"
                                                        value={setting.value}
                                                        onChange={(e) => handleUpdateValue(activeGroup, setting.key, e.target.value)}
                                                        className="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all"
                                                    />
                                                ) : (
                                                    <textarea
                                                        rows={2}
                                                        value={setting.value}
                                                        onChange={(e) => handleUpdateValue(activeGroup, setting.key, e.target.value)}
                                                        className="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all resize-none font-mono text-sm"
                                                    />
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ) : (
                            <div className="h-64 flex flex-col items-center justify-center border border-dashed border-zinc-800 rounded-2xl text-zinc-500 gap-3">
                                <Settings className="h-10 w-10 opacity-20" />
                                <p>Select a settings group to configure</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}
