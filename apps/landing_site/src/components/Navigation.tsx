import React from 'react'
import Link from 'next/link'
import { Menu, Globe } from 'lucide-react'

export const Navigation = ({ locale }: { locale: string }) => {
    return (
        <nav className="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md z-50 border-b border-zinc-100">
            <div className="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <Link href={`/${locale}`} className="flex items-center gap-2">
                    {/* Logo Placeholder */}
                    <div className="h-10 w-10 bg-primary-navy rounded-xl flex items-center justify-center text-primary-gold font-black text-xl">
                        W
                    </div>
                    <span className="text-2xl font-display font-black tracking-tighter text-primary-navy">
                        WADEXP<span className="text-blue-600">.</span>
                    </span>
                </Link>

                <div className="hidden lg:flex items-center gap-10">
                    <Link href="#" className="text-sm font-bold text-zinc-600 hover:text-blue-600 transition-colors">Services</Link>
                    <Link href="#" className="text-sm font-bold text-zinc-600 hover:text-blue-600 transition-colors">Business</Link>
                    <Link href="#" className="text-sm font-bold text-zinc-600 hover:text-blue-600 transition-colors">Safety</Link>
                    <Link href="#" className="text-sm font-bold text-zinc-600 hover:text-blue-600 transition-colors">Help</Link>
                </div>

                <div className="flex items-center gap-4">
                    <div className="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-zinc-50 border border-zinc-200 rounded-lg">
                        <Globe className="h-4 w-4 text-zinc-400" />
                        <span className="text-xs font-bold uppercase text-zinc-600">{locale}</span>
                    </div>
                    <Link href={`/login`} className="text-sm font-bold text-primary-navy hover:text-blue-600 transition-colors">
                        Log In
                    </Link>
                    <Link href={`/register`} className="btn-primary py-2.5 text-sm">
                        Sign Up
                    </Link>
                    <button className="lg:hidden p-2 text-zinc-900 border border-zinc-200 rounded-lg">
                        <Menu className="h-6 w-6" />
                    </button>
                </div>
            </div>
        </nav>
    )
}
