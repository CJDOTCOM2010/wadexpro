'use client'

import DashboardLayout from '@/components/DashboardLayout'
import { 
    Table, 
    TableBody, 
    TableCell, 
    TableHead, 
    TableHeader, 
    TableRow 
} from "@/components/ui/table"
import { Badge } from "@/components/ui/badge"
import useSWR from 'swr'
import axios from '@/lib/axios'
import { format } from 'date-fns'

export default function InvoicesPage() {
    const { data: invoices, error } = useSWR('/api/v1/accounting/invoices', () => 
        axios.get('/api/v1/accounting/invoices').then(res => res.data.data)
    )

    return (
        <DashboardLayout>
            <div className="max-w-7xl mx-auto space-y-8">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Customer Invoices</h1>
                        <p className="mt-2 text-zinc-600 dark:text-zinc-400">Manage billing cycles and track regional revenue collections.</p>
                    </div>
                    <div className="flex gap-4">
                        <button className="px-5 py-2.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-semibold rounded-xl transition-all">
                            Export CSV
                        </button>
                    </div>
                </div>

                <div className="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                    <Table>
                        <TableHeader>
                            <TableRow className="bg-zinc-50/50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                                <TableHead>Invoice #</TableHead>
                                <TableHead>Customer</TableHead>
                                <TableHead>Issue Date</TableHead>
                                <TableHead>Amount</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Action</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {invoices?.map((invoice: any) => (
                                <TableRow key={invoice.id} className="border-b border-zinc-100 dark:border-zinc-800/50 hover:bg-zinc-50/50 transition-colors">
                                    <TableCell className="font-mono text-xs font-bold text-blue-600">{invoice.invoice_number}</TableCell>
                                    <TableCell>{invoice.customer?.name}</TableCell>
                                    <TableCell>{format(new Date(invoice.issue_date), 'MMM dd, yyyy')}</TableCell>
                                    <TableCell className="font-semibold text-zinc-900 dark:text-white">
                                        {invoice.currency} {parseFloat(invoice.total).toLocaleString()}
                                    </TableCell>
                                    <TableCell>
                                        <Badge className={cn(
                                            "rounded-full px-2.5 py-0.5 text-[9px] font-bold uppercase",
                                            invoice.status === 'paid' ? "bg-emerald-100 text-emerald-700" : "bg-amber-100 text-amber-700"
                                        )}>
                                            {invoice.status}
                                        </Badge>
                                    </TableCell>
                                    <TableCell className="text-right">
                                        <button className="text-zinc-600 hover:text-blue-600 font-bold uppercase text-[10px] tracking-widest">Download PDF</button>
                                    </TableCell>
                                </TableRow>
                            ))}
                            {(!invoices || invoices.length === 0) && (
                                <TableRow>
                                    <TableCell colSpan={6} className="h-64 text-center text-zinc-500 italic uppercase text-[10px] tracking-widest">
                                        No invoices generated for current filters.
                                    </TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </div>
            </div>
        </DashboardLayout>
    )
}

function cn(...classes: any[]) {
    return classes.filter(Boolean).join(' ')
}
