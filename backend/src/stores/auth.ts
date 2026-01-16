import { defineStore } from 'pinia'
import type { User, LoginResponse } from '@/types'
import apiClient from '@/api/axios'

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
                console.error('Login failed:', error.response?.data || error.message)
                throw error
            } finally {
                this.isLoading = false
            }
        },
    },
})
