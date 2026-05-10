'use client'

import React, { useEffect, useState } from 'react'
import { adminApi } from '@/lib/axios'
import { 
    Landmark, 
    TrendingUp, 
    TrendingDown, 
    Download, 
    Search,
    ArrowUpRight,
    ArrowDownLeft,
    Clock,
    DollarSign,
    Scale
} from 'lucide-react'
import { format } from 'date-fns'

export default function GeneralLedgerPage() {
    const [transactions, setTransactions] = useState<any[]>([])
    const [summary, setSummary] = useState<any>(null)
    const [loading, setLoading] = useState(true)
    const [searchTerm, setSearchTerm] = useState('')

    const fetchData = async () => {
        setLoading(true)
        try {
            const [ledgerRes, summaryRes] = await Promise.all([
                adminApi.getGeneralLedger(),
                adminApi.getRevenueSummary()
            ])
            setTransactions(ledgerRes.data.data.data)
            setSummary(summaryRes.data.data)
        } catch (error) {
            console.error('Failed to fetch financial data:', error)
        } finally {
            setLoading(false)
        }
    }

    useEffect(() => {
        fetchData()
    }, [])

    const filteredTransactions = transactions.filter(tx => 
        tx.reference.toLowerCase().includes(searchTerm.toLowerCase()) ||
        tx.user?.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        tx.type.toLowerCase().includes(searchTerm.toLowerCase())
    )

    return (
        <div className="p-8 space-y-8 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
            {/* Header */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-3xl font-black tracking-tight text-zinc-900 dark:text-white flex items-center gap-3">
                        <Landmark className="w-8 h-8 text-blue-600" />
                        General Ledger
                    </h1>
                    <p className="text-zinc-500 dark:text-zinc-400 mt-1">
                        Real-time audit trail of all platform financial movements.
                    </p>
                </div>
                <div className="flex items-center gap-3">
                    <button 
                        onClick={() => window.print()}
                        className="flex items-center gap-2 px-4 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 transition-all"
                    >
                        <Download className="w-4 h-4" />
                        Export PDF
                    </button>
                    <button className="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/20 transition-all">
                        Process Batch Payouts
                    </button>
                </div>
            </div>

            {/* Financial Summary Cards */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div className="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
                    <div className="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <TrendingUp className="w-16 h-16 text-emerald-500" />
                    </div>
                    <p className="text-xs font-black text-zinc-400 uppercase tracking-widest">Gross Volume</p>
                    <h2 className="text-2xl font-black mt-2 text-zinc-900 dark:text-white">
                        {summary?.gross_volume.toLocaleString() || '0.00'} <span className="text-sm font-normal opacity-50">GHS</span>
                    </h2>
                    <div className="mt-4 flex items-center gap-1 text-emerald-500 text-xs font-bold">
                        <ArrowUpRight className="w-3 h-3" />
                        <span>+12.4% vs last week</span>
                    </div>
                </div>

                <div className="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
                    <div className="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <DollarSign className="w-16 h-16 text-blue-500" />
                    </div>
                    <p className="text-xs font-black text-zinc-400 uppercase tracking-widest">Platform Commission</p>
                    <h2 className="text-2xl font-black mt-2 text-zinc-900 dark:text-white text-blue-600">
                        {summary?.total_commissions.toLocaleString() || '0.00'} <span className="text-sm font-normal opacity-50">GHS</span>
                    </h2>
                    <p className="mt-4 text-zinc-400 text-[10px] font-bold">Net Platform Revenue</p>
                </div>

                <div className="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
                    <div className="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <Scale className="w-16 h-16 text-amber-500" />
                    </div>
                    <p className="text-xs font-black text-zinc-400 uppercase tracking-widest">Payout Liability</p>
                    <h2 className="text-2xl font-black mt-2 text-zinc-900 dark:text-white">
                        {summary?.payout_liability.toLocaleString() || '0.00'} <span className="text-sm font-normal opacity-50">GHS</span>
                    </h2>
                    <div className="mt-4 flex items-center gap-1 text-zinc-400 text-xs font-bold">
                        <Clock className="w-3 h-3" />
                        <span>Awaiting driver withdrawal</span>
                    </div>
                </div>

                <div className="bg-zinc-900 p-6 rounded-2xl border border-zinc-800 shadow-xl relative overflow-hidden group">
                    <div className="absolute bottom-0 right-0 p-4 opacity-20">
                        <TrendingUp className="w-24 h-24 text-blue-600 -rotate-12" />
                    </div>
                    <p className="text-xs font-black text-blue-400 uppercase tracking-widest">Estimated Growth</p>
                    <h2 className="text-2xl font-black mt-2 text-white">PROFITABLE</h2>
                    <p className="mt-4 text-zinc-500 text-[10px] font-bold uppercase tracking-tight">Financial Health: Excellent</p>
                </div>
            </div>

            {/* Ledger Table Container */}
            <div className="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-hidden">
                <div className="p-6 border-b border-zinc-100 dark:border-zinc-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div className="relative w-full md:w-96">
                        <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" />
                        <input 
                            type="text" 
                            placeholder="Search reference, user, or type..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="w-full pl-11 pr-4 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all"
                        />
                    </div>
                    <div className="flex items-center gap-2">
                        <select className="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-bold px-4 py-2.5">
                            <option>All Currencies</option>
                            <option>GHS</option>
                            <option>NGN</option>
                        </select>
                        <select className="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-bold px-4 py-2.5">
                            <option>All Types</option>
                            <option>Credit</option>
                            <option>Debit</option>
                            <option>Earning</option>
                        </select>
                    </div>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="bg-zinc-50 dark:bg-zinc-800/50">
                                <th className="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Date & Time</th>
                                <th className="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Reference</th>
                                <th className="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Entity / User</th>
                                <th className="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Type</th>
                                <th className="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Amount</th>
                                <th className="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-zinc-100 dark:divide-zinc-800">
                            {filteredTransactions.map((tx) => (
                                <tr key={tx.id} className="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td className="px-6 py-4">
                                        <div className="text-xs font-bold text-zinc-900 dark:text-white">
                                            {format(new Date(tx.created_at), 'MMM dd, yyyy')}
                                        </div>
                                        <div className="text-[10px] text-zinc-500 font-medium tracking-tight">
                                            {format(new Date(tx.created_at), 'HH:mm:ss')}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className="text-xs font-mono font-bold text-zinc-500 bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded">
                                            {tx.reference}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            <div className="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[10px] font-black text-zinc-500">
                                                {tx.user?.name.charAt(0)}
                                            </div>
                                            <div>
                                                <div className="text-xs font-black text-zinc-900 dark:text-white">{tx.user?.name}</div>
                                                <div className="text-[10px] text-zinc-500">{tx.user?.email}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-2">
                                            {tx.type === 'earning' || tx.type === 'credit' ? (
                                                <ArrowDownLeft className="w-3 h-3 text-emerald-500" />
                                            ) : (
                                                <ArrowUpRight className="w-3 h-3 text-rose-500" />
                                            )}
                                            <span className="text-[10px] font-black uppercase tracking-tight text-zinc-600 dark:text-zinc-400 underline decoration-zinc-200 underline-offset-4">
                                                {tx.type}
                                            </span>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className={tx.type === 'earning' || tx.type === 'credit' ? 'text-emerald-600 font-black' : 'text-rose-600 font-black'}>
                                            <span className="text-[10px] mr-1">{tx.currency}</span>
                                            {parseFloat(tx.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <span className="px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase rounded-full">
                                            {tx.status}
                                        </span>
                                    </td>
                                </tr>
                            ))}
                            {filteredTransactions.length === 0 && !loading && (
                                <tr>
                                    <td colSpan={6} className="px-6 py-12 text-center text-zinc-500 font-bold italic">
                                        No financial entries found for the current query.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination Placeholder */}
                <div className="p-6 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/30 flex items-center justify-between">
                    <p className="text-xs font-bold text-zinc-400">Showing {filteredTransactions.length} of {transactions.length} entries</p>
                    <div className="flex items-center gap-2">
                        <button className="px-3 py-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg text-[10px] font-black disabled:opacity-50" disabled>PREV</button>
                        <button className="px-3 py-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg text-[10px] font-black disabled:opacity-50" disabled>NEXT</button>
                    </div>
                </div>
            </div>
        </div>
    )
}
