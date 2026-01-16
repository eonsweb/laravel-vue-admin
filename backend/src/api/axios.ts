// axios.ts
import axios from 'axios'
import type { AxiosError, AxiosInstance, InternalAxiosRequestConfig, AxiosResponse } from 'axios'

import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const apiClient: AxiosInstance = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
    },
})

// REQUEST INTERCEPTOR
apiClient.interceptors.request.use(
    (config: InternalAxiosRequestConfig) => {
        const auth = useAuthStore()

        if (auth.token && config.headers) {
            config.headers.Authorization = `Bearer ${auth.token}`
        }
        return config
    },
    (error: AxiosError) => Promise.reject(error),
)

// RESPONSE INTERCEPTOR
apiClient.interceptors.response.use(
    (response: AxiosResponse) => response,
    (error: AxiosError) => {
        const router = useRouter()
        const auth = useAuthStore()

        if (error.response?.status === 401) {
            auth.setToken('')
            router.push('/login')
        }

        return Promise.reject(error)
    },
)

export default apiClient
