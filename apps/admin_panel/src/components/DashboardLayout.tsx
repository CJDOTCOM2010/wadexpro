'use client'

import { Sidebar } from './Sidebar'
import { useAuth } from '@/hooks/useAuth'
import { SocketProvider } from '@/hooks/useSocket'
import { GlobalSOSOverlay } from './GlobalSOSOverlay'

export default function DashboardLayout({ children }: { children: React.ReactNode }) {
    const { user } = useAuth({ middleware: 'auth' })

    if (!user) {
        return (
            <div className="flex h-screen items-center justify-center bg-zinc-50 dark:bg-zinc-950">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
        )
    }

    return (
        <SocketProvider namespace="/admin">
            <div className="flex h-screen bg-zinc-50 dark:bg-zinc-950 font-sans overflow-hidden">
                <Sidebar />
                <main className="flex-1 flex flex-col min-w-0 overflow-hidden">
                    <header className="h-16 flex items-center justify-between px-8 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 z-10">
                        <div className="flex items-center gap-4">
                            <h2 className="text-sm font-medium text-zinc-500">Logistics Tower</h2>
                            <span className="h-4 w-[1px] bg-zinc-300 dark:bg-zinc-700"></span>
                            <span className="text-sm font-semibold text-zinc-900 dark:text-white">Active session</span>
                        </div>
                        <div className="flex items-center gap-4">
                            <div className="flex flex-col items-end">
                                <span className="text-sm font-bold text-zinc-900 dark:text-white">{user.name}</span>
                                <span className="text-xs text-zinc-500 uppercase tracking-widest">{user.role || 'Admin'}</span>
                            </div>
                            <div className="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 font-bold border-2 border-white dark:border-zinc-800 shadow-sm">
                                {user.name.charAt(0)}
                            </div>
                        </div>
                    </header>
                    <div className="flex-1 overflow-y-auto p-8 custom-scrollbar">
                        {children}
                    </div>
                </main>
                <GlobalSOSOverlay />
            </div>
        </SocketProvider>
    )
}
