import type { RouteRecordRaw } from 'vue-router'
import { buildUsersCacheKey } from '@/utils/cacheKey'

export const routes: RouteRecordRaw[] = [
    {
        path: '/',
        name: 'login',
        component: () => import('@/views/auth/LoginView.vue'),
    },
    {
        path: '/app',
        name: 'app-layout',
        component: () => import('@/components/applayouts/AppLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: () => import('@/views/DashboardView.vue'),
            },
            {
                path: 'users',
                name: 'users',
                component: () => import('@/views/user/UsersView.vue'),
                beforeEnter: async (to) => {
                    //Lazy load users store
                    const { useUserStore } = await import('@/stores/user')
                    const { useUiProgressStore } = await import('@/stores/ui_progress')

                    const userStore = useUserStore()
                    const ui_progressStore = useUiProgressStore()

                    const page = Number(to.query.page ?? 1)
                    const perPage = Number(to.query.per_page ?? 10)
                    const sortParam = to.query.sort as string | undefined
                    const searchParam = to.query.search as string | undefined

                    const cacheKey = buildUsersCacheKey(page, perPage, sortParam, searchParam)

                    // Set a flag in sessionStorage to indicate route guard is handling initial fetch
                    // sessionStorage.setItem('routeGuardFetching', 'true')

                    // Only fetch if not already cached
                    if (!userStore.usersByPage[cacheKey]) {
                        try {
                            ui_progressStore.advanceProgress(50)
                            await userStore.fetchUsers(page, perPage, {
                                sort: sortParam,
                                search: searchParam,
                            })
                            ui_progressStore.advanceProgress(80)
                        } catch (error) {
                            console.error('Failed to preload users in route guard:', error)
                        }
                    }

                    // Clear the flag
                    // sessionStorage.removeItem('routeGuardFetching')
                    return true
                },
            },
            {
                path: 'users/:id',
                name: 'user-detail',
                component: () => import('@/views/user/UserDetail.vue'),
            },
            {
                path: 'settings',
                name: 'settings',
                component: () => import('@/views/setting/SettingView.vue'),
            },
            {
    path: 'sandbox',
    name: 'sandbox',
    component: () => import('@/views/Sandbox.vue'),
    beforeEnter: async (to) => {
        const { useUiProgressStore } = await import('@/stores/ui_progress')
        const { queryClient } = await import('@/lib/queryClient')
        const { usersApi } = await import('@/api/users')

        const ui_progressStore = useUiProgressStore()

        const page = Number(to.query.page ?? 1)
        const perPage = Number(to.query.per_page ?? 10)
        const sortParam = to.query.sort as string | undefined
        const searchParam = to.query.search as string | undefined

        // Prefetch using Vue Query
        try {
            ui_progressStore.advanceProgress(50)
            
            await queryClient.prefetchQuery({
                queryKey: ['users', 'list', { 
                    page, 
                    perPage, 
                    sort: sortParam, 
                    search: searchParam 
                }],
                queryFn: () => usersApi.fetchUsers(page, perPage, { 
                    sort: sortParam, 
                    search: searchParam 
                }),
            })
            
            ui_progressStore.advanceProgress(80)
        } catch (e) {
            console.error('Failed to preload users in route guard:', e)
        }

        return true
    },
},
        ],
    },
    {
        path: '/:catchAll(.*)',
        name: 'not-found',
        component: () => import('@/views/NotFoundView.vue'),
    },
]
