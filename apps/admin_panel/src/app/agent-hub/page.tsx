'use client'

import React, { useState } from 'react'
import { 
    Store, 
    CreditCard, 
    UserPlus, 
    Search,
    MapPin,
    Car,
    Gift,
    Clock,
    CheckCircle2
} from 'lucide-react'

export default function AgentHub() {
    const [activeTab, setActiveTab] = useState('TOPUP') // TOPUP, BOOKING, VOUCHER

    return (
        <div className="min-h-screen bg-[#F8FAFC]">
            {/* Agent Header */}
            <div className="bg-[#0F172A] text-white p-12 relative overflow-hidden">
                <div className="absolute right-0 bottom-0 w-1/3 h-full bg-gradient-to-t from-emerald-600/10 to-transparent skew-x-12 transform translate-x-24"></div>
                
                <div className="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                    <div>
                        <div className="flex items-center gap-3 mb-3">
                            <div className="bg-emerald-600 p-2.5 rounded-2xl shadow-lg shadow-emerald-600/20">
                                <Store size={28} />
                            </div>
                            <h1 className="text-4xl font-black tracking-tight">WADEX-OFFICE HUB</h1>
                        </div>
                        <p className="text-slate-400 font-medium max-w-xl text-lg">
                            In-store offline terminal for cash top-ups, manual client bookings, and voucher issuance.
                        </p>
                    </div>
                    
                    <div className="flex flex-col items-end gap-2 text-right">
                        <div className="bg-white/10 px-4 py-2 rounded-xl backdrop-blur-md border border-white/10">
                            <p className="text-xs text-slate-400 font-bold uppercase tracking-widest">Active Branch</p>
                            <p className="text-emerald-400 font-black text-lg">ACCRA CENTRAL</p>
                        </div>
                    </div>
                </div>

                {/* Sub Navigation */}
                <div className="flex items-center gap-4 mt-12 relative z-10">
                    <TabButton 
                        active={activeTab === 'TOPUP'} 
                        icon={<CreditCard size={18} />} 
                        label="Cash Top-up" 
                        onClick={() => setActiveTab('TOPUP')} 
                    />
                    <TabButton 
                        active={activeTab === 'BOOKING'} 
                        icon={<Car size={18} />} 
                        label="Manual Booking" 
                        onClick={() => setActiveTab('BOOKING')} 
                    />
                    <TabButton 
                        active={activeTab === 'VOUCHER'} 
                        icon={<Gift size={18} />} 
                        label="Issue Voucher" 
                        onClick={() => setActiveTab('VOUCHER')} 
                    />
                </div>
            </div>

            <main className="max-w-[1600px] mx-auto p-12 -mt-8 relative z-20">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    <div className="lg:col-span-8 bg-white rounded-[40px] border border-slate-200 p-10 shadow-2xl shadow-slate-200/50 min-h-[500px]">
                        {activeTab === 'TOPUP' && <TopUpFlow />}
                        {activeTab === 'BOOKING' && <OfflineBookingFlow />}
                        {activeTab === 'VOUCHER' && <VoucherFlow />}
                    </div>

                    {/* Agent Sidebar */}
                    <div className="lg:col-span-4 space-y-8">
                        <div className="bg-white p-8 rounded-[32px] border border-slate-200 shadow-xl shadow-slate-200/50">
                            <h4 className="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Today's Collections</h4>
                            <div className="text-4xl font-black text-slate-800 tracking-tight">
                                GHS 4,250<span className="text-lg text-slate-400">.00</span>
                            </div>
                            <div className="mt-6 space-y-4">
                                <div className="flex justify-between items-center text-sm font-bold text-slate-600">
                                    <span>Top-ups Processed</span>
                                    <span>24</span>
                                </div>
                                <div className="flex justify-between items-center text-sm font-bold text-slate-600">
                                    <span>Offline Rides Booked</span>
                                    <span>7</span>
                                </div>
                            </div>
                        </div>

                        <div className="bg-emerald-50 p-8 rounded-[32px] border border-emerald-100">
                            <div className="flex items-center gap-4 mb-4">
                                <div className="bg-emerald-500 text-white p-2 rounded-xl">
                                    <CheckCircle2 size={24} />
                                </div>
                                <h4 className="font-black text-slate-800">Terminal Ready</h4>
                            </div>
                            <p className="text-sm font-medium text-slate-600">
                                System is online and connected to the logistics gateway. Ensure all cash received is verified before committing top-ups.
                            </p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    )
}

