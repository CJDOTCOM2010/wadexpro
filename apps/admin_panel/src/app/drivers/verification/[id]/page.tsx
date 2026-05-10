'use client'

import { useEffect, useState, use } from 'react'
import { DashboardLayout } from '@/components/DashboardLayout'
import { adminApi } from '@/lib/axios'
import { CheckCircle, XCircle, ChevronLeft, Calendar, CreditCard, ShieldCheck, AlertCircle } from 'lucide-react'
import Link from 'next/link'
import { useRouter } from 'next/navigation'

export default function DriverVerificationDetailPage({ params }: { params: Promise<{ id: string }> }) {
    const { id } = use(params)
    const router = useRouter()
    const [driver, setDriver] = useState<any>(null)
    const [loading, setLoading] = useState(true)
    const [actionLoading, setActionLoading] = useState(false)
    const [selectedImage, setSelectedImage] = useState<string | null>(null)

    useEffect(() => {
        adminApi.getDriver(id)
            .then(res => setDriver(res.data))
            .catch(err => {
                console.error(err)
                router.push('/drivers')
            })
            .finally(() => setLoading(false))
    }, [id])

    const handleApprove = async () => {
        if (!confirm('Are you sure you want to approve this driver? They will be able to go online immediately.')) return
        
        setActionLoading(true)
        try {
            await adminApi.approveDriver(id)
            router.push('/drivers?status=active')
        } catch (err) {
            alert('Failed to approve driver')
        }
        setActionLoading(false)
    }

    const handleReject = async () => {
        const reason = window.prompt('Enter rejection reason (the driver will see this):')
        if (!reason) return

        setActionLoading(true)
        try {
            await adminApi.rejectDriver(id, reason)
            router.push('/drivers?status=pending_verification')
        } catch (err) {
            alert('Failed to reject application')
        }
        setActionLoading(false)
    }

    if (loading) return (
        <DashboardLayout>
            <div className="flex items-center justify-center h-64">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
        </DashboardLayout>
    )

    return (
        <DashboardLayout>
            <div className="max-w-6xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link href="/drivers" className="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-full transition-colors">
                            <ChevronLeft className="h-5 w-5 text-zinc-500" />
                        </Link>
                        <div>
                            <h1 className="text-2xl font-bold text-zinc-900 dark:text-white">Review Application</h1>
                            <p className="text-sm text-zinc-500">Vetting KYC documents for {driver.user_name}</p>
                        </div>
                    </div>
                    <div className="flex items-center gap-3">
                        <button
                            onClick={handleReject}
                            disabled={actionLoading}
                            className="flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 disabled:opacity-50 transition-colors"
                        >
                            <XCircle className="h-4 w-4" />
                            Reject
                        </button>
                        <button
                            onClick={handleApprove}
                            disabled={actionLoading}
                            className="flex items-center gap-2 px-6 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 disabled:opacity-50 shadow-lg shadow-emerald-600/20 transition-all transform hover:scale-[1.02]"
                        >
                            <CheckCircle className="h-4 w-4" />
                            Approve Driver
                        </button>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Documents Panel */}
                    <div className="lg:col-span-2 space-y-6">
                        <div className="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                            <h2 className="text-lg font-semibold mb-4 flex items-center gap-2">
                                <CreditCard className="h-5 w-5 text-blue-500" />
                                Identity Documents
                            </h2>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <DocumentCard 
                                    label="Ghana Card (Front)" 
                                    url={driver.documents.id_card_front} 
                                    onView={() => setSelectedImage(driver.documents.id_card_front)}
                                />
                                <DocumentCard 
                                    label="Ghana Card (Back)" 
                                    url={driver.documents.id_card_back} 
                                    onView={() => setSelectedImage(driver.documents.id_card_back)}
                                />
                            </div>
                        </div>

                        <div className="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                            <h2 className="text-lg font-semibold mb-4 flex items-center gap-2">
                                <ShieldCheck className="h-5 w-5 text-blue-500" />
                                Profile Verification
                            </h2>
                            <div className="flex flex-col md:flex-row gap-6">
                                <div className="w-full md:w-1/3 aspect-square bg-zinc-100 dark:bg-zinc-800 rounded-xl overflow-hidden cursor-zoom-in" onClick={() => setSelectedImage(driver.documents.profile_photo)}>
                                    {driver.documents.profile_photo ? (
                                        <img src={driver.documents.profile_photo} alt="Profile" className="w-full h-full object-cover" />
                                    ) : (
                                        <div className="w-full h-full flex items-center justify-center text-zinc-400">No Photo</div>
                                    )}
                                </div>
                                <div className="flex-1 space-y-4">
                                    <div className="p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30 rounded-xl flex items-start gap-3">
                                        <AlertCircle className="h-5 w-5 text-amber-600 shrink-0 mt-0.5" />
                                        <p className="text-sm text-amber-800 dark:text-amber-400">
                                            <strong>Review Task:</strong> Compare the profile photo (left) with the face on the identity card to ensure they match. Ensure the ID card information matches the license data below.
                                        </p>
                                    </div>
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <p className="text-xs text-zinc-500 dark:text-zinc-500 font-bold uppercase tracking-wider">License Number</p>
                                            <p className="text-sm dark:text-white font-medium">{driver.license.number || 'N/A'}</p>
                                        </div>
                                        <div>
                                            <p className="text-xs text-zinc-500 dark:text-zinc-500 font-bold uppercase tracking-wider">License Class</p>
                                            <p className="text-sm dark:text-white font-medium">{driver.license.class || 'N/A'}</p>
                                        </div>
                                        <div className="col-span-2">
                                            <p className="text-xs text-zinc-500 dark:text-zinc-500 font-bold uppercase tracking-wider">Expiry Date</p>
                                            <p className="text-sm dark:text-white font-medium flex items-center gap-1.5">
                                                <Calendar className="h-3.5 w-3.5 text-zinc-400" />
                                                {driver.license.expires_at || 'N/A'}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Sidebar Stats */}
                    <div className="space-y-6">
                        <div className="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                            <h3 className="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4">Contact Info</h3>
                            <div className="space-y-4">
                                <div>
                                    <p className="text-xs text-zinc-400 mb-0.5">Email Address</p>
                                    <p className="text-sm dark:text-white truncate">{driver.user_email}</p>
                                </div>
                                <div>
                                    <p className="text-xs text-zinc-400 mb-0.5">Phone Number</p>
                                    <p className="text-sm dark:text-white">{driver.user_phone}</p>
                                </div>
                                <div>
                                    <p className="text-xs text-zinc-400 mb-0.5">Application Date</p>
                                    <p className="text-sm dark:text-white">{new Date(driver.created_at).toLocaleDateString(undefined, { dateStyle: 'long' })}</p>
                                </div>
                            </div>
                        </div>

                        {driver.rejection_reason && (
                            <div className="bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 rounded-2xl p-6">
                                <h3 className="text-sm font-bold text-red-700 dark:text-red-400 uppercase tracking-widest mb-2">Previous Rejection</h3>
                                <p className="text-sm text-red-600 dark:text-red-300 italic">"{driver.rejection_reason}"</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Image Preview Modal */}
            {selectedImage && (
                <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-10" onClick={() => setSelectedImage(null)}>
                    <img src={selectedImage} className="max-w-full max-h-full object-contain" alt="Preview" />
                    <button className="absolute top-8 right-8 text-white hover:text-zinc-300 transition-colors">
                        <XCircle className="h-8 w-8" />
                    </button>
                </div>
            )}
        </DashboardLayout>
    )
}

function DocumentCard({ label, url, onView }: { label: string, url: string | null, onView: () => void }) {
    return (
        <div className="space-y-2">
            <p className="text-xs font-semibold text-zinc-500">{label}</p>
            <div 
                onClick={onView}
                className="aspect-[3/2] bg-zinc-100 dark:bg-zinc-800 rounded-xl overflow-hidden border border-zinc-100 dark:border-zinc-700 cursor-zoom-in group relative"
            >
                {url ? (
                    <>
                        <img src={url} alt={label} className="w-full h-full object-cover transition-transform group-hover:scale-105" />
                        <div className="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                             <div className="bg-white/90 text-zinc-900 px-3 py-1.5 rounded-full text-xs font-bold shadow-xl">Click to Zoom</div>
                        </div>
                    </>
                ) : (
                    <div className="w-full h-full flex items-center justify-center text-zinc-400 text-xs italic">Missing Attachment</div>
                )}
            </div>
        </div>
    )
}
