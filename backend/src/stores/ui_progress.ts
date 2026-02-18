import { initial } from 'lodash'
import { defineStore } from 'pinia'
import {ref,watch} from 'vue'

export const useUiProgressStore = defineStore('ui_progress',() => {
    const routeLoading = ref(false)
    const progress = ref(0)
    let progressInterval: ReturnType<typeof setInterval> | null = null

    // Clean up function
    function clearProgressInterval() {
        if (progressInterval) {
            clearInterval(progressInterval)
            progressInterval = null
        }
    }

   function startRouteLoading(initialProgress: number = 10) {
        routeLoading.value = true
        progress.value = initialProgress
        
        // Simulate incremental progress for better UX
        progressInterval = setInterval(() => {
            if (progress.value < 90) {
                const increment = progress.value < 50 ? 5 : 2
                progress.value = Math.min(progress.value + increment, 90)
            }
        }, 200)
    }

    function advanceProgress(value: number) {
        progress.value = Math.min(value, 95)
    }

    function finishRouteLoading() {
        clearProgressInterval()
        progress.value = 100
        
        setTimeout(() => {
            routeLoading.value = false
            progress.value = 0
        }, 200)
    }

    // Reset on error
    function reset() {
        clearProgressInterval()
        routeLoading.value = false
        progress.value = 0
    }

     return {
        routeLoading,
        progress,
        startRouteLoading,
        advanceProgress,
        finishRouteLoading,
        reset
    }

})
