import type { RouteRecordRaw } from 'vue-router'

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

          // Only fetch if not already cached
          if (!userStore.usersByPage[page]) {
            ui_progressStore.advanceProgress(50)
            await userStore.fetchUsers(page)
            ui_progressStore.advanceProgress(80)
          }

          // returning nothing === allow navigation
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
          //Lazy load users store
          const { useUserStore } = await import('@/stores/user')
          const { useUiProgressStore } = await import('@/stores/ui_progress')

          const userStore = useUserStore()
          const ui_progressStore = useUiProgressStore()

          const page = Number(to.query.page ?? 1)
          const perPage = Number(to.query.per_page ?? 10)
          const sortParam = to.query.sort as string | undefined

          //check cache using SAME KEY SHAPE as store
          const cacheKey = `${page}-${perPage}-${sortParam ?? 'none'}`

          // Only fetch if not already cached
          if (!userStore.usersByPage[cacheKey]) {
            try{
              ui_progressStore.advanceProgress(50)
              await userStore.fetchUsers(page,perPage,{sort:sortParam})
              ui_progressStore.advanceProgress(80)
            }catch(e){
              console.error('Failed to preload users in route guard:',e)
            }
          }

          //allow navigation
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
