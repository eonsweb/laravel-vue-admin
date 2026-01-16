<template>
    <Sidebar v-bind="props" class="[--sidebar-width:16rem] border-r bg-sidebar">
        <!-- Brand -->
        <SidebarHeader class="px-3 py-4">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        size="lg"
                        as-child
                        class="hover:bg-accent rounded-xl transition"
                    >
                        <a href="#" class="flex items-center gap-3">
                            <div
                                class="flex aspect-square size-9 items-center justify-center rounded-xl bg-sidebar-primary text-sidebar-primary-foreground shadow-sm"
                            >
                                <GalleryVerticalEnd class="size-5" />
                            </div>

                            <div class="flex flex-col leading-tight">
                                <span class="text-base font-semibold tracking-tight">Eonsweb</span>
                                <span class="text-xs text-muted-foreground">Solutions</span>
                            </div>
                        </a>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <Separator orientation="horizontal" />

        <!-- Main Navigation -->
        <SidebarContent class="px-2 py-3">
            <SidebarGroup>
                <SidebarGroupContent>
                    <SidebarMenu>
                        <SidebarMenuItem v-for="item in sidebarItems.mainNav" :key="item.name">
                            <SidebarMenuButton
                                as-child
                                class="rounded-lg hover:bg-accent transition text-sm font-medium"
                            >
                                <router-link
                                    :to="{ name: item.url }"
                                    exact-active-class="text-primary bg-muted"
                                    class="flex items-center gap-3"
                                >
                                    <component
                                        :is="item.icon"
                                        class="size-5 text-muted-foreground"
                                    />
                                    <span>{{ item.name }}</span>
                                </router-link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>
        </SidebarContent>

        <!-- Footer Links -->
        <SidebarFooter class="px-2 py-3">
            <SidebarMenu>
                <SidebarMenuItem v-for="item in sidebarItems.secondaryNav" :key="item.name">
                    <SidebarMenuButton
                        as-child
                        class="rounded-lg hover:bg-accent transition text-sm font-medium"
                    >
                        <a :href="item.url" class="flex items-center gap-3">
                            <component :is="item.icon" class="size-5 text-muted-foreground" />
                            <span>{{ item.name }}</span>
                        </a>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarFooter>

        <SidebarRail />
    </Sidebar>
</template>

<script setup lang="ts">
import type { SidebarProps } from '@/components/ui/sidebar'

import {
    GalleryVerticalEnd,
    LayoutDashboard,
    Users,
    Settings,
    ChartNoAxesCombined,
} from 'lucide-vue-next'

import {
    Sidebar,
    SidebarContent,
    SidebarGroup,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarRail,
} from '@/components/ui/sidebar'

import { Separator } from '@/components/ui/separator'

const props = defineProps<SidebarProps>()

const sidebarItems = {
    mainNav: [
        { name: 'Dashboard', url: 'dashboard', icon: LayoutDashboard },
        { name: 'Users', url: 'users', icon: Users },
        { name: 'Analytics', url: 'settings', icon: ChartNoAxesCombined },
        { name: 'Sandbox', url: 'sandbox', icon: ChartNoAxesCombined },
    ],
    secondaryNav: [{ name: 'Settings', url: '#', icon: Settings }],
}
</script>
