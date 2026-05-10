'use client'

import { useState, useEffect } from 'react'
import DashboardLayout from '@/components/DashboardLayout'
import axios from '@/lib/axios'
import { ShieldCheck, UserPlus, Lock, Unlock, Mail, Edit3, Trash2, Search } from 'lucide-react'
import { Button } from '@/components/ui/button'

interface User {
    id: string
    name: string
    email: string
    user_type: string
    is_active: boolean
    last_login_at: string
}

export default function UserIdentityPage() {
    const [users, setUsers] = useState<User[]>([])
    const [isLoading, setIsLoading] = useState(true)

    useEffect(() => {
        fetchUsers()
    }, [])

    const fetchUsers = async () => {
        try {
            const response = await axios.get('/orchestrator/users')
            setUsers(response.data.data)
        } catch (error) {
            console.error('Failed to load identity registry.')
        } finally {
            setIsLoading(false)
        }
    }

    const toggleUserStatus = async (id: string, currentStatus: boolean) => {
        try {
            await axios.patch(`/orchestrator/users/${id}/status`, {
                is_active: !currentStatus
            })
            fetchUsers()
        } catch (error) {
            alert('Security Alert: Access denied to identity orchestration.')
        }
    }

    return (
        <DashboardLayout>
            <div className="max-w-7xl mx-auto space-y-8 text-zinc-900 dark:text-zinc-50">
                <header className="flex justify-between items-center border-b border-zinc-200 dark:border-zinc-800 pb-8">
                    <div>
                        <div className="flex items-center gap-2 mb-2">
                             <span className="px-2 py-0.5 bg-zinc-900 dark:bg-white text-[10px] font-bold text-white dark:text-zinc-900 rounded uppercase tracking-tighter">Identity HQ</span>
                             <span className="text-zinc-400 text-xs">/ Systems Command</span>
                        </div>
                        <h1 className="text-4xl font-black tracking-tight flex items-center gap-3">
                            <ShieldCheck className="w-10 h-10 text-zinc-900 dark:text-white" />
                            Identity Orchestra
                        </h1>
                        <p className="mt-2 text-lg text-zinc-600 dark:text-zinc-400">
                             Manage platform-wide identities and granular access levels.
                        </p>
                    </div>
                    <Button className="bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:scale-[1.02] active:scale-95 transition-all h-14 px-8 rounded-2xl font-black uppercase tracking-widest text-xs">
                        <UserPlus className="w-4 h-4 mr-3" />
                         Inject Identity
                    </Button>
                </header>

                <div className="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-2xl overflow-hidden">
                    <table className="w-full text-left">
                        <thead>
                            <tr className="border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50">
                                <th className="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-400">Unique Identity</th>
                                <th className="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-400">Role Classification</th>
                                <th className="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-400">System Status</th>
                                <th className="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-400 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-zinc-100 dark:divide-zinc-800">
                            {isLoading ? (
                                [1, 2, 3].map(i => (
                                    <tr key={i} className="animate-pulse">
                                        <td colSpan={4} className="px-10 py-8 h-20 bg-zinc-50/20 dark:bg-zinc-800/20" />
                                    </tr>
                                ))
                            ) : (
                                users.map((user) => (
                                    <tr key={user.id} className="group hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-all">
                                        <td className="px-10 py-8">
                                            <div className="flex items-center gap-5">
                                                <div className="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-black text-xl text-zinc-400 group-hover:bg-zinc-900 group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-zinc-900 transition-all">
                                                    {user.name.charAt(0)}
                                                </div>
                                                <div>
                                                    <p className="font-black text-lg tracking-tight mb-1">{user.name}</p>
                                                    <p className="text-xs font-medium text-zinc-400">{user.email}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-10 py-8">
                                            <span className={`text-[10px] font-black uppercase tracking-[0.2em] px-4 py-2 rounded-xl ${
                                                user.user_type === 'super_admin' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 
                                                user.user_type === 'admin' ? 'bg-blue-50 text-blue-600' : 'bg-zinc-100 text-zinc-500'
                                            }`}>
                                                {user.user_type}
                                            </span>
                                        </td>
                                        <td className="px-10 py-8">
                                            <div className="flex items-center gap-3">
                                                <div className={`w-2.5 h-2.5 rounded-full ${user.is_active ? 'bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.5)]' : 'bg-red-500 shadow-[0_0_12px_rgba(239,68,68,0.5)]'}`} />
                                                <span className="text-xs font-black uppercase tracking-widest">{user.is_active ? 'Active' : 'Locked'}</span>
                                            </div>
                                        </td>
                                        <td className="px-10 py-8 text-right">
                                            <div className="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300">
                                                <button 
                                                    onClick={() => toggleUserStatus(user.id, user.is_active)}
                                                    className={`w-12 h-12 flex items-center justify-center rounded-2xl transition-all ${user.is_active ? 'bg-red-50 text-red-500 hover:bg-red-500 hover:text-white' : 'bg-emerald-50 text-emerald-500 hover:bg-emerald-500 hover:text-white'}`}
                                                >
                                                    {user.is_active ? <Lock className="w-5 h-5"/> : <Unlock className="w-5 h-5"/>}
                                                </button>
                                                <button className="w-12 h-12 flex items-center justify-center bg-zinc-100 dark:bg-zinc-800 text-zinc-400 rounded-2xl hover:bg-zinc-900 dark:hover:bg-white hover:text-white dark:hover:text-zinc-900 transition-all">
                                                    <Edit3 className="w-5 h-5"/>
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
