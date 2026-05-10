'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { 
    Tag, 
    Plus, 
    Calendar, 
    Activity, 
    CheckCircle2, 
    XCircle, 
    Percent, 
    Banknote,
    Clock,
    Filter,
    MoreHorizontal,
    Trash2,
    ToggleLeft,
    ToggleRight
} from 'lucide-react'

interface Promotion {
    id: string
    code: string
    name: string
    description: string
    type: 'percentage' | 'fixed'
    value: number
    min_order_amount: number
    max_discount: number | null
    starts_at: string | null
    expires_at: string | null
    max_uses: number | null
    times_used: number
    is_active: boolean
    created_at: string
}

export default function PromotionsPage() {
    const [promotions, setPromotions] = useState<Promotion[]>([])
    const [loading, setLoading] = useState(true)
    const [showModal, setShowModal] = useState(false)
    const [editingPromo, setEditingPromo] = useState<Promotion | null>(null)

    useEffect(() => {
        fetchPromotions()
    }, [])

    const fetchPromotions = async () => {
        try {
            setLoading(true)
            const res = await adminApi.getPromotions()
            setPromotions(res.data.data)
        } catch (err) {
            console.error('Failed to fetch promotions', err)
        } finally {
            setLoading(false)
        }
    }

    const handleToggle = async (id: string) => {
        try {
            await adminApi.togglePromotion(id)
            setPromotions(prev => prev.map(p => p.id === id ? { ...p, is_active: !p.is_active } : p))
        } catch (err) {
            console.error('Toggle failed', err)
        }
    }

    const handleDelete = async (id: string) => {
        if (!confirm('Are you sure you want to delete this promotion?')) return
        try {
            await adminApi.deletePromotion(id)
            setPromotions(prev => prev.filter(p => p.id !== id))
        } catch (err) {
            console.error('Delete failed', err)
        }
    }

    return (
        <DashboardLayout>
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-bold text-zinc-900 dark:text-white">Promotion Engine</h1>
                        <p className="mt-1 text-sm text-zinc-500">Manage campaign codes and customer loyalty incentives</p>
                    </div>
                    <button 
                        onClick={() => { setEditingPromo(null); setShowModal(true); }}
                        className="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all shadow-lg shadow-blue-500/20 text-sm font-semibold"
                    >
                        <Plus className="h-4 w-4" />
                        Create Campaign
                    </button>
                </div>

                {/* Metrics Summary */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div className="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                        <div className="flex items-center gap-4">
                            <div className="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-blue-600">
                                <Activity className="h-5 w-5" />
                            </div>
                            <div>
                                <p className="text-xs text-zinc-500 uppercase tracking-wider font-semibold">Active Campaigns</p>
                                <p className="text-2xl font-bold dark:text-white">{promotions.filter(p => p.is_active).length}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                        <div className="flex items-center gap-4">
                            <div className="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl text-emerald-600">
                                <CheckCircle2 className="h-5 w-5" />
                            </div>
                            <div>
                                <p className="text-xs text-zinc-500 uppercase tracking-wider font-semibold">Total Redemptions</p>
                                <p className="text-2xl font-bold dark:text-white">{promotions.reduce((sum, p) => sum + p.times_used, 0)}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                        <div className="flex items-center gap-4">
                            <div className="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl text-amber-600">
                                <Clock className="h-5 w-5" />
                            </div>
                            <div>
                                <p className="text-xs text-zinc-500 uppercase tracking-wider font-semibold">Expiring Soon</p>
                                <p className="text-2xl font-bold dark:text-white">
                                    {promotions.filter(p => p.expires_at && new Date(p.expires_at) < new Date(Date.now() + 7*24*60*60*1000)).length}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Table Section */}
                <div className="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                    <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                        <div className="relative">
                            <Filter className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-zinc-400" />
                            <input 
                                type="text" 
                                placeholder="Filter codes..." 
                                className="pl-10 pr-4 py-2 bg-zinc-50 dark:bg-zinc-800 border-none rounded-lg text-xs focus:ring-2 focus:ring-blue-500 max-w-[200px]"
                            />
                        </div>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm">
                            <thead className="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 font-medium">
                                <tr>
                                    <th className="px-6 py-4">Campaign</th>
                                    <th className="px-6 py-4">Value</th>
                                    <th className="px-6 py-4">Usage</th>
                                    <th className="px-6 py-4">Timeline</th>
                                    <th className="px-6 py-4">Status</th>
                                    <th className="px-6 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-zinc-200 dark:divide-zinc-800">
                                {loading ? (
                                    <tr>
                                        <td colSpan={6} className="px-6 py-10 text-center text-zinc-500">
                                            <div className="flex items-center justify-center gap-2">
                                                <div className="h-4 w-4 animate-spin rounded-full border-2 border-blue-600 border-t-transparent" />
                                                Synchronizing campaign data...
                                            </div>
                                        </td>
                                    </tr>
                                ) : promotions.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="px-6 py-10 text-center text-zinc-500">
                                            No campaigns found. Start by creating a new promotion code.
                                        </td>
                                    </tr>
                                ) : (
                                    promotions.map((promo) => (
                                        <tr key={promo.id} className="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="h-10 w-10 bg-zinc-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center">
                                                        <Tag className="h-5 w-5 text-zinc-500" />
                                                    </div>
                                                    <div>
                                                        <p className="font-bold text-zinc-900 dark:text-white tracking-widest uppercase">{promo.code}</p>
                                                        <p className="text-[10px] text-zinc-500 truncate max-w-[150px]">{promo.name}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-1.5">
                                                    {promo.type === 'percentage' ? <Percent className="h-3.5 w-3.5 text-blue-500" /> : <Banknote className="h-3.5 w-3.5 text-emerald-500" />}
                                                    <span className="font-semibold text-zinc-700 dark:text-zinc-300">
                                                        {promo.value}{promo.type === 'percentage' ? '%' : ' GHS'}
                                                    </span>
                                                </div>
                                                <p className="text-[10px] text-zinc-500 mt-0.5">Min: {promo.min_order_amount} GHS</p>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex flex-col gap-1 w-24">
                                                    <div className="flex justify-between text-[10px] font-medium">
                                                        <span>{promo.times_used} used</span>
                                                        {promo.max_uses && <span>/{promo.max_uses}</span>}
                                                    </div>
                                                    <div className="h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                                        <div 
                                                            className="h-full bg-blue-500 rounded-full" 
                                                            style={{ width: `${promo.max_uses ? (promo.times_used / promo.max_uses) * 100 : 100}%` }}
                                                        />
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex flex-col gap-1 text-[10px] text-zinc-500">
                                                    <div className="flex items-center gap-1.5">
                                                        <Calendar className="h-3 w-3" />
                                                        <span>{promo.starts_at ? new Date(promo.starts_at).toLocaleDateString() : 'Immediate'}</span>
                                                    </div>
                                                    <div className="flex items-center gap-1.5">
                                                        <Clock className="h-3 w-3" />
                                                        <span>{promo.expires_at ? new Date(promo.expires_at).toLocaleDateString() : 'No Expiry'}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <button 
                                                    onClick={() => handleToggle(promo.id)}
                                                    className={`p-1 rounded-lg transition-colors ${promo.is_active ? 'text-emerald-500 hover:bg-emerald-50' : 'text-zinc-400 hover:bg-zinc-100'}`}
                                                >
                                                    {promo.is_active ? <ToggleRight className="h-7 w-7" /> : <ToggleLeft className="h-7 w-7" />}
                                                </button>
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <div className="flex items-center justify-end gap-2">
                                                    <button className="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors text-zinc-500">
                                                        <MoreHorizontal className="h-4 w-4" />
                                                    </button>
                                                    <button 
                                                        onClick={() => handleDelete(promo.id)}
                                                        className="p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors text-red-500"
                                                    >
                                                        <Trash2 className="h-4 w-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    )
}
