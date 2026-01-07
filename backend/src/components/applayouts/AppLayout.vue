<script setup lang="ts">
import AppSidebar from '@/components/applayouts/AppSidebar.vue'
import AppTopnav from '@/components/applayouts/AppTopnav.vue'
import AppMain from '@/components/applayouts/AppMain.vue'

import { SidebarInset, SidebarProvider } from '@/components/ui/sidebar'
import { Progress } from '@/components/ui/progress'
import { useUiProgressStore } from '@/stores/ui_progress'

const ui = useUiProgressStore()
</script>

<template>
  <SidebarProvider>
    <!-- Sidebar -->
    <AppSidebar />

    <!-- Main column -->
    <SidebarInset class="flex flex-col h-screen overflow-hidden">
      <!-- Top progress bar (absolute, no layout shift) -->
      <Progress
        v-if="ui.routeLoading"
        :model-value="ui.progress"
        class="absolute left-0 top-0 z-50 h-1 w-full rounded-none transition-all duration-300 ease-out"
      />

      <!-- Sticky top navigation -->
      <AppTopnav class="sticky top-0 z-40" />

      <!-- Scrollable main content -->
      <AppMain />
    </SidebarInset>
  </SidebarProvider>
</template>
