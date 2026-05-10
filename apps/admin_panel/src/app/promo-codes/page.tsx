'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { Tag, Plus, ToggleLeft, ToggleRight, Copy, Trash2 } from 'lucide-react'

export default function PromoCodesPage() {
    const [promoCodes, setPromoCodes] = useState<any[]>([])
    const [loading, setLoading] = useState(true)

    useEffect(() => {
        const fetchData = async () => {
            try {
                const res = await adminApi.getPromotions()
                setPromoCodes(res.data?.data || [])
            } catch { }
            setLoading(false)
        }
        fetchData()
    }, [])

    return (
        <DashboardLayout>
            <div className="space-y-8 pb-12">
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 className="text-3xl font-black tracking-tight text-zinc-900 dark:text-white">Promo Codes</h1>
                        <p className="mt-1 text-sm text-zinc-500 font-medium italic">Manage discount campaigns and referral incentives</p>
                    </div>
                    <button className="flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold shadow-lg hover:bg-blue-700 transition-colors">
                        <Plus className="h-4 w-4" />
                        Create Promo Code
                    </button>
                </div>

                <div className="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead className="bg-zinc-50 dark:bg-zinc-900 text-xs font-bold text-zinc-500 uppercase tracking-widest">
                                <tr>
                                    <th className="px-6 py-4">Code</th>
                                    <th className="px-6 py-4">Type</th>
                                    <th className="px-6 py-4">Value</th>
                                    <th className="px-6 py-4">Uses</th>
                                    <th className="px-6 py-4">Status</th>
                                    <th className="px-6 py-4">Expires</th>
                                    <th className="px-6 py-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-zinc-100 dark:divide-zinc-800">
                                {loading ? (
                                    <tr><td colSpan={7} className="px-6 py-12 text-center text-zinc-400">Loading promo codes...</td></tr>
                                ) : promoCodes.length === 0 ? (
                                    <tr>
                                        <td colSpan={7} className="px-6 py-12 text-center text-zinc-400">
                                            <Tag className="h-10 w-10 mx-auto mb-3 opacity-30" />
                                            <p className="font-medium">No promo codes created yet</p>
                                        </td>
                                    </tr>
                                ) : promoCodes.map((promo) => (
                                    <tr key={promo.id} className="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-2">
                                                <code className="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-md text-sm font-mono font-bold">{promo.code}</code>
                                                <button className="text-zinc-400 hover:text-zinc-600"><Copy className="h-3.5 w-3.5" /></button>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-sm capitalize">{promo.type}</td>
                                        <td className="px-6 py-4 text-sm font-bold">
                                            {promo.type === 'percentage' ? `${promo.value}%` : `GH₵${promo.value}`}
                                        </td>
                                        <td className="px-6 py-4 text-sm">{promo.times_used}/{promo.max_uses || '∞'}</td>
                                        <td className="px-6 py-4">
                                            <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold ${promo.is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800'}`}>
                                                {promo.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-sm text-zinc-500">{promo.expires_at ? new Date(promo.expires_at).toLocaleDateString() : 'Never'}</td>
                                        <td className="px-6 py-4">
                                            <button className="text-zinc-400 hover:text-rose-600 transition-colors"><Trash2 className="h-4 w-4" /></button>
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
