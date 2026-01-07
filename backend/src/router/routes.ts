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

          // Only fetch if not already cached
          if (!userStore.usersByPage[page]) {
            ui_progressStore.advanceProgress(50)
            await userStore.fetchUsers(page)
            ui_progressStore.advanceProgress(80)
          }

          // returning nothing === allow navigation
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
