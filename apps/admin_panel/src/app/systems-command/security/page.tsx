'use client'

import { useState } from 'react'
import DashboardLayout from '@/components/DashboardLayout'
import { Server, ShieldAlert, Key, Fingerprint, Lock, Globe, Zap, AlertCircle, CheckCircle2, ChevronRight } from 'lucide-react'
import { Button } from '@/components/ui/button'
import StepUpGuard from '@/components/StepUpGuard'

export default function SecurityTowerPage() {
    const [securityAlerts] = useState([
        { id: 1, type: 'CRITICAL', title: 'Unauthorized API Access Attempt', origin: '192.168.1.1', time: '5 mins ago', severity: 'high' },
        { id: 2, type: 'WARNING', title: 'Mass Password Reset Request', origin: 'System Instance #4', time: '1 hr ago', severity: 'medium' },
        { id: 3, type: 'INFO', title: 'Security Patch Level Updated', origin: 'Auto-Update', time: '4 hrs ago', severity: 'low' }
    ])

    const [apiKeys] = useState([
        { id: 'key_01', name: 'Mobile App Flutter (Prod)', key: 'sk_live_•••••••••142', created_at: '2026-01-12' },
        { id: 'key_02', name: 'Paystack Notification Webhook', key: 'sk_live_•••••••••882', created_at: '2026-03-01' },
        { id: 'key_03', name: 'Public Tracking API', key: 'pk_live_•••••••••011', created_at: '2026-03-15' }
    ])

    return (
        <DashboardLayout>
            <StepUpGuard>
                <div className="max-w-7xl mx-auto space-y-8 pb-20">
                    <header className="flex justify-between items-end border-b border-zinc-200 dark:border-zinc-800 pb-8">
                        <div>
                             <div className="flex items-center gap-2 mb-2">
                                 <span className="px-2 py-0.5 bg-rose-600 text-[10px] font-bold text-white rounded uppercase tracking-tighter">Security Citadel</span>
                                 <span className="text-zinc-400 text-xs">/ Systems Command</span>
                            </div>
                            <h1 className="text-4xl font-black tracking-tight text-zinc-900 dark:text-zinc-50 flex items-center gap-3">
                                <Server className="w-10 h-10 text-rose-600" />
                                Security Tower
                            </h1>
                            <p className="mt-2 text-lg text-zinc-600 dark:text-zinc-400">
                                Orchestrating platform-wide cryptographic keys, access controls, and threat monitoring.
                            </p>
                        </div>
                        <Button className="bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-rose-600 hover:text-white transition-all h-14 px-8 rounded-2xl font-black uppercase tracking-widest text-xs">
                            <ShieldAlert className="w-4 h-4 mr-3" />
                            Firewall Status: ACTIVE
                        </Button>
                    </header>

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {/* Live Threat Feed */}
                        <div className="lg:col-span-2 space-y-6">
                            <div className="flex items-center justify-between px-2">
                                <h2 className="text-xl font-black text-zinc-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                                    <Zap className="w-5 h-5 text-amber-500" />
                                    Threat Detection Feed
                                </h2>
                                <span className="text-xs text-zinc-400 underline cursor-pointer">Archive All</span>
                            </div>
                            
                            <div className="space-y-4">
                                {securityAlerts.map(alert => (
                                    <div key={alert.id} className="p-6 bg-white dark:bg-zinc-950 rounded-3xl border-l-4 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between hover:border-zinc-300 dark:hover:border-zinc-700 transition-all"
                                         style={{ borderLeftColor: alert.severity === 'high' ? '#e11d48' : alert.severity === 'medium' ? '#f59e0b' : '#3b82f6' }}>
                                        <div className="flex items-center gap-5">
                                            <div className={`p-4 rounded-2xl ${alert.severity === 'high' ? 'bg-rose-50 text-rose-600' : 'bg-zinc-100 text-zinc-500'}`}>
                                                <AlertCircle className="w-6 h-6" />
                                            </div>
                                            <div>
                                                <p className="font-black text-lg text-zinc-900 dark:text-white">{alert.title}</p>
                                                <div className="flex items-center gap-3 mt-1">
                                                    <span className="text-[10px] font-bold text-zinc-400 flex items-center gap-1 uppercase">
                                                        <Globe className="w-3 h-3" /> {alert.origin}
                                                    </span>
                                                    <span className="text-zinc-300">•</span>
                                                    <span className="text-zinc-400 text-[10px] uppercase font-bold">{alert.time}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <Button variant="ghost" className="rounded-xl h-10 w-10 p-0 text-zinc-400 hover:text-zinc-900 transition-all">
                                            <ChevronRight className="w-5 h-5" />
                                        </Button>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* API Key Orchestration */}
                        <div className="space-y-6">
                            <h2 className="text-xl font-black text-zinc-900 dark:text-white uppercase tracking-widest flex items-center gap-2 px-2">
                                <Key className="w-5 h-5 text-blue-500" />
                                Key Registry
                            </h2>
                            
                            <div className="p-8 bg-zinc-950 rounded-[2.5rem] border border-zinc-800 shadow-2xl space-y-6">
                                {apiKeys.map(key => (
                                    <div key={key.id} className="space-y-2 group cursor-pointer">
                                        <div className="flex justify-between items-center">
                                            <p className="text-xs font-black text-white uppercase tracking-tighter">{key.name}</p>
                                            <span className="text-[10px] text-emerald-500 flex items-center gap-1 uppercase">
                                                <CheckCircle2 className="w-3 h-3" /> Live
                                            </span>
                                        </div>
                                        <div className="p-3 bg-white/5 border border-white/10 rounded-xl flex items-center justify-between group-hover:border-blue-500/50 transition-all">
                                            <code className="text-[11px] text-zinc-400 font-mono">{key.key}</code>
                                            <Lock className="w-3 h-3 text-zinc-600 group-hover:text-blue-400 transition-all" />
                                        </div>
                                    </div>
                                ))}
                                <Button className="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-2xl h-12 font-black uppercase tracking-widest text-[10px] mt-4">
                                    Rotate Master Keys
                                </Button>
                            </div>

                            {/* Fingerprint Stats */}
                            <div className="p-8 bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4">
                                <div className="flex items-center gap-4">
                                    <div className="p-3 bg-rose-50 dark:bg-rose-900/20 rounded-xl">
                                        <Fingerprint className="w-6 h-6 text-rose-600" />
                                    </div>
                                    <div>
                                        <h3 className="font-black text-zinc-900 dark:text-white">Biometric Vault</h3>
                                        <p className="text-[10px] text-zinc-500 uppercase font-black">2FA Compliance: 100%</p>
                                    </div>
                                </div>
                                <div className="h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                    <div className="h-full bg-emerald-500 w-[100%]" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </StepUpGuard>
        </DashboardLayout>
    )
}
