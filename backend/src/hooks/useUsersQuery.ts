import { useQuery } from '@tanstack/vue-query'
import { computed } from 'vue'
import { usersApi } from '@/api/users'
import type { FetchUserOptions, ApiResponse, PaginatedResponse, User } from '@/types'

export function useUsersQuery(
    page = 1,
    perPage = 10,
    options: FetchUserOptions = {},
    enabled = true,
) {
    const { sort, search } = options

    const queryKey = computed(() => ['users', 'list', { page, perPage, sort, search }])

    return useQuery<PaginatedResponse<User>>({
        queryKey: queryKey.value,
        queryFn: () => usersApi.fetchUsers(page, perPage, options),
        enabled,
        staleTime: 2 * 60 * 1000,
        gcTime: 10 * 60 * 1000,
    })
}

export function useUserQuery(id: number, enabled = true) {
    return useQuery({
        queryKey: ['users', 'detail', id],
        queryFn: () => usersApi.fetchUserById(id),
        enabled,
        staleTime: 2 * 60 * 1000,
        gcTime: 10 * 60 * 1000,
        select: (data: ApiResponse<User>) => data.data,
    })
}
