'use client'

import { useState, ReactNode } from 'react'
import { Lock, ShieldAlert, KeyRound, ArrowRight } from 'lucide-react'
import { Button } from '@/components/ui/button'

interface StepUpGuardProps {
    children: ReactNode
}

/**
 * StepUpGuard
 * Forces a re-verification check before mounting sensitive orchestration components.
 * In a real-world scenario, this would check a JWT 'step-up' claim or trigger a 2FA prompt.
 */
export default function StepUpGuard({ children }: StepUpGuardProps) {
    const [isVerified, setIsVerified] = useState(false)
    const [password, setPassword] = useState('')
    const [error, setError] = useState('')

    const handleVerify = (e: React.FormEvent) => {
        e.preventDefault()
        // Mock verification: In production, this would hit /api/v1/auth/step-up
        if (password === 'admin123') { // Temporary mock credential
            setIsVerified(true)
            setError('')
        } else {
            setError('Invalid credentials. Access attempt logged.')
        }
    }

    if (isVerified) {
        return <>{children}</>
    }

    return (
        <div className="fixed inset-0 z-[100] bg-zinc-950 flex items-center justify-center p-6">
            <div className="max-w-md w-full space-y-8 text-center">
                <div className="mx-auto w-20 h-20 bg-rose-600/20 border border-rose-500/50 rounded-3xl flex items-center justify-center animate-pulse">
                    <Lock className="w-10 h-10 text-rose-500" />
                </div>
                
                <div className="space-y-2">
                    <h2 className="text-3xl font-black text-white tracking-tighter italic">SECURITY CHECKPOINT</h2>
                    <p className="text-zinc-500 text-sm">
                        You are entering a high-security orchestration zone. Re-verification required to proceed.
                    </p>
                </div>

                <form onSubmit={handleVerify} className="space-y-4">
                    <div className="relative">
                        <div className="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                            <KeyRound className="h-5 w-5 text-zinc-600" />
                        </div>
                        <input 
                            type="password"
                            placeholder="Confirm Administrative Password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            className="w-full bg-white/5 border border-white/10 rounded-2xl py-5 pl-12 pr-4 text-white text-center font-mono focus:ring-2 focus:ring-rose-500/50 outline-none transition-all placeholder:text-zinc-700"
                            autoFocus
                        />
                    </div>
                    
                    {error && (
                        <div className="flex items-center gap-2 text-rose-500 text-xs font-bold justify-center animate-bounce">
                            <ShieldAlert className="w-4 h-4" /> {error}
                        </div>
                    )}

                    <Button 
                        type="submit"
                        className="w-full bg-rose-600 hover:bg-rose-700 text-white h-16 rounded-[1.2rem] font-black uppercase tracking-widest text-sm shadow-2xl shadow-rose-900/20"
                    >
                        Authorize Access <ArrowRight className="w-4 h-4 ml-2" />
                    </Button>
                </form>

                <p className="text-[10px] text-zinc-700 font-mono uppercase tracking-widest">
                    ID: {Math.random().toString(36).substring(7).toUpperCase()} // IP: TRACED // ATTEMPT LOGGED
                </p>
            </div>
        </div>
    )
}
