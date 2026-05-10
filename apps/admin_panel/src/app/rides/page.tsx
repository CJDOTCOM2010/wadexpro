'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { Car, Eye, Filter, Search } from 'lucide-react'
import Link from 'next/link'

interface Ride {
    id: string
    customer_name: string
    customer_phone: string
    driver_name: string | null
    driver_phone: string | null
    pickup_address: string
    dropoff_address: string
    vehicle_type: string
    status: string
    estimated_price: string
    final_price: string | null
    created_at: string
}

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    searching: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    driver_assigned: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400',
    driver_arrived: 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
    in_progress: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
}

export default function RidesPage() {
    const [rides, setRides] = useState<Ride[]>([])
    const [loading, setLoading] = useState(true)
    const [statusFilter, setStatusFilter] = useState('')
    const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0 })

    const fetchRides = (page = 1) => {
        setLoading(true)
        const params: Record<string, string> = { page: String(page), per_page: '20' }
        if (statusFilter) params.status = statusFilter

        adminApi.getRides(params)
            .then(res => {
                setRides(res.data.data || [])
                setPagination({
                    current_page: res.data.current_page || 1,
                    last_page: res.data.last_page || 1,
                    total: res.data.total || 0,
                })
            })
            .catch(console.error)
            .finally(() => setLoading(false))
    }

    useEffect(() => { fetchRides() }, [statusFilter])

    return (
        <DashboardLayout>
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-zinc-900 dark:text-white">Ride Management</h1>
                        <p className="mt-1 text-sm text-zinc-500">{pagination.total} total rides</p>
                    </div>
                </div>

                {/* Filters */}
                <div className="flex items-center gap-3 flex-wrap">
                    <div className="flex items-center gap-2 text-sm">
                        <Filter className="h-4 w-4 text-zinc-400" />
                        <span className="text-zinc-500">Status:</span>
                    </div>
                    {['', 'pending', 'searching', 'in_progress', 'completed', 'cancelled'].map((status) => (
                        <button
                            key={status}
                            onClick={() => setStatusFilter(status)}
                            className={`px-3 py-1.5 text-xs font-medium rounded-full border transition-colors ${
                                statusFilter === status
                                    ? 'bg-blue-600 text-white border-blue-600'
                                    : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700 hover:border-blue-300'
                            }`}
                        >
                            {status || 'All'}
                        </button>
                    ))}
                </div>

                {/* Table */}
                <div className="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                            <thead className="bg-zinc-50 dark:bg-zinc-800/50">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Customer</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Driver</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Route</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Type</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Status</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Price</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Date</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider" />
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-zinc-100 dark:divide-zinc-800">
                                {loading ? (
                                    <tr>
                                        <td colSpan={8} className="px-4 py-12 text-center text-zinc-500">
                                            <div className="flex justify-center">
                                                <div className="h-6 w-6 animate-spin rounded-full border-2 border-blue-600 border-t-transparent" />
                                            </div>
                                        </td>
                                    </tr>
                                ) : rides.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} className="px-4 py-12 text-center text-zinc-500">
                                            <Car className="h-8 w-8 mx-auto mb-2 text-zinc-300" />
                                            <p>No rides found</p>
                                        </td>
                                    </tr>
                                ) : (
                                    rides.map((ride) => (
                                        <tr key={ride.id} className="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                            <td className="px-4 py-3">
                                                <p className="text-sm font-medium text-zinc-900 dark:text-white">{ride.customer_name || 'N/A'}</p>
                                                <p className="text-xs text-zinc-500">{ride.customer_phone}</p>
                                            </td>
                                            <td className="px-4 py-3">
                                                <p className="text-sm text-zinc-900 dark:text-white">{ride.driver_name || '--'}</p>
                                                <p className="text-xs text-zinc-500">{ride.driver_phone || ''}</p>
                                            </td>
                                            <td className="px-4 py-3 max-w-[200px]">
                                                <p className="text-xs text-zinc-600 dark:text-zinc-400 truncate">{ride.pickup_address}</p>
                                                <p className="text-xs text-zinc-400 truncate">{ride.dropoff_address}</p>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className="text-xs font-medium text-zinc-600 dark:text-zinc-400 capitalize">{ride.vehicle_type}</span>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[ride.status] || ''}`}>
                                                    {ride.status?.replace('_', ' ')}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className="text-sm font-medium text-zinc-900 dark:text-white">
                                                    GHS {ride.final_price || ride.estimated_price}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-xs text-zinc-500">
                                                {new Date(ride.created_at).toLocaleDateString()}
                                            </td>
                                            <td className="px-4 py-3">
                                                <Link href={`/rides/${ride.id}`} className="p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                                                    <Eye className="h-4 w-4 text-zinc-400" />
                                                </Link>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {pagination.last_page > 1 && (
                        <div className="flex items-center justify-between px-4 py-3 border-t border-zinc-200 dark:border-zinc-800">
                            <p className="text-xs text-zinc-500">
                                Page {pagination.current_page} of {pagination.last_page}
                            </p>
                            <div className="flex gap-2">
                                <button
                                    onClick={() => fetchRides(pagination.current_page - 1)}
                                    disabled={pagination.current_page <= 1}
                                    className="px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-200 dark:border-zinc-700 disabled:opacity-50 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                                >
                                    Previous
                                </button>
                                <button
                                    onClick={() => fetchRides(pagination.current_page + 1)}
                                    disabled={pagination.current_page >= pagination.last_page}
                                    className="px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-200 dark:border-zinc-700 disabled:opacity-50 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                                >
                                    Next
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    )
}
