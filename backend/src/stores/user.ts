import { defineStore } from 'pinia'
import type { User, ApiResponse,FetchUserOptions } from '@/types'
import apiClient from '@/lib/axios'
import { get } from '@vueuse/core'

export const useUserStore = defineStore('user', {
  state: () => ({
    users: [] as User[],
    user: null as User | null,
    isLoading: false,
    isLoaded: false, // Indicates if a single user has been loaded
    usersByPage: {} as Record<string, User[]>, // Cache users by page number
    activePageSize: null as number | null,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0,
    },
  }),
  getters: {
    getUsersByPage: (state) => {
  return (page: number, perPage: number, sort?: string, direction?: string) => {
    const cacheKey = `${page}-${perPage}-${sort ?? 'none'}-${direction ?? 'none'}`
    return state.usersByPage[cacheKey] || null
  }
}
  },

  actions: {
    async fetchUsers(
      page: number = 1,
      perPage = 10,
      options: FetchUserOptions = {},
    ) {
      //RESET CACHE IF PERPAGE CHANGES
      if (this.activePageSize !== perPage) {
        this.usersByPage = {} // Clear cache if page size changed
        this.activePageSize = perPage // Update active page size
      }
      const { sort, direction } = options
      const cacheKey = `${page}-${perPage}-${sort ?? 'none'}-${direction ?? 'none'}`

      //SERVE CACHED
      if (this.usersByPage[cacheKey]) {
        return this.usersByPage[cacheKey]
      }

      this.isLoading = true

      try {
        const { sort, direction } = options
        const params: Record<string, any> = { page, per_page: perPage }

        if (sort) params.sort = sort
        if (direction) params.direction = direction

        const response = await apiClient.get(`/users`, { params })

        const { data, meta } = response.data

        //UPDATE PAGINATION AFTER FETCH
        this.pagination = meta

        //CACHE THE PAGE
        this.usersByPage[cacheKey] = data

        return data
      } catch (error: any) {
        console.error('Failed to fetch users:', error.response?.data || error.message)
        throw error
      } finally {
        this.isLoading = false
      }
    },

    getAllCachedUsers() {
      //FLATTEN ALL PAGES IF NEEDED
      return Object.values(this.usersByPage).flat()
    },

    async fetchUserById(id: number): Promise<User | null> {
      this.isLoading = true
      try {
        const response = await apiClient.get<ApiResponse<User>>(`/users/${id}`)
        this.user = response.data.data
        this.isLoaded = true
        return this.user
      } catch (error: any) {
        console.error('Failed to fetch user by ID:', error.response?.data || error.message)
        return null
      } finally {
        this.isLoading = false
      }
    },
  },
})
