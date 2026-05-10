'use client'

import React, { useEffect, useState } from 'react'
import { adminApi } from '@/lib/axios'
import { 
    Building2, 
    Users, 
    CreditCard, 
    TrendingUp, 
    ShieldCheck, 
    Plus, 
    Search, 
    ArrowUpRight, 
    FileText, 
    Settings2, 
    ChevronRight,
    Briefcase,
    DollarSign,
    Percent,
    CheckCircle2,
    Clock
} from 'lucide-react'

export default function OrganizationsHub() {
    const [organizations, setOrganizations] = useState<any[]>([])
    const [loading, setLoading] = useState(true)
    const [searchQuery, setSearchQuery] = useState('')
    const [selectedOrg, setSelectedOrg] = useState<any>(null)
    const [isCreating, setIsCreating] = useState(false)

    // Form states for new org
    const [newOrg, setNewOrg] = useState({
        name: '',
        billing_email: '',
        billing_type: 'PREPAID',
        credit_limit: 0,
        initial_deposit: 0
    })

    useEffect(() => {
        loadOrganizations()
    }, [searchQuery])

    const loadOrganizations = async () => {
        try {
            const res = await adminApi.getOrganizations(searchQuery)
            setOrganizations(res.data.data.data)
        } catch (error) {
            console.error('Failed to load organizations:', error)
        } finally {
            setLoading(false)
        }
    }

    const handleCreateOrg = async (e: React.FormEvent) => {
        e.preventDefault()
        try {
            await adminApi.createOrganization(newOrg)
            setIsCreating(false)
            loadOrganizations()
            setNewOrg({ name: '', billing_email: '', billing_type: 'PREPAID', credit_limit: 0, initial_deposit: 0 })
        } catch (error) {
            console.error('Failed to create organization:', error)
        }
    }

    return (
        <div className="min-h-screen bg-[#F8FAFC]">
            {/* Enterprise Header */}
            <div className="bg-[#0F172A] text-white p-12 relative overflow-hidden">
                {/* Abstract geometric background */}
                <div className="absolute right-0 bottom-0 w-1/2 h-full bg-gradient-to-t from-blue-600/10 to-transparent skew-x-12 transform translate-x-32 translate-y-12"></div>
                
                <div className="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                    <div>
                        <div className="flex items-center gap-3 mb-3">
                            <div className="bg-blue-600 p-2.5 rounded-2xl shadow-lg shadow-blue-600/30">
                                <Building2 size={28} />
                            </div>
                            <h1 className="text-4xl font-black tracking-tight">ENTERPRISE HUB</h1>
                        </div>
                        <p className="text-slate-400 font-medium max-w-xl text-lg">
                            Manage your corporate logistics partners, multi-tenant credit limits, and specialized enterprise billing configurations.
                        </p>
                    </div>
                    
                    <button 
                        onClick={() => setIsCreating(true)}
                        className="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-3xl font-black text-sm uppercase tracking-widest flex items-center gap-3 transition-all shadow-xl shadow-blue-600/20 active:scale-95"
                    >
                        <Plus size={20} />
                        Register New Partner
                    </button>
                </div>

                {/* KPI Metrics Strip */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-8 mt-12 pt-12 border-t border-slate-800">
                    <div>
                        <p className="text-slate-500 text-xs font-black uppercase tracking-widest mb-1">Managed Partners</p>
                        <p className="text-3xl font-black text-white">{organizations.length}</p>
                    </div>
                    <div>
                        <p className="text-slate-500 text-xs font-black uppercase tracking-widest mb-1">Active Staff</p>
                        <p className="text-3xl font-black text-white">
                            {organizations.reduce((acc, org) => acc + (org.members_count || 0), 0)}
                        </p>
                    </div>
                    <div>
                        <p className="text-slate-500 text-xs font-black uppercase tracking-widest mb-1">MTD B2B Volume</p>
                        <p className="text-3xl font-black text-blue-400">GHS 124.5k</p>
                    </div>
                    <div>
                        <p className="text-slate-500 text-xs font-black uppercase tracking-widest mb-1">Credit Utilization</p>
                        <p className="text-3xl font-black text-orange-400">62%</p>
                    </div>
                </div>
            </div>

            <main className="max-w-[1600px] mx-auto p-12 -mt-8 relative z-20">
                <div className="flex flex-col lg:flex-row gap-8">
                    {/* Organizations List */}
                    <div className="lg:col-span-8 flex-1">
                        <div className="bg-white rounded-[40px] border border-slate-200 shadow-2xl shadow-slate-200/50 overflow-hidden">
                            <div className="p-8 border-b border-slate-100 flex justify-between items-center bg-white sticky top-0 z-30">
                                <div className="flex items-center gap-4 flex-1 max-w-md">
                                    <div className="relative w-full">
                                        <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                                        <input 
                                            type="text" 
                                            placeholder="Search by name, Tax ID, or email..."
                                            value={searchQuery}
                                            onChange={(e) => setSearchQuery(e.target.value)}
                                            className="w-full pl-12 pr-4 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-blue-500 outline-none font-medium text-slate-800 transition-all"
                                        />
                                    </div>
                                </div>
                                <div className="flex items-center gap-4">
                                    <div className="flex p-1 bg-slate-100 rounded-xl">
                                        <button className="px-4 py-2 text-xs font-black rounded-lg bg-white shadow-sm text-blue-600 uppercase">Active</button>
                                        <button className="px-4 py-2 text-xs font-black text-slate-400 uppercase">Archived</button>
                                    </div>
                                </div>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="w-full text-left">
                                    <thead>
                                        <tr className="bg-slate-50/50">
                                            <th className="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Enterprise Profile</th>
                                            <th className="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Billing Model</th>
                                            <th className="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Utilization</th>
                                            <th className="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Logistics Hub</th>
                                            <th className="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest"></th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-50">
                                        {loading ? (
                                            <tr>
                                                <td colSpan={5} className="py-24 text-center">
                                                    <div className="inline-flex flex-col items-center">
                                                        <Briefcase className="text-slate-200 animate-pulse mb-4" size={48} />
                                                        <p className="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Initializing Organizational Schema...</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        ) : organizations.length === 0 ? (
                                            <tr>
                                                <td colSpan={5} className="py-24 text-center">
                                                    <p className="text-slate-400 font-bold uppercase tracking-widest text-[10px]">No Corporate Partners Found</p>
                                                </td>
                                            </tr>
                                        ) : (
                                            organizations.map((org) => (
                                                <tr key={org.id} className="hover:bg-slate-50/50 transition-colors group cursor-pointer" onClick={() => setSelectedOrg(org)}>
                                                    <td className="px-8 py-6">
                                                        <div className="flex items-center gap-4">
                                                            <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-500 shadow-sm border border-white">
                                                                {org.logo_url ? <img src={org.logo_url} className="w-8 h-8 object-contain" /> : <Building2 size={24} />}
                                                            </div>
                                                            <div>
                                                                <h4 className="font-black text-slate-800 text-lg uppercase tracking-tight">{org.name}</h4>
                                                                <p className="text-xs text-slate-500 font-bold mt-0.5">{org.billing_email}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-8 py-6">
                                                        <div className="inline-flex items-center gap-2 px-3 py-1 bg-white rounded-full border border-slate-200 shadow-sm group-hover:border-blue-200 transition-all">
                                                            {org.billing_type === 'PREPAID' ? <DollarSign size={14} className="text-green-500" /> : <Clock size={14} className="text-blue-500" />}
                                                            <span className="text-[10px] font-black text-slate-700">{org.billing_type}</span>
                                                        </div>
                                                    </td>
                                                    <td className="px-8 py-6">
                                                        <div className="w-32">
                                                            <div className="flex justify-between items-center mb-1.5">
                                                                <span className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Balance</span>
                                                                <span className="text-xs font-black text-slate-700">GHS {org.balance}</span>
                                                            </div>
                                                            <div className="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                                                <div 
                                                                    className={`h-full transition-all duration-1000 ${org.balance > 1000 ? 'bg-green-500' : 'bg-orange-500'}`} 
                                                                    style={{ width: '75%' }}
                                                                ></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-8 py-6">
                                                        <div className="flex gap-4">
                                                            <div className="text-center">
                                                                <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Members</p>
                                                                <p className="text-lg font-black text-slate-800">{org.members_count || 0}</p>
                                                            </div>
                                                            <div className="text-center">
                                                                <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rides</p>
                                                                <p className="text-lg font-black text-slate-800">{org.ride_requests_count || 0}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-8 py-6 text-right">
                                                        <div className="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <button className="p-3 bg-white hover:bg-blue-50 text-slate-400 hover:text-blue-600 rounded-2xl border border-slate-200 shadow-sm transition-all active:scale-90">
                                                                <Settings2 size={18} />
                                                            </button>
                                                            <button className="p-3 bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-600/20 transition-all active:scale-95">
                                                                <ArrowUpRight size={18} />
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
                    </div>

                    {/* Quick Access Sidebar / Detail Context */}
                    <div className="w-full lg:w-[400px] space-y-8">
                        {/* New Org Modal Overlays could go here, but let's implement inline creation or static helper */}
                        <div className="bg-white p-8 rounded-[40px] border border-slate-200 shadow-2xl shadow-slate-200/50">
                            <h3 className="text-xl font-black text-slate-800 mb-6 flex items-center gap-3">
                                <Plus size={24} className="text-blue-600" />
                                Onboard Enterprise
                            </h3>
                            <form onSubmit={handleCreateOrg} className="space-y-5">
                                <div>
                                    <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Business Name</label>
                                    <input 
                                        type="text" 
                                        required
                                        value={newOrg.name}
                                        onChange={(e) => setNewOrg({...newOrg, name: e.target.value})}
                                        className="w-full p-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-blue-500 outline-none font-medium text-slate-800"
                                        placeholder="Globex Corporation"
                                    />
                                </div>
                                <div>
                                    <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Billing Email</label>
                                    <input 
                                        type="email" 
                                        required
                                        value={newOrg.billing_email}
                                        onChange={(e) => setNewOrg({...newOrg, billing_email: e.target.value})}
                                        className="w-full p-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-blue-500 outline-none font-medium text-slate-800"
                                        placeholder="finance@globex.com"
                                    />
                                </div>
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Billing Model</label>
                                        <select 
                                            value={newOrg.billing_type}
                                            onChange={(e) => setNewOrg({...newOrg, billing_type: e.target.value})}
                                            className="w-full p-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-800 text-sm appearance-none"
                                        >
                                            <option value="PREPAID">PREPAID</option>
                                            <option value="POSTPAID">POSTPAID</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label className="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Initial {newOrg.billing_type === 'PREPAID' ? 'Deposit' : 'Limit'}</label>
                                        <input 
                                            type="number" 
                                            value={newOrg.billing_type === 'PREPAID' ? newOrg.initial_deposit : newOrg.credit_limit}
                                            onChange={(e) => setNewOrg({...newOrg, [newOrg.billing_type === 'PREPAID' ? 'initial_deposit' : 'credit_limit']: parseFloat(e.target.value)})}
                                            className="w-full p-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-blue-500 outline-none font-medium text-slate-800"
                                            placeholder="5000"
                                        />
                                    </div>
                                </div>
                                <button type="submit" className="w-full py-5 bg-[#0F172A] text-white rounded-[24px] font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-slate-900/20 hover:bg-slate-800 transition-all active:scale-[0.98] mt-4 flex items-center justify-center gap-3">
                                    <ShieldCheck size={20} />
                                    Validate & Provision
                                </button>
                            </form>
                        </div>

                        {/* Recent Corporate Activity Widget */}
                        <div className="bg-white p-8 rounded-[40px] border border-slate-200 shadow-2xl shadow-slate-200/50">
                            <h3 className="text-xl font-black text-slate-800 mb-6 font-display uppercase tracking-tight italic">Global Insights</h3>
                            <div className="space-y-6">
                                <div className="flex items-start gap-4">
                                    <div className="p-3 bg-blue-50 rounded-2xl text-blue-600 border border-blue-100 flex-shrink-0">
                                        <TrendingUp size={20} />
                                    </div>
                                    <div>
                                        <p className="text-xs font-black text-slate-500 uppercase tracking-widest mb-1">Weekly Growth</p>
                                        <h5 className="text-lg font-black text-slate-800">+14% Corporate Spend</h5>
                                        <p className="text-[10px] text-slate-400 font-bold">Driven by WADEX Enterprise Logistics Hub adoption.</p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-4">
                                    <div className="p-3 bg-green-50 rounded-2xl text-green-600 border border-green-100 flex-shrink-0">
                                        <CreditCard size={20} />
                                    </div>
                                    <div>
                                        <p className="text-xs font-black text-slate-500 uppercase tracking-widest mb-1">Billing Health</p>
                                        <h5 className="text-lg font-black text-slate-800">98.2% Collection Rate</h5>
                                        <p className="text-[10px] text-slate-400 font-bold">Automated direct-debit cycles working optimally.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    )
}
