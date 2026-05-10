'use client'

import { useState, useEffect } from 'react'
import DashboardLayout from '@/components/DashboardLayout'
import axios from '@/lib/axios'
import { Settings, Save, Globe, Smartphone, Bell, Database, Shield } from 'lucide-react'
import { Button } from '@/components/ui/button'

export default function SystemSettingsPage() {
    const [settings, setSettings] = useState<any>({
        platform_name: 'WADEXP Logistics',
        system_email: 'noreply@wadexp.com',
        base_currency: 'GHS',
        default_language: 'en',
        sms_gateway: 'hubtel',
        automatic_assignment: true,
        maintenance_mode: false
    })
    const [isLoading, setIsLoading] = useState(true)
    const [isSaving, setIsSaving] = useState(false)

    useEffect(() => {
        fetchSettings()
    }, [])

    const fetchSettings = async () => {
        try {
            const response = await axios.get('/admin/settings')
            setSettings(response.data.data)
        } catch (error) {
            console.error('Failed to load global configuration.')
        } finally {
            setIsLoading(false)
        }
    }

    const saveSettings = async (e: React.FormEvent) => {
        e.preventDefault()
        setIsSaving(true)
        try {
            await axios.post('/admin/settings', settings)
            alert('Global configuration synchronized successfully.')
        } catch (error) {
            alert('Failed to update system variables.')
        } finally {
            setIsSaving(false)
        }
    }

    return (
        <DashboardLayout>
            <div className="max-w-4xl mx-auto space-y-8">
                <header className="flex justify-between items-end">
                    <div>
                        <h1 className="text-4xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 flex items-center gap-3">
                            <Settings className="w-10 h-10 text-blue-600" />
                            System Orchestration
                        </h1>
                        <p className="mt-2 text-lg text-zinc-600 dark:text-zinc-400">
                            Configure global parameters and mission-critical system variables.
                        </p>
                    </div>
                </header>

                <form onSubmit={saveSettings} className="space-y-6">
                    {/* General Settings */}
                    <div className="p-8 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                        <div className="flex items-center gap-3 mb-4">
                            <Globe className="w-5 h-5 text-zinc-400" />
                            <h2 className="text-xl font-bold text-zinc-900 dark:text-white">General & Regional</h2>
                        </div>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Platform Name</label>
                                <input 
                                    type="text" 
                                    value={settings.platform_name}
                                    onChange={e => setSettings({...settings, platform_name: e.target.value})}
                                    className="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 outline-none"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Base Currency</label>
                                <select 
                                    value={settings.base_currency}
                                    onChange={e => setSettings({...settings, base_currency: e.target.value})}
                                    className="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 outline-none"
                                >
                                    <option value="GHS">GHS (Ghana Cedi)</option>
                                    <option value="XOF">XOF (CFA Franc)</option>
                                    <option value="USD">USD (US Dollar)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Infrastructure Settings */}
                    <div className="p-8 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                        <div className="flex items-center gap-3 mb-4">
                            <Database className="w-5 h-5 text-zinc-400" />
                            <h2 className="text-xl font-bold text-zinc-900 dark:text-white">Network & Gateway</h2>
                        </div>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">SMS Integration Hub</label>
                                <select 
                                    value={settings.sms_gateway}
                                    onChange={e => setSettings({...settings, sms_gateway: e.target.value})}
                                    className="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 outline-none"
                                >
                                    <option value="hubtel">Hubtel Africa</option>
                                    <option value="twilio">Twilio Global</option>
                                    <option value="none">Disabled</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Default Locale</label>
                                <select 
                                    value={settings.default_language}
                                    onChange={e => setSettings({...settings, default_language: e.target.value})}
                                    className="w-full px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 outline-none"
                                >
                                    <option value="en">English (UK/US)</option>
                                    <option value="fr">French (West Africa)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Operational Toggles */}
                    <div className="p-8 bg-zinc-900 dark:bg-blue-950/20 rounded-3xl border border-zinc-800 shadow-xl space-y-6">
                        <div className="flex items-center gap-3 mb-4">
                            <Shield className="w-5 h-5 text-blue-400" />
                            <h2 className="text-xl font-bold text-white">Security & Logic</h2>
                        </div>
                        
                        <div className="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/10">
                            <div className="flex items-center gap-4">
                                <div className={`p-2 rounded-lg ${settings.automatic_assignment ? 'bg-blue-500' : 'bg-zinc-700'}`}>
                                    <Smartphone className="w-5 h-5 text-white" />
                                </div>
                                <div>
                                    <p className="font-bold text-white">AI-Auto Assignment</p>
                                    <p className="text-xs text-zinc-400">Automatically find best-fit drivers via TSP logic.</p>
                                </div>
                            </div>
                            <button 
                                type="button"
                                onClick={() => setSettings({...settings, automatic_assignment: !settings.automatic_assignment})}
                                className={`w-14 h-8 rounded-full transition-all relative ${settings.automatic_assignment ? 'bg-blue-500' : 'bg-zinc-700'}`}
                            >
                                <div className={`absolute top-1 w-6 h-6 bg-white rounded-full transition-all ${settings.automatic_assignment ? 'right-1' : 'left-1'}`} />
                            </button>
                        </div>
                    </div>

                    <div className="flex justify-end gap-4">
                        <Button 
                            type="submit" 
                            disabled={isSaving}
                            className="bg-blue-600 hover:bg-blue-700 text-white rounded-2xl px-12 py-7 font-bold text-lg shadow-xl shadow-blue-500/20 transition-all active:scale-95"
                        >
                            {isSaving ? 'Synchronizing...' : (
                                <span className="flex items-center gap-2">
                                    <Save className="w-5 h-5" />
                                    Publish Configuration
                                </span>
                            )}
                        </Button>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    )
}
