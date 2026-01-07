<template>
  <header class="flex h-16 shrink-0 items-center border-b bg-background">
    <div class="flex w-full items-center justify-between px-4">
      <SidebarTrigger />

      <div class="flex items-center gap-4">
        <!-- DARKMODE -->
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="outline" class="relative">
              <!-- moon icon -->
              <Moon
                class="h-5 w-5 rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0"
              />
              <!-- sun icon -->
              <Sun
                class="absolute h-5 w-5 rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100"
              />
              <span class="sr-only">Toggle theme</span>
            </Button>
          </DropdownMenuTrigger>

          <DropdownMenuContent align="end">
            <DropdownMenuItem @click="mode = 'light'"> Light </DropdownMenuItem>
            <DropdownMenuItem @click="mode = 'dark'"> Dark </DropdownMenuItem>
            <DropdownMenuItem @click="mode = 'auto'"> System </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
        <!-- NOTIFICATIONS -->
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="relative">
              <Bell class="size-5" />

              <!-- Badge -->
              <span
                v-if="unreadCount > 0"
                class="absolute -right-[2px] -top-[2px] flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-medium text-white"
              >
                {{ unreadCount }}
              </span>
            </Button>
          </DropdownMenuTrigger>

          <DropdownMenuContent class="w-80" align="end">
            <DropdownMenuLabel class="flex items-center justify-between">
              <span class="text-base font-semibold">Notifications</span>
              <span class="rounded-md bg-secondary px-2 py-0.5 text-xs text-secondary-foreground">
                {{ unreadCount }} new
              </span>
            </DropdownMenuLabel>

            <DropdownMenuSeparator />

            <!-- Notification Items -->
            <div class="max-h-72 overflow-y-auto">
              <DropdownMenuItem
                v-for="n in notifications"
                :key="n.id"
                class="flex w-full cursor-pointer flex-col items-start gap-1 py-3"
              >
                <div class="flex w-full justify-between">
                  <span class="font-medium">{{ n.title }}</span>
                  <span class="text-xs text-muted-foreground">
                    {{ n.time }}
                  </span>
                </div>

                <p class="text-sm text-muted-foreground leading-tight">
                  {{ n.message }}
                </p>
              </DropdownMenuItem>
            </div>
          </DropdownMenuContent>
        </DropdownMenu>

        <!-- USER INFO -->
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="cursor-pointer flex items-center gap-3">
              <Avatar class="h-8 w-8">
                <AvatarImage src="https://github.com/shadcn.png" />
                <AvatarFallback>AD</AvatarFallback>
              </Avatar>

              <div class="hidden md:flex flex-col text-left leading-tight">
                <span class="font-medium uppercase">{{ auth.currentUser?.username }}</span>
                <span class="text-sm text-muted-foreground capitalize">Admin</span>
              </div>
            </Button>
          </DropdownMenuTrigger>

          <DropdownMenuContent class="w-56" align="end">
            <DropdownMenuLabel class="font-semibold">My Account</DropdownMenuLabel>
            <DropdownMenuSeparator />

            <DropdownMenuGroup>
              <DropdownMenuItem v-for="item in userDropdownItems.main" :key="item.name">
                <router-link :to="{ name: item.url }" class="w-full flex items-center gap-2">
                  <component :is="item.icon" class="size-4 mr-2" />
                  {{ item.name }}
                </router-link>
              </DropdownMenuItem>
            </DropdownMenuGroup>

            <DropdownMenuSeparator />

            <DropdownMenuItem
              v-for="item in userDropdownItems.footer"
              :key="item.name"
              class="text-red-600 focus:text-red-600"
              as-child
            >
              <router-link
                v-if="item.name !== 'Log out'"
                :to="{ name: item.url }"
                class="flex items-center gap-2"
              >
                <component :is="item.icon" class="size-4" />
                {{ item.name }}
              </router-link>

              <!-- Log out button -->
              <button
                v-else
                @click="logout"
                class="w-full flex items-center gap-2 text-red-600 cursor-pointer"
              >
                <component :is="item.icon" class="size-4" />
                {{ item.name }}
              </button>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { SidebarTrigger } from '@/components/ui/sidebar'
import { CircleUserRound, Settings, LogOut, Bell, Moon, Sun } from 'lucide-vue-next'

import { useColorMode } from '@vueuse/core'

import { ref, computed } from 'vue'

import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

// reactive color mode: 'light', 'dark', 'auto'
const mode = useColorMode()

const userDropdownItems = {
  main: [
    { name: 'Profile', url: 'users', icon: CircleUserRound },
    { name: 'Settings', url: 'settings', icon: Settings },
  ],
  footer: [{ name: 'Log out', url: '#', icon: LogOut }],
}

const notifications = ref([
  {
    id: 1,
    title: 'New User Registered',
    message: 'John Doe created an account.',
    time: '2m ago',
    read: false,
  },
  {
    id: 2,
    title: 'Payment Received',
    message: 'You received $49.00 from Acme.',
    time: '10m ago',
    read: false,
  },
  {
    id: 3,
    title: 'Server Restart Required',
    message: 'A restart is needed.',
    time: '1h ago',
    read: true,
  },
  { id: 4, title: 'New Message', message: 'Sarah sent you a message.', time: '3h ago', read: true },
  {
    id: 5,
    title: 'Failed Login Attempt',
    message: 'Suspicious login attempt.',
    time: 'Yesterday',
    read: false,
  },
])

const logout = () => {
  auth.setToken('')
  router.push({ name: 'login' })
}

const unreadCount = computed(() => notifications.value.filter((n) => !n.read).length)
</script>
