import apiClient from './axios'
import type { User, ApiResponse, PaginatedResponse, FetchUserOptions } from '../types'

export const usersApi = {
    async fetchUsers(
        page: number = 1,
        perPage = 10,
        options: FetchUserOptions = {},
    ): Promise<PaginatedResponse<User>> {
        const params: Record<string, any> = { page, per_page: perPage }

        if (options.sort) params.sort = options.sort
        if (options.search) params.search = options.search

        // const response = await apiClient.get<PaginatedResponse<User[]>>(`/users`, { params })
        const response = await apiClient.get<PaginatedResponse<User>>('/users', { params })
        return response.data
    },

    async fetchUserById(id: number) {
        const response = await apiClient.get<ApiResponse<User>>(`/users/${id}`)
        return response.data
    },
}
