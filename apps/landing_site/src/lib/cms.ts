import axios from 'axios';

const API_BASE = process.env.NEXT_PUBLIC_API_URL || 'http://wadexp_logistics.test/api';

const api = axios.create({
    baseURL: API_BASE,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

export const cmsApi = {
    /**
     * Get a page by slug and locale.
     */
    getPage: (slug: string, locale: string = 'en') => 
        api.get(`/v1/cms/pages/${slug}`, { params: { locale } }),

    /**
     * Get navigation menu.
     */
    getNavigation: (locale: string = 'en') => 
        api.get('/v1/cms/navigation', { params: { locale } }),

    /**
     * Get section types definitions.
     */
    getSectionTypes: () => 
        api.get('/v1/cms/section-types'),
};

export default api;
