import {
    createRouter,
    createWebHistory,
    type NavigationGuardNext,
    type RouteLocationNormalized,
} from 'vue-router'
import { routes } from './routes'
import { useAuthStore } from '@/stores/auth'

import { useUiProgressStore } from '@/stores/ui_progress'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
})

// GLOBAL AUTH GUARD
router.beforeEach(
    (to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext) => {
        const auth = useAuthStore()
        const isLoggedIn = !!auth.token

        // If route requires auth and user is not logged in
        if (to.meta.requiresAuth && !isLoggedIn) {
            return next({ name: 'login' })
        }

        // If logged in, prevent access to login page
        if (to.name === 'login' && isLoggedIn) {
            return next({ name: 'dashboard' })
        }

        return next()
    },
)

router.beforeEach(() => {
    const ui_progressStore = useUiProgressStore()
    ui_progressStore.startRouteLoading()
})

router.afterEach(() => {
    const ui_progressStore = useUiProgressStore()
    requestAnimationFrame(() => {
        ui_progressStore.finishRouteLoading()
    })
})

export default router
