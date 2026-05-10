'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { 
    Users, 
    Gift, 
    Settings,
    TrendingUp,
    CheckCircle2,
    Clock,
    ArrowUpRight
} from 'lucide-react'

export default function GrowthReferralsPage() {
    const [metrics, setMetrics] = useState<any>(null)
    const [conversions, setConversions] = useState<any[]>([])
    const [loading, setLoading] = useState(true)

    const fetchData = async () => {
        setLoading(true)
        try {
            const [metRes, convRes] = await Promise.all([
                adminApi.getReferralMetrics(),
                adminApi.getReferralConversions()
            ])
            setMetrics(metRes.data)
            setConversions(convRes.data)
        } catch (err) {
            console.error('Failed to load referral data', err)
        }
        setLoading(false)
    }

    useEffect(() => { fetchData() }, [])

    if (loading) {
        return (
            <DashboardLayout>
                <div className="flex items-center justify-center h-[calc(100vh-200px)]">
                    <div className="flex flex-col items-center gap-4">
                        <div className="h-10 w-10 animate-spin rounded-full border-4 border-emerald-600 border-t-transparent" />
                        <p className="text-zinc-500 font-medium">Loading Growth Metrics...</p>
                    </div>
                </div>
            </DashboardLayout>
        )
    }

    return (
        <DashboardLayout>
            <div className="space-y-8 pb-12">
                {/* Header */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 className="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">Growth & Referrals</h1>
                        <p className="mt-1 text-sm text-zinc-500 font-medium italic">Monitor viral acquisition and manage incentive logic</p>
                    </div>
                    <div className="flex items-center gap-3">
                        <button className="flex items-center gap-2 px-4 py-2 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl text-sm font-bold shadow-lg hover:scale-105 transition-transform">
                            <Settings className="h-4 w-4" />
                            Configure Incentives
                        </button>
                    </div>
                </div>

                {/* Primary Metric Grid */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {/* Conversions Card */}
                    <div className="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
                        <div className="absolute -right-4 -bottom-4 text-emerald-50 opacity-50 dark:opacity-5 group-hover:scale-110 transition-transform">
                            <Users className="h-40 w-40" />
                        </div>
                        <div className="relative z-10">
                            <div className="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl text-emerald-600 inline-block mb-4">
                                <TrendingUp className="h-6 w-6" />
                            </div>
                            <h3 className="text-sm font-bold text-zinc-500 uppercase tracking-widest">Network Conversion</h3>
                            <p className="text-4xl font-black text-zinc-900 dark:text-white mt-1">
                                {metrics?.conversion_rate}%
                            </p>
                            <div className="mt-4 flex items-center gap-2 text-xs font-semibold text-zinc-500">
                                <span>{metrics?.successful_referrals} successful</span>
                                <span className="w-1 h-1 rounded-full bg-zinc-300" />
                                <span>{metrics?.total_referrals} total generated</span>
                            </div>
                        </div>
                    </div>

                    {/* Value Card */}
                    <div className="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
                        <div className="absolute -right-4 -bottom-4 text-emerald-50 opacity-50 dark:opacity-5 group-hover:scale-110 transition-transform">
                            <Gift className="h-40 w-40" />
                        </div>
                        <div className="relative z-10">
                            <div className="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-blue-600 inline-block mb-4">
                                <Gift className="h-6 w-6" />
                            </div>
                            <h3 className="text-sm font-bold text-zinc-500 uppercase tracking-widest">Incentive Value Granted</h3>
                            <p className="text-4xl font-black text-zinc-900 dark:text-white mt-1">
                                GH₵{metrics?.total_value_given?.toFixed(2)}
                            </p>
                            <div className="mt-4 flex items-center gap-2 text-xs font-semibold text-zinc-500">
                                <span className="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-full">GH₵10 Given to Referrer</span>
                                <span className="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-full">GH₵10 Given to New User</span>
                            </div>
                        </div>
                    </div>

                    {/* Pipeline Card */}
                    <div className="bg-zinc-900 text-white p-6 rounded-3xl border border-zinc-800 shadow-xl relative overflow-hidden">
                        <div className="relative z-10">
                            <div className="p-3 bg-zinc-800 rounded-xl text-amber-500 inline-block mb-4">
                                <Clock className="h-6 w-6" />
                            </div>
                            <h3 className="text-sm font-bold text-zinc-400 uppercase tracking-widest">Pending Verification</h3>
                            <p className="text-4xl font-black mt-1">
                                {metrics?.pending_referrals}
                            </p>
                            <p className="mt-4 text-xs font-medium text-zinc-500 max-w-[200px] leading-relaxed">
                                Users have registered with a code, but haven't completed their first ride yet.
                            </p>
                        </div>
                    </div>
                </div>

                {/* Recent Conversions Table */}
                <div className="bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                    <div className="p-6 md:p-8 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 flex justify-between items-center">
                        <h3 className="text-xl font-black text-zinc-900 dark:text-white">Recent Conversions</h3>
                    </div>
                    
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead className="bg-zinc-50 dark:bg-zinc-900 text-xs font-bold text-zinc-500 uppercase tracking-widest">
                                <tr>
                                    <th className="px-8 py-4">Promoter (Referrer)</th>
                                    <th className="px-8 py-4">New Customer</th>
                                    <th className="px-8 py-4">Reward Value</th>
                                    <th className="px-8 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-zinc-100 dark:divide-zinc-800">
                                {conversions.length === 0 ? (
                                    <tr>
                                        <td colSpan={4} className="px-8 py-12 text-center text-zinc-500">
                                            No rewarded referrals found yet.
                                        </td>
                                    </tr>
                                ) : conversions.map((ref) => (
                                    <tr key={ref.id} className="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                        <td className="px-8 py-4">
                                            <p className="font-bold text-zinc-900 dark:text-white">{ref.referrer?.name}</p>
                                            <p className="text-xs text-zinc-500">{ref.referrer?.email}</p>
                                        </td>
                                        <td className="px-8 py-4">
                                            <p className="font-bold text-zinc-900 dark:text-white">{ref.referred?.name}</p>
                                            <p className="text-xs text-zinc-500">{ref.referred?.email}</p>
                                        </td>
                                        <td className="px-8 py-4">
                                            <div className="font-black text-emerald-600">GH₵20.00 Total</div>
                                            <p className="text-xs text-zinc-500">(2x GH₵10.00 Promo Codes)</p>
                                        </td>
                                        <td className="px-8 py-4">
                                            <div className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold">
                                                <CheckCircle2 className="h-3.5 w-3.5" />
                                                Verified
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}
