'use client'

import { useState, useEffect } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { 
    Table, 
    TableBody, 
    TableCell, 
    TableHead, 
    TableHeader, 
    TableRow 
} from "@/components/ui/table"
import { Badge } from "@/components/ui/badge"
import { adminApi } from '@/lib/axios'
import { format } from 'date-fns'
import { Clock, MapPin, Search, Calendar } from 'lucide-react'

export default function AttendancePage() {
    const [records, setRecords] = useState<any[]>([])
    const [loading, setLoading] = useState(true)

    const fetchAttendance = async () => {
        setLoading(true)
        try {
            const res = await adminApi.getAttendanceLogs()
            setRecords(res.data.data)
        } catch (err) {
            console.error(err)
        }
        setLoading(false)
    }

    useEffect(() => { fetchAttendance() }, [])

    return (
        <DashboardLayout>
                <div>
                    <h1 className="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Attendance Monitor</h1>
                    <p className="mt-2 text-zinc-600 dark:text-zinc-400">Real-time visibility into staff clock-ins and geospatial verification.</p>
                </div>

                <div className="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                    <Table>
                        <TableHeader>
                            <TableRow className="bg-zinc-50/50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                                <TableHead>Employee</TableHead>
                                <TableHead>Date</TableHead>
                                <TableHead>Clock In</TableHead>
                                <TableHead>Clock Out</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Verification</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow className="border-b border-zinc-100 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                                <TableCell className="font-semibold text-zinc-900 dark:text-white">Admin User</TableCell>
                                <TableCell>{format(new Date(), 'MMM dd, yyyy')}</TableCell>
                                <TableCell>08:00 AM</TableCell>
                                <TableCell>05:00 PM</TableCell>
                                <TableCell>
                                    <Badge className="bg-emerald-100 text-emerald-700 border-emerald-200 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase">Present</Badge>
                                </TableCell>
                                <TableCell className="text-right">
                                    <span className="text-xs text-zinc-500 font-mono">Verified GPS</span>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </DashboardLayout>
    )
}
