import { defineStore } from 'pinia'

export const useUiProgressStore = defineStore('ui_progress', {
  state: () => ({
    routeLoading: false,
    progress: 0,
  }),

  actions: {
    startRouteLoading() {
      ;((this.routeLoading = true), (this.progress = 30))
    },
    advanceProgress(value: number = 60) {
      this.progress = Math.min(value, 95)
    },
    finishRouteLoading() {
      this.progress = 100

      setTimeout(() => {
        this.routeLoading = false
        this.progress = 0
      }, 200)
    },
  },
})
