'use client'

import { useState } from 'react'
import DashboardLayout from '@/components/DashboardLayout'
import { Landmark, TrendingUp, CreditCard, DollarSign, PieChart, ArrowUpRight, ArrowDownRight, Activity, Calendar } from 'lucide-react'
import { Button } from '@/components/ui/button'
import StepUpGuard from '@/components/StepUpGuard'

export default function RevenueOrchestraPage() {
    const [stats] = useState({
        tpv: 'GHS 142,500.00',
        revenue: 'GHS 14,250.00',
        growth: '+12.4%',
        settled: 'GHS 128,250.00',
        pending_payouts: 'GHS 4,100.00'
    })

    const [recentTransactions] = useState([
        { id: 'tx_001', type: 'Order Payment', amount: 'GHS 45.00', status: 'completed', user: 'James K.', time: '2 mins ago' },
        { id: 'tx_002', type: 'Driver Payout', amount: '-GHS 120.00', status: 'processing', user: 'Eric T.', time: '15 mins ago' },
        { id: 'tx_003', type: 'Order Payment', amount: 'GHS 85.00', status: 'completed', user: 'Ama A.', time: '22 mins ago' }
    ])

    return (
        <DashboardLayout>
            <StepUpGuard>
                <div className="max-w-7xl mx-auto space-y-8 pb-20">
                    <header className="flex justify-between items-end border-b border-zinc-200 dark:border-zinc-800 pb-8">
                        <div>
                             <div className="flex items-center gap-2 mb-2">
                                 <span className="px-2 py-0.5 bg-blue-600 text-[10px] font-bold text-white rounded uppercase tracking-tighter">FinSec Control</span>
                                 <span className="text-zinc-400 text-xs">/ Systems Command</span>
                            </div>
                            <h1 className="text-4xl font-black tracking-tight text-zinc-900 dark:text-zinc-50 flex items-center gap-3">
                                <Landmark className="w-10 h-10 text-blue-600" />
                                Revenue Orchestra
                            </h1>
                            <p className="mt-2 text-lg text-zinc-600 dark:text-zinc-400">
                                Centralized platform-wide financial oversight, TPV monitoring, and automated settlement tracking.
                            </p>
                        </div>
                        <div className="flex gap-3">
                            <Button variant="outline" className="rounded-2xl h-12 px-6 font-black uppercase tracking-widest text-[10px] border-zinc-200 dark:border-zinc-800">
                                <Calendar className="w-4 h-4 mr-2" />
                                Real-time View
                            </Button>
                            <Button className="bg-blue-600 hover:bg-blue-700 text-white rounded-2xl h-12 px-6 font-black uppercase tracking-widest text-[10px] shadow-xl shadow-blue-500/20">
                                Download Global Audit
                            </Button>
                        </div>
                    </header>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {/* TPV Metric */}
                        <div className="bg-zinc-950 p-8 rounded-[2.5rem] border border-zinc-800 shadow-2xl space-y-4">
                            <div className="p-3 bg-blue-500/20 rounded-xl w-fit">
                                <TrendingUp className="w-6 h-6 text-blue-400" />
                            </div>
                            <div>
                                <p className="text-[10px] font-black text-zinc-500 uppercase tracking-[.2em]">Platform TPV</p>
                                <h3 className="text-3xl font-black text-white mt-1">{stats.tpv}</h3>
                                <div className="flex items-center gap-1 mt-2 text-emerald-400 text-[10px] font-black uppercase">
                                    <ArrowUpRight className="w-3 h-3" /> {stats.growth} from last mth
                                </div>
                            </div>
                        </div>

                        {/* Net Revenue */}
                        <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4">
                            <div className="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl w-fit">
                                <DollarSign className="w-6 h-6 text-emerald-600" />
                            </div>
                            <div>
                                <p className="text-[10px] font-black text-zinc-500 uppercase tracking-[.2em]">Net Recognition</p>
                                <h3 className="text-3xl font-black text-zinc-900 dark:text-white mt-1">{stats.revenue}</h3>
                                <p className="text-xs font-bold text-zinc-400 mt-2 italic">Automated 10% Commission</p>
                            </div>
                        </div>

                        {/* Pending Payouts */}
                        <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4">
                            <div className="p-3 bg-rose-50 dark:bg-rose-900/20 rounded-xl w-fit">
                                <CreditCard className="w-6 h-6 text-rose-600" />
                            </div>
                            <div>
                                <p className="text-[10px] font-black text-zinc-500 uppercase tracking-[.2em]">Pending Settlement</p>
                                <h3 className="text-3xl font-black text-zinc-900 dark:text-white mt-1">{stats.pending_payouts}</h3>
                                <p className="text-xs font-bold font-mono text-rose-500 mt-2">Active Batch Processing...</p>
                            </div>
                        </div>

                        {/* Health Score */}
                        <div className="bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4">
                            <div className="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl w-fit">
                                <Activity className="w-6 h-6 text-purple-600" />
                            </div>
                            <div>
                                <p className="text-[10px] font-black text-zinc-500 uppercase tracking-[.2em]">FinSec Health</p>
                                <h3 className="text-3xl font-black text-zinc-900 dark:text-white mt-1">99.8%</h3>
                                <p className="text-xs font-bold text-zinc-400 mt-2 italic">Low Reconcile Error Rate</p>
                            </div>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {/* Live Stream */}
                        <div className="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-10 space-y-6">
                            <div className="flex items-center justify-between">
                                <h2 className="text-xl font-black uppercase tracking-widest text-zinc-900 dark:text-white flex items-center gap-3">
                                    <PieChart className="w-6 h-6 text-blue-600" />
                                    Global Ledger Stream
                                </h2>
                            </div>
                            
                            <div className="space-y-6">
                                {recentTransactions.map(tx => (
                                    <div key={tx.id} className="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800 last:border-0 hover:border-blue-500/30 transition-all cursor-default group">
                                        <div className="flex items-center gap-5">
                                            <div className={`w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-xs ${tx.amount.startsWith('-') ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'}`}>
                                                {tx.amount.startsWith('-') ? <ArrowDownRight className="w-5 h-5" /> : <ArrowUpRight className="w-5 h-5" />}
                                            </div>
                                            <div>
                                                <p className="font-black text-zinc-900 dark:text-white uppercase tracking-tighter">{tx.type}</p>
                                                <div className="flex items-center gap-2 mt-1">
                                                    <span className="text-[10px] text-zinc-400 font-bold uppercase">{tx.user}</span>
                                                    <span className="text-zinc-300">•</span>
                                                    <span className="text-[10px] text-zinc-400 font-bold uppercase">{tx.time}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            <p className={`font-black tracking-tight ${tx.amount.startsWith('-') ? 'text-zinc-500' : 'text-blue-600 dark:text-blue-400'}`}>{tx.amount}</p>
                                            <span className={`text-[8px] font-black uppercase tracking-widest ${tx.status === 'completed' ? 'text-emerald-500' : 'text-amber-500'}`}>{tx.status}</span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Settlement Visualizer Map Placeholder */}
                        <div className="bg-zinc-950 rounded-[2.5rem] border border-zinc-800 p-10 flex flex-col justify-center items-center text-center space-y-6 relative overflow-hidden">
                            <div className="absolute inset-0 bg-blue-500/5 backdrop-blur-[100px]" />
                            <div className="relative z-10 space-y-6">
                                <div className="w-24 h-24 bg-blue-600/20 border border-blue-500/50 rounded-full flex items-center justify-center animate-pulse">
                                    <Landmark className="w-10 h-10 text-blue-500" />
                                </div>
                                <div>
                                    <h3 className="text-2xl font-black text-white italic tracking-tighter">Automated Settlement Engine</h3>
                                    <p className="text-zinc-500 text-sm mt-3 max-w-sm mx-auto">
                                        Distributing driver earnings and recognizing revenue via Paystack Transfer APIs in near real-time.
                                    </p>
                                </div>
                                <Button className="bg-white text-zinc-900 hover:bg-zinc-100 rounded-2xl px-12 py-7 font-black text-sm transition-all active:scale-95 shadow-2xl shadow-white/10 uppercase tracking-widest">
                                    Configure Payout Matrix
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </StepUpGuard>
        </DashboardLayout>
    )
}
