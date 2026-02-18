import { defineStore } from 'pinia'
import type { User, LoginResponse } from '@/types'
import apiClient from '@/api/axios'
import type { AuthErrorResponse } from '@/types'
import type { Router } from 'vue-router'

//custom error class for login
class AuthError extends Error implements AuthErrorResponse {
    status: number

    constructor(message: string, status: number) {
        super(message)
        this.status = status
        Object.setPrototypeOf(this, AuthError.prototype)
    }
}

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: (localStorage.getItem('token') || '') as string,
        currentUser: null as User | null,
        isLoading: false,
    }),

    actions: {
        setToken(newToken: string) {
            this.token = newToken
            localStorage.setItem('token', newToken)
        },

        setCurrentUser(user: User | null) {
            this.currentUser = user
        },

        async login(email: string, password: string) {
            this.isLoading = true

            try {
                const response = await apiClient.post<LoginResponse>('/login', {
                    email,
                    password,
                })

                const token = response.data.data.token
                const user = response.data.data.user

                this.setToken(token)
                this.setCurrentUser(user)

                return true // Let component handle the redirect
            } catch (error: any) {
                const response = error?.response

                const message = response?.data?.message ?? 'Invalid email or password'
                const status = response?.status ?? 500

                throw new AuthError(message, status)
            } finally {
                this.isLoading = false
            }
        },
        clear() {
            ;((this.token = ''), (this.currentUser = null), localStorage.removeItem('token'))
        },
        logout(router: Router) {
            this.clear()
            router.push({ name: 'login' })
        },
    },
})
