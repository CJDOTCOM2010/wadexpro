'use client'

import { useState } from 'react'
import { useAuth } from '@/hooks/useAuth'
import { Button } from '@/components/ui/button'

export default function LoginPage() {
    const [email, setEmail] = useState('superadmin@wadexp.com')
    const [password, setPassword] = useState('WadExp@Admin2024!')
    const [errors, setErrors] = useState<any>([])
    const [status, setStatus] = useState<string | null>(null)

    const { login, isLoading } = useAuth({
        middleware: 'guest',
        redirectIfAuthenticated: '/dashboard',
    })

    const submitForm = (event: React.FormEvent) => {
        event.preventDefault()
        login({ email, password, setErrors, setStatus })
    }

    return (
        <div className="flex min-h-screen bg-zinc-50 dark:bg-zinc-950">
            {/* Left Side: Login Form (30%) */}
            <div className="w-full lg:w-[30%] flex flex-col justify-center p-8 lg:p-12 bg-white dark:bg-zinc-900 shadow-2xl z-10 border-r border-zinc-200 dark:border-zinc-800">
                <div className="w-full max-w-sm mx-auto space-y-8">
                    <div>
                        <h1 className="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white">
                            WADEXP <span className="text-blue-600">Admin</span>
                        </h1>
                        <p className="mt-3 text-zinc-600 dark:text-zinc-400">
                            Logistics Control Tower v1.0
                        </p>
                    </div>

                    <form className="mt-10 space-y-6" onSubmit={submitForm}>
                        <div className="space-y-5">
                            <div>
                                <label className="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                    Operator Email
                                </label>
                                <input
                                    type="email"
                                    value={email}
                                    onChange={e => setEmail(e.target.value)}
                                    placeholder="operator@wadexp.com"
                                    className="mt-2 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 px-4 py-3 text-zinc-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all sm:text-sm"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                    Secure Password
                                </label>
                                <input
                                    type="password"
                                    value={password}
                                    onChange={e => setPassword(e.target.value)}
                                    className="mt-2 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 px-4 py-3 text-zinc-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all sm:text-sm"
                                    required
                                />
                            </div>
                        </div>

                        {errors.general && (
                            <div className="bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/20 p-3 rounded-lg text-red-600 dark:text-red-400 text-xs text-center font-medium">
                                {errors.general}
                            </div>
                        )}

                        <Button
                            type="submit"
                            disabled={isLoading}
                            className="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition-all active:scale-[0.98]"
                        >
                            {isLoading ? (
                                <span className="flex items-center gap-2">
                                    <span className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                                    Authorizing...
                                </span>
                            ) : 'Enter Systems'}
                        </Button>
                    </form>

                    <footer className="pt-10 text-xs text-zinc-400">
                        &copy; 2026 WADEXP Logistics. Authorized Personnel Only.
                    </footer>
                </div>
            </div>

            {/* Right Side: Visual Asset (70%) */}
            <div className="hidden lg:flex lg:w-[70%] relative overflow-hidden bg-zinc-900">
                <img 
                    src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=2070&auto=format&fit=crop" 
                    alt="Logistics background"
                    className="absolute inset-0 w-full h-full object-cover opacity-60 scale-105 animate-pulse"
                    style={{ animationDuration: '8s' }}
                />
                
                {/* Dynamic Blur Overlays */}
                <div className="absolute inset-0 bg-gradient-to-tr from-blue-900/40 via-transparent to-zinc-950/60" />
                
                <div className="relative z-10 m-auto max-w-2xl p-12 backdrop-blur-md bg-white/5 border border-white/10 rounded-3xl shadow-2xl">
                    <div className="flex items-center gap-3 mb-6">
                        <div className="w-12 h-1 gap-1 flex flex-col">
                            <div className="h-full bg-blue-500 rounded-full" />
                            <div className="h-full bg-zinc-400/30 rounded-full" />
                        </div>
                        <span className="text-xs font-bold uppercase tracking-widest text-blue-400">System Information</span>
                    </div>
                    <h2 className="text-5xl font-extrabold text-white leading-tight">
                        Orchestrating the <br/>
                        <span className="text-blue-400 underline decoration-blue-500/30 decoration-8 underline-offset-8">Future of Logistics.</span>
                    </h2>
                    <p className="mt-8 text-xl text-zinc-300 font-light leading-relaxed">
                        WADEXP Logistics provides real-time visibility and AI-powered route optimization 
                        across the West African corridor. Monitor, manage, and move with precision.
                    </p>
                    <div className="mt-12 flex gap-12">
                        <div>
                            <p className="text-lg font-bold text-white">500k+</p>
                            <p className="text-xs text-zinc-400 uppercase tracking-wide">Monthly Trips</p>
                        </div>
                        <div>
                            <p className="text-lg font-bold text-white">99.9%</p>
                            <p className="text-xs text-zinc-400 uppercase tracking-wide">Uptime SLA</p>
                        </div>
                        <div>
                            <p className="text-lg font-bold text-white">Accra, Lomé, Abidjan</p>
                            <p className="text-xs text-zinc-400 uppercase tracking-wide">Primary Hubs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
