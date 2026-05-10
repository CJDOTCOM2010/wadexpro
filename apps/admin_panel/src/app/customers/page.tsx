'use client'

import { useEffect, useState } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { Users, Search, Shield, UserX, UserCheck } from 'lucide-react'

interface User {
    id: string
    name: string
    email: string
    phone: string
    user_type: string
    is_active: boolean
    is_verified: boolean
    created_at: string
    last_login_at: string | null
}

export default function CustomersPage() {
    const [users, setUsers] = useState<User[]>([])
    const [loading, setLoading] = useState(true)
    const [search, setSearch] = useState('')
    const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0 })

    const fetchUsers = (page = 1) => {
        setLoading(true)
        const params: Record<string, string> = { type: 'customer', page: String(page), per_page: '25' }
        if (search) params.search = search

        adminApi.getUsers(params)
            .then(res => {
                setUsers(res.data.data || [])
                setPagination({
                    current_page: res.data.current_page || 1,
                    last_page: res.data.last_page || 1,
                    total: res.data.total || 0,
                })
            })
            .catch(console.error)
            .finally(() => setLoading(false))
    }

    useEffect(() => { fetchUsers() }, [])

    const toggleStatus = async (id: string) => {
        try {
            await adminApi.toggleUserStatus(id)
            fetchUsers(pagination.current_page)
        } catch (err) {
            console.error(err)
        }
    }

    return (
        <DashboardLayout>
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-zinc-900 dark:text-white">Customer Management</h1>
                        <p className="mt-1 text-sm text-zinc-500">{pagination.total} registered customers</p>
                    </div>
                </div>

                {/* Search */}
                <div className="relative max-w-md">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-zinc-400" />
                    <input
                        type="text"
                        placeholder="Search by name, email, or phone..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        onKeyDown={(e) => e.key === 'Enter' && fetchUsers()}
                        className="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                    />
                </div>

                {/* Table */}
                <div className="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                    <table className="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                        <thead className="bg-zinc-50 dark:bg-zinc-800/50">
                            <tr>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Customer</th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Phone</th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Status</th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Verified</th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Joined</th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Last Login</th>
                                <th className="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-zinc-100 dark:divide-zinc-800">
                            {loading ? (
                                <tr><td colSpan={7} className="px-4 py-12 text-center">
                                    <div className="h-6 w-6 animate-spin rounded-full border-2 border-blue-600 border-t-transparent mx-auto" />
                                </td></tr>
                            ) : users.length === 0 ? (
                                <tr><td colSpan={7} className="px-4 py-12 text-center text-zinc-500">
                                    <Users className="h-8 w-8 mx-auto mb-2 text-zinc-300" />
                                    <p>No customers found</p>
                                </td></tr>
                            ) : users.map((user) => (
                                <tr key={user.id} className="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td className="px-4 py-3">
                                        <div className="flex items-center gap-3">
                                            <div className="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xs font-bold">
                                                {user.name?.charAt(0)?.toUpperCase() || '?'}
                                            </div>
                                            <div>
                                                <p className="text-sm font-medium text-zinc-900 dark:text-white">{user.name}</p>
                                                <p className="text-xs text-zinc-500">{user.email}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{user.phone || '--'}</td>
                                    <td className="px-4 py-3">
                                        <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${
                                            user.is_active
                                                ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
                                        }`}>
                                            {user.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                    </td>
                                    <td className="px-4 py-3">
                                        {user.is_verified ? (
                                            <Shield className="h-4 w-4 text-emerald-500" />
                                        ) : (
                                            <Shield className="h-4 w-4 text-zinc-300" />
                                        )}
                                    </td>
                                    <td className="px-4 py-3 text-xs text-zinc-500">
                                        {new Date(user.created_at).toLocaleDateString()}
                                    </td>
                                    <td className="px-4 py-3 text-xs text-zinc-500">
                                        {user.last_login_at ? new Date(user.last_login_at).toLocaleDateString() : 'Never'}
                                    </td>
                                    <td className="px-4 py-3">
                                        <button
                                            onClick={() => toggleStatus(user.id)}
                                            className={`p-1.5 rounded-lg transition-colors ${
                                                user.is_active
                                                    ? 'hover:bg-red-50 dark:hover:bg-red-900/20 text-red-500'
                                                    : 'hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-emerald-500'
                                            }`}
                                            title={user.is_active ? 'Deactivate' : 'Activate'}
                                        >
                                            {user.is_active ? <UserX className="h-4 w-4" /> : <UserCheck className="h-4 w-4" />}
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </DashboardLayout>
    )
}
