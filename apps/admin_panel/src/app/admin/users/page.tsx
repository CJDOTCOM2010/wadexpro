'use client'

import { useState, useEffect } from 'react'
import DashboardLayout from '@/components/DashboardLayout'
import axios from '@/lib/axios'
import { ShieldCheck, UserPlus, Lock, Unlock, Mail, Edit3, Trash2 } from 'lucide-react'
import { Button } from '@/components/ui/button'

interface User {
    id: string
    name: string
    email: string
    user_type: string
    is_active: boolean
    last_login_at: string
}

export default function UserManagementPage() {
    const [users, setUsers] = useState<User[]>([])
    const [isLoading, setIsLoading] = useState(true)

    useEffect(() => {
        fetchUsers()
    }, [])

    const fetchUsers = async () => {
        try {
            const response = await axios.get('/admin/users')
            setUsers(response.data.data)
        } catch (error) {
            console.error('Failed to load identity registry.')
        } finally {
            setIsLoading(false)
        }
    }

    const toggleUserStatus = async (id: string, currentStatus: boolean) => {
        try {
            await axios.patch(`/admin/users/${id}/toggle-status`)
            fetchUsers()
        } catch (error) {
            alert('Failed to update user status.')
        }
    }

    return (
        <DashboardLayout>
            <div className="max-w-7xl mx-auto space-y-8">
                <header className="flex justify-between items-center">
                    <div>
                        <h1 className="text-4xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 flex items-center gap-3">
                            <ShieldCheck className="w-10 h-10 text-blue-600" />
                            User Orchestration
                        </h1>
                        <p className="mt-2 text-lg text-zinc-600 dark:text-zinc-400">
                            Manage system identities and secure access controls.
                        </p>
                    </div>
                    <Button className="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-6 py-6 font-bold shadow-lg shadow-blue-500/20">
                        <UserPlus className="w-5 h-5 mr-3" />
                        Create Global User
                    </Button>
                </header>

                <div className="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                    <table className="w-full text-left border-collapse">
                        <thead>
                            <tr className="border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                                <th className="px-8 py-5 text-xs font-bold uppercase tracking-widest text-zinc-500">User Identity</th>
                                <th className="px-8 py-5 text-xs font-bold uppercase tracking-widest text-zinc-500">Role</th>
                                <th className="px-8 py-5 text-xs font-bold uppercase tracking-widest text-zinc-500">Status</th>
                                <th className="px-8 py-5 text-xs font-bold uppercase tracking-widest text-zinc-500">Last Seen</th>
                                <th className="px-8 py-5 text-right text-xs font-bold uppercase tracking-widest text-zinc-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-zinc-100 dark:divide-zinc-800">
                            {isLoading ? (
                                [1, 2, 3, 4].map(i => (
                                    <tr key={i} className="animate-pulse">
                                        <td colSpan={5} className="px-8 py-10 h-16 bg-zinc-50/50 dark:bg-zinc-900/50" />
                                    </tr>
                                ))
                            ) : (
                                users.map((user) => (
                                    <tr key={user.id} className="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                        <td className="px-8 py-6">
                                            <div className="flex items-center gap-4">
                                                <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-blue-400 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-blue-500/10">
                                                    {user.name.charAt(0)}
                                                </div>
                                                <div>
                                                    <p className="font-bold text-zinc-900 dark:text-white leading-none mb-1">{user.name}</p>
                                                    <div className="flex items-center gap-1.5 text-xs text-zinc-500">
                                                        <Mail className="w-3 h-3" />
                                                        {user.email}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-8 py-6">
                                            <span className={`text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full ${
                                                user.user_type === 'super_admin' ? 'bg-indigo-100 text-indigo-700' :
                                                user.user_type === 'admin' ? 'bg-blue-100 text-blue-700' :
                                                'bg-zinc-100 text-zinc-600'
                                            }`}>
                                                {user.user_type.replace('_', ' ')}
                                            </span>
                                        </td>
                                        <td className="px-8 py-6">
                                            <div className="flex items-center gap-2">
                                                <div className={`w-2 h-2 rounded-full ${user.is_active ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]' : 'bg-red-500'}`} />
                                                <span className="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                                    {user.is_active ? 'Active' : 'Locked'}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-8 py-6 text-sm text-zinc-500 dark:text-zinc-400 font-medium">
                                            {user.last_login_at ? new Date(user.last_login_at).toLocaleDateString() : 'Never'}
                                        </td>
                                        <td className="px-8 py-6 text-right">
                                            <div className="flex items-center justify-end gap-2">
                                                <button 
                                                    onClick={() => toggleUserStatus(user.id, user.is_active)}
                                                    className={`p-2.5 rounded-xl transition-all ${
                                                        user.is_active 
                                                        ? 'text-red-500 hover:bg-red-50 bg-red-50/50' 
                                                        : 'text-emerald-500 hover:bg-emerald-50 bg-emerald-50/50'
                                                    }`}
                                                >
                                                    {user.is_active ? <Lock className="w-4 h-4" /> : <Unlock className="w-4 h-4" />}
                                                </button>
                                                <button className="p-2.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 bg-zinc-100 dark:bg-zinc-800 rounded-xl transition-all">
                                                    <Edit3 className="w-4 h-4" />
                                                </button>
                                                <button className="p-2.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 bg-zinc-100 dark:bg-zinc-800 rounded-xl transition-all">
                                                    <Trash2 className="w-4 h-4" />
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
        </DashboardLayout>
    )
}
