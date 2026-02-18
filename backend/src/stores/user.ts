import { defineStore } from 'pinia'
import type { User } from '@/types'

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null as User | null, // Keep for local user management if needed
    }),
    actions: {
        // Only local actions if needed
        setUser(user: User) {
            this.user = user
        },
        clearUser() {
            this.user = null
        },
    },
})
