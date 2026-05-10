'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { Wallet, ArrowUpRight, ArrowDownRight, Clock, CheckCircle2, XCircle } from 'lucide-react'

export default function PaymentsPage() {
    return (
        <DashboardLayout>
            <div className="space-y-8 pb-12">
                <div>
                    <h1 className="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">Payments & Transactions</h1>
                    <p className="mt-1 text-sm text-zinc-500 font-medium italic">Monitor all platform financial transactions in real-time</p>
                </div>

                {/* KPI Row */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                    {[
                        { label: 'Total Revenue', value: 'GH₵ 0.00', icon: Wallet, color: 'emerald', trend: '+0%' },
                        { label: 'Successful', value: '0', icon: CheckCircle2, color: 'blue', trend: '0%' },
                        { label: 'Pending', value: '0', icon: Clock, color: 'amber', trend: '0' },
                        { label: 'Failed', value: '0', icon: XCircle, color: 'rose', trend: '0' },
                    ].map((kpi) => (
                        <div key={kpi.label} className="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                            <div className={`p-2.5 bg-${kpi.color}-50 dark:bg-${kpi.color}-900/20 rounded-xl text-${kpi.color}-600 inline-block mb-3`}>
                                <kpi.icon className="h-5 w-5" />
                            </div>
                            <h3 className="text-xs font-bold text-zinc-500 uppercase tracking-widest">{kpi.label}</h3>
                            <p className="text-2xl font-black text-zinc-900 dark:text-white mt-1">{kpi.value}</p>
                            <p className="text-xs text-zinc-400 mt-2">{kpi.trend} vs last period</p>
                        </div>
                    ))}
                </div>

                {/* Transactions Table */}
                <div className="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                    <div className="p-6 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                        <h3 className="text-lg font-black text-zinc-900 dark:text-white">Recent Transactions</h3>
                        <div className="flex gap-2">
                            {['All', 'Completed', 'Pending', 'Failed'].map(filter => (
                                <button key={filter} className="px-3 py-1.5 text-xs font-bold rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                                    {filter}
                                </button>
                            ))}
                        </div>
                    </div>
                    <div className="p-12 text-center text-zinc-400">
                        <Wallet className="h-12 w-12 mx-auto mb-4 opacity-30" />
                        <p className="font-medium">No transactions yet</p>
                        <p className="text-sm mt-1">Transactions will appear here once rides are completed.</p>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}
