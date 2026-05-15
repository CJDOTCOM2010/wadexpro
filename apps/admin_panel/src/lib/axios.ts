import axios from 'axios';

const API_BASE = process.env.NEXT_PUBLIC_API_URL || 'http://wadexpro.test/api';

const api = axios.create({
    baseURL: API_BASE,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    withCredentials: true,
});

// Request interceptor — attach auth token
api.interceptors.request.use((config) => {
    if (typeof window !== 'undefined') {
        const token = localStorage.getItem('wadexp_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
    }
    return config;
});

// Response interceptor — handle 401
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401 && typeof window !== 'undefined') {
            localStorage.removeItem('wadexp_token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default api;

// -----------------------------------------------------------------------
// Admin API Functions
// -----------------------------------------------------------------------

export const adminApi = {
    // Dashboard
    getDashboardOverview: () => api.get('/v1/orchestrator/dashboard'),

    // Users
    getUsers: (params?: Record<string, string>) => api.get('/v1/orchestrator/users', { params }),
    getUser: (id: string) => api.get(`/v1/orchestrator/users/${id}`),
    toggleUserStatus: (id: string) => api.patch(`/v1/orchestrator/users/${id}/status`),

    // Drivers
    getDrivers: (params?: Record<string, string>) => api.get('/v1/logistics/admin/drivers', { params }),
    getDriver: (id: string) => api.get(`/v1/logistics/admin/drivers/${id}`),
    approveDriver: (id: string) => api.post(`/v1/logistics/admin/drivers/${id}/approve`),
    rejectDriver: (id: string, reason: string) => api.post(`/v1/logistics/admin/drivers/${id}/reject`, { reason }),
    suspendDriver: (id: string, reason: string) => api.post(`/v1/logistics/admin/drivers/${id}/suspend`, { reason }),

    // Rides
    getRides: (params?: Record<string, string>) => api.get('/v1/logistics/admin/rides', { params }),
    getRide: (id: string) => api.get(`/v1/logistics/admin/rides/${id}`),

    // Payments
    getTransactions: (params?: Record<string, string>) => api.get('/v1/orchestrator/payments', { params }),

    // Modules
    getModules: () => api.get('/v1/orchestrator/modules'),
    toggleModule: (slug: string) => api.patch(`/v1/orchestrator/modules/${slug}/toggle`),

    // System Settings
    getSettings: () => api.get('/v1/orchestrator/settings'),
    updateSettings: (group: string, settings: Record<string, unknown>) =>
        api.patch('/v1/orchestrator/settings', { group, settings }),

    // Activity Logs
    getLogs: (params?: Record<string, string>) => api.get('/v1/orchestrator/logs', { params }),

    // CMS
    getCmsPages: () => api.get('/v1/cms/admin/pages'),
    getCmsPage: (id: string) => api.get(`/v1/cms/admin/pages/${id}`),
    createCmsPage: (data: Record<string, unknown>) => api.post('/v1/cms/admin/pages', data),
    updateCmsPage: (id: string, data: Record<string, unknown>) => api.put(`/v1/cms/admin/pages/${id}`, data),
    deleteCmsPage: (id: string) => api.delete(`/v1/cms/admin/pages/${id}`),
    reorderSections: (pageId: string, sectionIds: string[]) =>
        api.post(`/v1/cms/admin/pages/${pageId}/reorder-sections`, { section_ids: sectionIds }),

    // Sections
    createSection: (pageId: string, data: Record<string, unknown>) =>
        api.post(`/v1/cms/admin/pages/${pageId}/sections`, data),
    updateSection: (sectionId: string, data: Record<string, unknown>) =>
        api.put(`/v1/cms/admin/sections/${sectionId}`, data),
    deleteSection: (sectionId: string) => api.delete(`/v1/cms/admin/sections/${sectionId}`),

    // Blocks
    createBlock: (sectionId: string, data: Record<string, unknown>) =>
        api.post(`/v1/cms/admin/sections/${sectionId}/blocks`, data),
    updateBlock: (blockId: string, data: Record<string, unknown>) =>
        api.put(`/v1/cms/admin/blocks/${blockId}`, data),
    deleteBlock: (blockId: string) => api.delete(`/v1/cms/admin/blocks/${blockId}`),

    // Section types
    getSectionTypes: () => api.get('/v1/cms/section-types'),

    // SOS
    getSosEvents: () => api.get('/v1/logistics/admin/sos'),
    updateSosEvent: (id: string, data: { status: string; notes?: string }) => 
        api.patch(`/v1/logistics/admin/sos/${id}`, data),
    triggerSos: (data: { lat: number; lng: number; ride_request_id?: string }) => 
        api.post('/v1/logistics/admin/sos', data),

    // Real-time Telemetry
    getLiveMapDrivers: () => api.get('/v1/logistics/admin/live-map/drivers'),

    // Analytics & Intelligence
    getDemandHeatmap: () => api.get('/v1/logistics/admin/analytics/demand-heatmap'),

    // Promotions & Campaigns
    getPromotions: () => api.get('/v1/logistics/admin/promotions'),
    createPromotion: (data: Record<string, unknown>) => api.post('/v1/logistics/admin/promotions', data),
    updatePromotion: (id: string, data: Record<string, unknown>) => api.put(`/v1/logistics/admin/promotions/${id}`, data),
    deletePromotion: (id: string) => api.delete(`/v1/logistics/admin/promotions/${id}`),
    togglePromotion: (id: string) => api.patch(`/v1/logistics/admin/promotions/${id}/toggle`),

    // Surge & Dynamic Pricing
    getSurgeZones: () => api.get('/v1/logistics/admin/surge'),
    getSurgeZone: (id: string) => api.get(`/v1/logistics/admin/surge/${id}`),
    createSurgeZone: (data: Record<string, unknown>) => api.post('/v1/logistics/admin/surge', data),
    updateSurgeZone: (id: string, data: Record<string, unknown>) => api.put(`/v1/logistics/admin/surge/${id}`, data),
    deleteSurgeZone: (id: string) => api.delete(`/v1/logistics/admin/surge/${id}`),
    syncSurgeRules: (id: string, rules: any[]) => api.post(`/v1/logistics/admin/surge/${id}/rules`, { rules }),

    // Analytics & Reports
    getAnalyticsRevenue: (days?: number) => api.get('/v1/logistics/admin/analytics/revenue', { params: { days } }),
    getAnalyticsRides: () => api.get('/v1/logistics/admin/analytics/rides'),
    getAnalyticsDrivers: () => api.get('/v1/logistics/admin/analytics/drivers'),

    // HR & Employee Management
    getEmployees: () => api.get('/v1/hr/employees'),
    onboardEmployee: (data: any) => api.post('/v1/hr/employees', data),
    getAttendanceLogs: () => api.get('/v1/hr/attendance'),
    clockIn: (data: any) => api.post('/v1/hr/clock-in', data),
    clockOut: () => api.post('/v1/hr/clock-out'),
    
    // Accounting & Financial Ledger
    getGeneralLedger: (params?: Record<string, string>) => api.get('/v1/logistics/admin/accounting/ledger', { params }),
    getRevenueSummary: () => api.get('/v1/logistics/admin/accounting/summary'),
    getEarningsBreakdown: () => api.get('/v1/logistics/admin/accounting/breakdown'),

    // Advanced Logistics & Deliveries
    getDeliveries: (params?: Record<string, string>) => api.get('/v1/logistics/admin/deliveries', { params }),
    getDeliveryDetails: (id: string) => api.get(`/v1/logistics/admin/deliveries/${id}`),
    
    // Fleet Management
    getFleetOverview: () => api.get('/v1/logistics/admin/fleet/overview'),
    getVehicles: () => api.get('/v1/logistics/admin/fleet/vehicles'),
    updateVehicleStatus: (id: string, status: string) => api.patch(`/v1/logistics/admin/fleet/vehicles/${id}/status`, { status }),

    // Global Expansion & Regions
    getRegions: () => api.get('/v1/logistics/admin/regions'),
    getRegion: (id: string) => api.get(`/v1/logistics/admin/regions/${id}`),
    createRegion: (data: any) => api.post('/v1/logistics/admin/regions', data),
    updateRegion: (id: string, data: any) => api.put(`/v1/logistics/admin/regions/${id}`, data),
    deleteRegion: (id: string) => api.delete(`/v1/logistics/admin/regions/${id}`),
    getRegionRates: (id: string) => api.get(`/v1/logistics/admin/regions/${id}/rates`),
    updateRegionRates: (id: string, rates: any[]) => api.post(`/v1/logistics/admin/regions/${id}/rates`, { rates }),

    // Advanced Security & Safety
    getSafetyAlerts: (status?: string) => api.get('/v1/logistics/admin/safety/alerts', { params: { status } }),
    getSafetyAlert: (id: string) => api.get(`/v1/logistics/admin/safety/alerts/${id}`),
    resolveSafetyAlert: (id: string, data: { status: string, notes: string }) => api.post(`/v1/logistics/admin/safety/alerts/${id}/resolve`, data),
    getSafetyStats: () => api.get('/v1/logistics/admin/safety/stats'),

    // Enterprise & Multi-Tenancy
    getOrganizations: (search?: string) => api.get('/v1/logistics/admin/organizations', { params: { search } }),
    getOrganization: (id: string) => api.get(`/v1/logistics/admin/organizations/${id}`),
    createOrganization: (data: any) => api.post('/v1/logistics/admin/organizations', data),
    updateOrgBilling: (id: string, data: any) => api.put(`/v1/logistics/admin/organizations/${id}/billing`, data),
    addOrgMembers: (id: string, data: { user_ids: string[], role: string }) => api.post(`/v1/logistics/admin/organizations/${id}/members`, data),

    // Analytics, BI & Fleet Intelligence
    getAnalyticsOverview: () => api.get('/v1/logistics/admin/analytics/overview'),
    getAnalyticsTrends: () => api.get('/v1/logistics/admin/analytics/trends'),
    getSupplyDemandRatio: () => api.get('/v1/logistics/admin/analytics/ratio'),
    getLeaderboards: () => api.get('/v1/logistics/admin/analytics/leaderboards'),
    getDemandHeatmap: () => api.get('/v1/logistics/admin/analytics/heatmap'),
};