function TabButton({ active, icon, label, onClick }: any) {
    return (
        <button 
            onClick={onClick}
            className={`flex items-center gap-3 px-6 py-3 rounded-2xl font-black text-sm transition-all focus:outline-none ${
                active 
                    ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' 
                    : 'bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white'
            }`}
        >
            {icon}
            {label}
        </button>
    )
}

function TopUpFlow() {
    return (
        <div className="max-w-2xl">
            <h3 className="text-2xl font-black text-slate-800 tracking-tight mb-8">Process Cash Top-up</h3>
            
            <div className="space-y-6">
                <div>
                    <label className="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Customer Search</label>
                    <div className="relative">
                        <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" size={20} />
                        <input 
                            type="text" 
                            placeholder="Phone Number or WADEX ID" 
                            className="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 pl-12 pr-4 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                    </div>
                </div>

                <div>
                    <label className="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Cash Amount Received</label>
                    <div className="relative">
                        <span className="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 font-black text-xl">GHS</span>
                        <input 
                            type="number" 
                            placeholder="0.00" 
                            className="w-full bg-slate-50 border border-slate-200 rounded-2xl py-6 pl-20 pr-6 text-slate-800 font-black text-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                    </div>
                </div>

                <div className="pt-8">
                    <button className="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-black py-5 rounded-2xl transition-all shadow-xl shadow-emerald-500/20 flex items-center justify-center gap-3 active:scale-95">
                        <CreditCard size={20} />
                        CREDIT CUSTOMER WALLET
                    </button>
                </div>
            </div>
        </div>
    )
}

function OfflineBookingFlow() {
    return (
        <div className="max-w-2xl">
            <h3 className="text-2xl font-black text-slate-800 tracking-tight mb-8">Book Ride for Walk-in</h3>
            
            <div className="space-y-6">
                <div className="grid grid-cols-2 gap-6">
                    <div>
                        <label className="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Guest Name</label>
                        <input 
                            type="text" 
                            className="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                    </div>
                    <div>
                        <label className="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Guest Phone</label>
                        <input 
                            type="text" 
                            className="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                    </div>
                </div>

                <div>
                    <label className="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Pickup Location</label>
                    <div className="relative">
                        <MapPin className="absolute left-4 top-1/2 -translate-y-1/2 text-blue-500" size={20} />
                        <input 
                            type="text" 
                            defaultValue="Accra Central Branch (Current Location)"
                            className="w-full bg-blue-50 border border-blue-100 rounded-2xl py-4 pl-12 pr-4 text-blue-800 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                    </div>
                </div>

                <div>
                    <label className="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Destination</label>
                    <div className="relative">
                        <MapPin className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" size={20} />
                        <input 
                            type="text" 
                            placeholder="Enter drop-off address"
                            className="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 pl-12 pr-4 text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                        />
                    </div>
                </div>

                <div className="pt-8">
                    <button className="w-full bg-slate-800 hover:bg-slate-900 text-white font-black py-5 rounded-2xl transition-all shadow-xl shadow-slate-900/20 flex items-center justify-center gap-3 active:scale-95">
                        <Car size={20} />
                        DISPATCH NEAREST DRIVER
                    </button>
                </div>
            </div>
        </div>
    )
}

function VoucherFlow() {
    return (
        <div className="max-w-2xl text-center py-12">
            <div className="bg-emerald-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <Gift className="text-emerald-500" size={40} />
            </div>
            <h3 className="text-2xl font-black text-slate-800 tracking-tight mb-4">Print Physical Voucher</h3>
            <p className="text-slate-500 font-medium max-w-md mx-auto mb-10">
                Generate a secure, single-use voucher code to print for offline gifting or corporate distribution.
            </p>
            
            <button className="bg-emerald-500 hover:bg-emerald-600 text-white font-black py-4 px-10 rounded-2xl transition-all shadow-xl shadow-emerald-500/20 inline-flex items-center gap-3 active:scale-95">
                <Gift size={20} />
                GENERATE & PRINT VOUCHER
            </button>
        </div>
    )
}
