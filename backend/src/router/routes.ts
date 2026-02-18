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
            // routes.ts
            {
                path: 'users',
                name: 'users',
                component: () => import('@/views/user/UsersView.vue'), // Updated import
                beforeEnter: async (to) => {
                    const { useUiProgressStore } = await import('@/stores/ui_progress')
                    const { queryClient } = await import('@/lib/queryClient')
                    const { usersApi } = await import('@/api/users')

                    const ui_progressStore = useUiProgressStore()

                    // Start progress
                    ui_progressStore.startRouteLoading(10)

                    const page = Number(to.query.page ?? 1)
                    const perPage = Number(to.query.per_page ?? 10)
                    const sortParam = to.query.sort as string | undefined
                    const searchParam = to.query.search as string | undefined

                    try {
                        await queryClient.prefetchQuery({
                            queryKey: [
                                'users',
                                'list',
                                { page, perPage, sort: sortParam, search: searchParam },
                            ],
                            queryFn: async () => {
                             
                                const result = await usersApi.fetchUsers(page, perPage, {
                                    sort: sortParam,
                                    search: searchParam,
                                })
                                ui_progressStore.advanceProgress(80)
                                return result
                        },
                        })

                        ui_progressStore.finishRouteLoading()
                    } catch (e) {
                        // console.error('Failed to preload users in route guard:', e)
                        ui_progressStore.reset()
                    }

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
                            queryKey: [
                                'users',
                                'list',
                                {
                                    page,
                                    perPage,
                                    sort: sortParam,
                                    search: searchParam,
                                },
                            ],
                            queryFn: () =>
                                usersApi.fetchUsers(page, perPage, {
                                    sort: sortParam,
                                    search: searchParam,
                                }),
                        })

                        ui_progressStore.advanceProgress(80)
                    } catch (e) {
                        console.error('Failed to preload users in route guard:', e)
                    }

                    return true
                },
            },
            {
                path: ':catchAll(.*)',
                name: 'admin-not-found',
                component: () => import('@/views/AdminNotFoundView.vue'),
            },
        ],
    },
    {
        path: '/test',
        name: 'test',
        component: () => import('@/views/test.vue'),
    },
    {
        path: '/:catchAll(.*)',
        name: 'public-not-found',
        component: () => import('@/views/PublicNotFoundView.vue'),
    },
]
