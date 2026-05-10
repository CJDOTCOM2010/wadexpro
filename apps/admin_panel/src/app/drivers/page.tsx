'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { UserCheck, Search, Filter, CheckCircle, XCircle, Star, Wifi, Eye } from 'lucide-react'
import { useSearchParams } from 'next/navigation'
import Link from 'next/link'

interface Driver {
    id: string
    user_name: string
    user_email: string
    user_phone: string
    avatar_url: string | null
    license_number: string
    status: string
    is_online: boolean
    is_available: boolean
    rating: string
    total_deliveries: number
    total_cancellations: number
    created_at: string
}

const statusStyles: Record<string, string> = {
    active: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400',
    pending_verification: 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400',
    suspended: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    deactivated: 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-400',
}

export default function DriversPage() {
    const searchParams = useSearchParams()
    const [drivers, setDrivers] = useState<Driver[]>([])
    const [loading, setLoading] = useState(true)
    const [statusFilter, setStatusFilter] = useState(searchParams?.get('status') || '')
    const [searchQuery, setSearchQuery] = useState('')
    const [actionLoading, setActionLoading] = useState<string | null>(null)

    const fetchDrivers = () => {
        setLoading(true)
        const params: Record<string, string> = { per_page: '25' }
        if (statusFilter) params.status = statusFilter
        if (searchQuery) params.search = searchQuery

        adminApi.getDrivers(params)
            .then(res => setDrivers(res.data.data || []))
            .catch(console.error)
            .finally(() => setLoading(false))
    }

    useEffect(() => { fetchDrivers() }, [statusFilter])

    const approveDriver = async (id: string) => {
        setActionLoading(id)
        try {
            await adminApi.approveDriver(id)
            fetchDrivers()
        } catch (err) {
            console.error(err)
        }
        setActionLoading(null)
    }

    const suspendDriver = async (id: string) => {
        const reason = window.prompt('Enter suspension reason:')
        if (!reason) return

        setActionLoading(id)
        try {
            await adminApi.suspendDriver(id, reason)
            fetchDrivers()
        } catch (err) {
            console.error(err)
        }
        setActionLoading(null)
    }

    return (
        <DashboardLayout>
            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-bold text-zinc-900 dark:text-white">Driver Management</h1>
                    <p className="mt-1 text-sm text-zinc-500">Approve, manage, and monitor all platform drivers</p>
                </div>

                {/* Search + Filters */}
                <div className="flex items-center gap-3 flex-wrap">
                    <div className="relative flex-1 max-w-sm">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-zinc-400" />
                        <input
                            type="text"
                            placeholder="Search by name, email, phone, license..."
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && fetchDrivers()}
                            className="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                        />
                    </div>
                    <div className="flex items-center gap-2">
                        <Filter className="h-4 w-4 text-zinc-400" />
                        {['', 'pending_verification', 'active', 'suspended'].map((status) => (
                            <button
                                key={status}
                                onClick={() => setStatusFilter(status)}
                                className={`px-3 py-1.5 text-xs font-medium rounded-full border transition-colors ${
                                    statusFilter === status
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700 hover:border-blue-300'
                                }`}
                            >
                                {status ? status.replace('_', ' ') : 'All'}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Drivers Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {loading ? (
                        Array.from({ length: 6 }).map((_, i) => (
                            <div key={i} className="animate-pulse rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
                                <div className="flex items-start gap-4">
                                    <div className="w-12 h-12 rounded-full bg-zinc-200 dark:bg-zinc-700" />
                                    <div className="flex-1 space-y-2">
                                        <div className="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-3/4" />
                                        <div className="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-1/2" />
                                    </div>
                                </div>
                            </div>
                        ))
                    ) : drivers.length === 0 ? (
                        <div className="col-span-full flex flex-col items-center py-12">
                            <UserCheck className="h-10 w-10 text-zinc-300 mb-3" />
                            <p className="text-zinc-500">No drivers found</p>
                        </div>
                    ) : (
                        drivers.map((driver) => (
                            <div
                                key={driver.id}
                                className="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5 hover:shadow-md transition-shadow"
                            >
                                <div className="flex items-start gap-4">
                                    <div className="relative">
                                        <div className="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 text-lg font-bold">
                                            {driver.user_name?.charAt(0)?.toUpperCase() || 'D'}
                                        </div>
                                        {driver.is_online && (
                                            <div className="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white dark:border-zinc-900" />
                                        )}
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center justify-between">
                                            <h3 className="text-sm font-semibold text-zinc-900 dark:text-white truncate">{driver.user_name}</h3>
                                            <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium ${statusStyles[driver.status] || ''}`}>
                                                {driver.status?.replace('_', ' ')}
                                            </span>
                                        </div>
                                        <p className="text-xs text-zinc-500 truncate">{driver.user_email}</p>
                                        <p className="text-xs text-zinc-400">{driver.user_phone}</p>
                                    </div>
                                </div>

                                {/* Stats */}
                                <div className="flex items-center gap-4 mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                                    <div className="flex items-center gap-1">
                                        <Star className="h-3.5 w-3.5 text-amber-500" />
                                        <span className="text-xs font-medium text-zinc-700 dark:text-zinc-300">{driver.rating}</span>
                                    </div>
                                    <div className="text-xs text-zinc-500">{driver.total_deliveries} rides</div>
                                    {driver.is_online && (
                                        <div className="flex items-center gap-1 text-emerald-600">
                                            <Wifi className="h-3 w-3" />
                                            <span className="text-[10px] font-medium">Online</span>
                                        </div>
                                    )}
                                    <span className="text-[10px] text-zinc-400 ml-auto">
                                        {driver.license_number || 'No license'}
                                    </span>
                                </div>

                                {/* Actions */}
                                <div className="flex items-center gap-2 mt-3">
                                    {driver.status === 'pending_verification' && (
                                        <Link
                                            href={`/drivers/verification/${driver.id}`}
                                            className="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"
                                        >
                                            <Eye className="h-3.5 w-3.5" />
                                            Review KYC
                                        </Link>
                                    )}
                                    {driver.status === 'active' && (
                                        <button
                                            onClick={() => suspendDriver(driver.id)}
                                            disabled={actionLoading === driver.id}
                                            className="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-red-600 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 disabled:opacity-50 transition-colors"
                                        >
                                            <XCircle className="h-3.5 w-3.5" />
                                            Suspend
                                        </button>
                                    )}
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>
        </DashboardLayout>
    )
}
