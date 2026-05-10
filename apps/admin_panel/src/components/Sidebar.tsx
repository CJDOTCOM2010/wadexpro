'use client'

import Link from 'next/link'
import { usePathname } from 'next/navigation'
import { cn } from '@/lib/utils'
import {
    LayoutDashboard,
    Users,
    Car,
    MapPin,
    Clock,
    Wallet,
    FileText,
    Activity,
    LogOut,
    Layers,
    Settings,
    ShieldCheck,
    Zap,
    Landmark,
    Map,
    BarChart3,
    Tag,
    TrendingUp,
    Globe,
    Newspaper,
    Image,
    Menu,
    AlertTriangle,
    UserCheck,
    Package,
    ChevronDown,
    ChevronRight,
    type LucideIcon
} from 'lucide-react'
import { useAuth } from '@/hooks/useAuth'
import { useState } from 'react'

interface NavItem {
    name: string
    href: string
    icon: LucideIcon
}

interface NavGroup {
    label: string
    items: NavItem[]
    collapsible?: boolean
}

const navigation: NavGroup[] = [
    {
        label: 'Overview',
        items: [
            { name: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
            { name: 'Live Map', href: '/live-map', icon: Map },
        ],
    },
    {
        label: 'Ride Operations',
        items: [
            { name: 'Rides', href: '/rides', icon: Car },
            { name: 'Drivers', href: '/drivers', icon: UserCheck },
            { name: 'Customers', href: '/customers', icon: Users },
            { name: 'SOS Alerts', href: '/sos', icon: AlertTriangle },
        ],
    },
    {
        label: 'Finance',
        items: [
            { name: 'Payments', href: '/payments', icon: Wallet },
            { name: 'Commissions', href: '/commissions', icon: TrendingUp },
            { name: 'Promo Codes', href: '/promo-codes', icon: Tag },
            { name: 'General Ledger', href: '/accounting/ledger', icon: Landmark },
            { name: 'Invoices', href: '/accounting/invoices', icon: FileText },
        ],
    },
    {
        label: 'Content Management',
        collapsible: true,
        items: [
            { name: 'Pages', href: '/cms/pages', icon: Globe },
            { name: 'Blog / Posts', href: '/cms/blog', icon: Newspaper },
            { name: 'Media Library', href: '/cms/media', icon: Image },
            { name: 'Menus', href: '/cms/menus', icon: Menu },
        ],
    },
    {
        label: 'HR & Operations',
        collapsible: true,
        items: [
            { name: 'Employees', href: '/hr/employees', icon: Users },
            { name: 'Attendance', href: '/hr/attendance', icon: Clock },
            { name: 'Deliveries', href: '/deliveries', icon: Package },
        ],
    },
    {
        label: 'Reports',
        items: [
            { name: 'Analytics', href: '/reports/analytics', icon: BarChart3 },
            { name: 'Activity Log', href: '/reports/logs', icon: Activity },
        ],
    },
]

const systemNav: NavItem[] = [
    { name: 'Module Orchestra', href: '/systems-command/modules', icon: Layers },
    { name: 'Identity Hub', href: '/systems-command/users', icon: ShieldCheck },
    { name: 'Surge Zones', href: '/systems-command/surge', icon: MapPin },
    { name: 'Pricing Config', href: '/systems-command/pricing', icon: TrendingUp },
    { name: 'Vehicle Types', href: '/systems-command/vehicles', icon: Car },
    { name: 'Global Settings', href: '/systems-command/settings', icon: Settings },
]

export function Sidebar() {
    const pathname = usePathname()
    const { user, logout } = useAuth()
    const [collapsed, setCollapsed] = useState<Record<string, boolean>>({})

    const toggleGroup = (label: string) => {
        setCollapsed(prev => ({ ...prev, [label]: !prev[label] }))
    }

    const renderNavItem = (item: NavItem) => {
        const isActive = pathname === item.href || pathname?.startsWith(item.href + '/')
        return (
            <Link
                key={item.name}
                href={item.href}
                className={cn(
                    'group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                    isActive
                        ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400'
                        : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-50'
                )}
            >
                <item.icon className={cn(
                    'mr-3 h-4 w-4 flex-shrink-0 transition-colors',
                    isActive ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-500 dark:text-zinc-500 dark:group-hover:text-zinc-400'
                )} />
                {item.name}
            </Link>
        )
    }

    return (
        <div className="flex h-full w-64 flex-col bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800">
            <div className="flex h-16 items-center px-6 border-b border-zinc-200 dark:border-zinc-800">
                <span className="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    WAD<span className="text-blue-600">EXP</span>
                </span>
                <span className="ml-2 text-[10px] font-bold text-zinc-400 uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-full">
                    Admin
                </span>
            </div>

            <nav className="flex-1 space-y-0.5 px-3 py-4 overflow-y-auto scrollbar-thin">
                {navigation.map((group) => (
                    <div key={group.label} className="pb-3">
                        {group.collapsible ? (
                            <button
                                onClick={() => toggleGroup(group.label)}
                                className="w-full flex items-center justify-between text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-3 mb-1.5 hover:text-zinc-600 transition-colors"
                            >
                                {group.label}
                                {collapsed[group.label] ? (
                                    <ChevronRight className="h-3 w-3" />
                                ) : (
                                    <ChevronDown className="h-3 w-3" />
                                )}
                            </button>
                        ) : (
                            <div className="text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-3 mb-1.5">
                                {group.label}
                            </div>
                        )}
                        {!collapsed[group.label] && (
                            <div className="space-y-0.5">
                                {group.items.map((item) => renderNavItem(item))}
                            </div>
                        )}
                    </div>
                ))}

                {user?.role === 'super_admin' && (
                    <div className="pt-4 border-t border-zinc-100 dark:border-zinc-800/50">
                        <div className="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest px-3 mb-1.5">
                            System Control
                        </div>
                        <div className="space-y-0.5">
                            {systemNav.map((item) => renderNavItem(item))}
                        </div>
                        <button
                            onClick={() => confirm('INITIATE GLOBAL MAINTENANCE MODE? This will disconnect all non-admin sessions.')}
                            className="w-full mt-3 flex items-center gap-3 px-3 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all group border border-dashed border-rose-200 dark:border-rose-800/50"
                        >
                            <Zap className="w-4 h-4 group-hover:animate-pulse" />
                            <span className="text-xs font-bold uppercase tracking-tight">System Lockdown</span>
                        </button>
                    </div>
                )}
            </nav>

            <div className="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {user && (
                    <div className="flex items-center mb-3 px-2">
                        <div className="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xs font-bold">
                            {user.name?.charAt(0)?.toUpperCase() || 'A'}
                        </div>
                        <div className="ml-3 overflow-hidden">
                            <p className="text-sm font-medium text-zinc-900 dark:text-white truncate">{user.name}</p>
                            <p className="text-[10px] text-zinc-500 dark:text-zinc-400 truncate">{user.email}</p>
                        </div>
                    </div>
                )}
                <button
                    onClick={() => logout()}
                    className="flex w-full items-center px-3 py-2 text-sm font-medium text-zinc-600 rounded-lg hover:bg-red-50 hover:text-red-600 dark:text-zinc-400 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-all duration-200"
                >
                    <LogOut className="mr-3 h-4 w-4" />
                    Sign out
                </button>
            </div>
        </div>
    )
}
