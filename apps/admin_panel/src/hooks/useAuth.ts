import useSWR from 'swr';
import axios from '@/lib/axios';
import { useCallback } from 'react';
import { useRouter } from 'next/navigation';

interface UseAuthParams {
    middleware?: string;
    redirectIfAuthenticated?: string;
}

export const useAuth = ({ middleware, redirectIfAuthenticated }: UseAuthParams = {}) => {
    const router = useRouter();

    const { data: user, error, mutate } = useSWR('/auth/me', () =>
        axios
            .get('/auth/me')
            .then(res => res.data.data)
            .catch(error => {
                if (error.response.status !== 409) throw error;
            }), {
                revalidateOnFocus: false
            }
    );

    const csrf = () => axios.get(process.env.NEXT_PUBLIC_SANCTUM_URL || 'http://localhost:8000/sanctum/csrf-cookie');

    const login = useCallback(async ({ setErrors, setStatus, ...props }: { 
        setErrors: (errors: any) => void;
        setStatus: (status: string | null) => void;
        [key: string]: any;
    }) => {
        setErrors([]);
        setStatus(null);

        await csrf();

        axios
            .post('/auth/login', props)
            .then(response => {
                // If using tokens securely
                localStorage.setItem('wadexp_token', response.data.data.token.access_token);
                mutate();
            })
            .catch(error => {
                if (error.response?.status === 422) {
                    setErrors(error.response.data.errors);
                } else {
                    setErrors({ general: 'Failed to authenticate securely. Double check credentials.' });
                }
            });
    }, [mutate]);

    const logout = useCallback(async () => {
        if (!error) {
            await axios.post('/auth/logout');
            localStorage.removeItem('wadexp_token');
            mutate();
        }

        window.location.pathname = '/login';
    }, [error, mutate]);

    return {
        user,
        login,
        logout,
        isLoading: user === undefined && error === undefined,
    };
};
