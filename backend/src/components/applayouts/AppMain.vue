<script setup lang="ts">
import { useUiProgressStore } from '@/stores/ui_progress'
import {watch} from 'vue'
import { useRoute } from 'vue-router'

const ui_progressStore = useUiProgressStore()

//reset progress on route change
const route = useRoute()
watch(
    () => route.fullPath,
    () => {
        ui_progressStore.reset
    }
)
</script>

<template>
    <main class="flex-1 overflow-y-auto py-10 px-8 bg-slate-200 dark:bg-gray-900">
              <router-view v-slot="{ Component, route }">
            <div :key="route.fullPath">
                <Suspense 
                    :timeout="0" 
                    @pending="ui_progressStore.startRouteLoading(30)"
                    @resolve="ui_progressStore.finishRouteLoading"
                    @fallback="ui_progressStore.advanceProgress(60)"
                >
                    <component :is="Component" />

                    <template #fallback>
                        <!-- Enhanced skeleton with progress awareness -->
                        <div class="space-y-4 animate-pulse">
                            <!-- Header skeleton -->
                            <div class="space-y-2">
                                <div class="h-8 w-64 bg-muted rounded" />
                                <div class="h-4 w-48 bg-muted rounded" />
                            </div>
                            
                            <!-- Content skeleton -->
                            <div class="space-y-3 mt-6">
                                <div class="h-10 bg-muted rounded" />
                                <div class="h-32 bg-muted rounded-xl" />
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="h-24 bg-muted rounded" />
                                    <div class="h-24 bg-muted rounded" />
                                    <div class="h-24 bg-muted rounded" />
                                </div>
                            </div>
                            
                            <!-- Optional: Show progress percentage -->
                            <div v-if="ui_progressStore.progress > 0" 
                                 class="text-sm text-muted-foreground mt-2">
                                Loading... {{ Math.round(ui_progressStore.progress) }}%
                            </div>
                        </div>
                    </template>
                </Suspense>
            </div>
        </router-view>
    </main>
</template>

<style scoped></style>
