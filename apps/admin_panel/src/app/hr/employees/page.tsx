'use client'

import { useState, useEffect } from 'react'
import DashboardLayout from '@/components/DashboardLayout'
import { 
    Table, 
    TableBody, 
    TableCell, 
    TableHead, 
    TableHeader, 
    TableRow 
} from "@/components/ui/table"
import { adminApi } from '@/lib/axios'
import { UserPlus, Search, Mail, Briefcase } from 'lucide-react'

export default function EmployeePage() {
    const [employees, setEmployees] = useState<any[]>([])
    const [loading, setLoading] = useState(true)
    const [showModal, setShowModal] = useState(false)
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        department: 'Operations',
        position: 'Dispatcher',
        base_salary: '2500'
    })

    const fetchEmployees = async () => {
        setLoading(true)
        try {
            const res = await adminApi.getEmployees()
            setEmployees(res.data.data)
        } catch (err) {
            console.error(err)
        }
        setLoading(false)
    }

    const handleOnboard = async (e: React.FormEvent) => {
        e.preventDefault()
        try {
            await adminApi.onboardEmployee(formData)
            setShowModal(false)
            fetchEmployees()
        } catch (err) {
            alert('Onboarding failed. Check console.')
        }
    }

    useEffect(() => { fetchEmployees() }, [])

    return (
        <DashboardLayout>
            <div className="max-w-7xl mx-auto space-y-8 pb-12">
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Personnel Directory</h1>
                        <p className="mt-2 text-zinc-600 dark:text-zinc-400">Manage your workforce, from dispatchers to support staff.</p>
                    </div>
                    <button className="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-600/20">
                        Add Employee
                    </button>
                </div>

                <div className="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                    <Table>
                        <TableHeader>
                            <TableRow className="bg-zinc-50/50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                                <TableHead className="w-[100px]">ID</TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Department</TableHead>
                                <TableHead>Position</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {employees?.map((employee: any) => (
                                <TableRow key={employee.id} className="border-b border-zinc-100 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                                    <TableCell className="font-mono text-xs text-zinc-500">{employee.employee_code}</TableCell>
                                    <TableCell className="font-semibold text-zinc-900 dark:text-white">{employee.user?.name}</TableCell>
                                    <TableCell>{employee.department}</TableCell>
                                    <TableCell>{employee.position}</TableCell>
                                    <TableCell>
                                        <Badge variant={employee.is_active ? "success" : "secondary" as any} className={cn(
                                            "rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider",
                                            employee.is_active ? "bg-emerald-100 text-emerald-700 border-emerald-200" : "bg-zinc-100 text-zinc-500"
                                        )}>
                                            {employee.is_active ? 'Active' : 'Inactive'}
                                        </Badge>
                                    </TableCell>
                                    <TableCell className="text-right">
                                        <button className="text-blue-600 hover:text-blue-700 font-medium text-sm">View Profile</button>
                                    </TableCell>
                                </TableRow>
                            ))}
                            {(!employees || employees.length === 0) && (
                                <TableRow>
                                    <TableCell colSpan={6} className="h-64 text-center text-zinc-500 italic">
                                        No personnel found in the directory.
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
