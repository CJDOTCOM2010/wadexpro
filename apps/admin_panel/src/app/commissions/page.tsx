'use client'

import { DashboardLayout } from '@/components/DashboardLayout'
import { TrendingUp, DollarSign, Users, Percent } from 'lucide-react'

export default function CommissionsPage() {
    return (
        <DashboardLayout>
            <div className="space-y-8 pb-12">
                <div>
                    <h1 className="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">Commission Ledger</h1>
                    <p className="mt-1 text-sm text-zinc-500 font-medium italic">Track platform earnings from every completed ride</p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {[
                        { label: 'Total Commission Earned', value: 'GH₵ 0.00', icon: DollarSign, desc: 'All-time platform commission' },
                        { label: 'Active Drivers', value: '0', icon: Users, desc: 'Drivers contributing to commissions' },
                        { label: 'Commission Rate', value: '15%', icon: Percent, desc: 'Current platform take-rate' },
                    ].map((stat) => (
                        <div key={stat.label} className="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                            <stat.icon className="h-5 w-5 text-blue-600 mb-3" />
                            <h3 className="text-xs font-bold text-zinc-500 uppercase tracking-widest">{stat.label}</h3>
                            <p className="text-3xl font-black text-zinc-900 dark:text-white mt-1">{stat.value}</p>
                            <p className="text-xs text-zinc-400 mt-2">{stat.desc}</p>
                        </div>
                    ))}
                </div>

                <div className="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                    <div className="p-6 border-b border-zinc-100 dark:border-zinc-800">
                        <h3 className="text-lg font-black text-zinc-900 dark:text-white">Commission Breakdown by Driver</h3>
                    </div>
                    <div className="p-12 text-center text-zinc-400">
                        <TrendingUp className="h-12 w-12 mx-auto mb-4 opacity-30" />
                        <p className="font-medium">No commission data yet</p>
                        <p className="text-sm mt-1">Commission records will populate after rides are completed.</p>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}
