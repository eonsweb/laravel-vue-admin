<script setup lang="ts">
import { useUiProgressStore } from '@/stores/ui_progress'

const ui_progressStore = useUiProgressStore()
</script>

<template>
    <main class="flex-1 overflow-y-auto py-10 px-8 bg-slate-200 dark:bg-gray-900">
        <router-view v-slot="{ Component, route }">
            <div :key="route.fullPath">
                <Suspense :timeout="0" @resolve="ui_progressStore.finishRouteLoading">
                    <component :is="Component" />

                    <template #fallback>
                        <!-- skeleton -->
                        <div class="space-y-4">
                            <div class="h-6 w-1/3 bg-muted rounded" />
                            <div class="h-40 bg-muted rounded-xl" />
                        </div>
                    </template>
                </Suspense>
            </div>
        </router-view>
    </main>
</template>

<style scoped></style>
