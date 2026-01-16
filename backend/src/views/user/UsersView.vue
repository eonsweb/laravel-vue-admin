<template>
    <div class="min-h-screen">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Users</h1>
                <p class="text-sm text-muted-foreground">
                    Manage application users and permissions
                </p>
            </div>

            <Button class="cursor-pointer"> + New User </Button>
        </div>

        <DataTable
            class="bg-white dark:bg-slate-900 rounded-xl border bg-background shadow-sm"
            v-if="users"
            :is-loading="userStore.isLoading"
            :columns="columns"
            :data="users"
        />

        <!-- PAGINATION -->
        <div class="flex justify-end mt-4" v-if="!isInitialLoad">
            <div class="flex flex-col gap-6"></div>
        </div>
    </div>

    <div class="flex items-center justify-between border-t px-4 py-3 text-sm text-muted-foreground">
        <!-- Left:results text -->
        <div>
            Showing {{ pagination.per_page }} to {{ pagination.per_page * currentPage }} of
            {{ pagination.total }}
            results
        </div>

        <!-- Center: pagination -->
        <div class="flex items-center gap-2 text-sm">
            <span class="text-muted-foreground">Per page</span>
            <Select v-model="perPage">
                <SelectTrigger class="w-20">
                    <SelectValue />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem :value="10">10</SelectItem>
                    <SelectItem :value="25">25</SelectItem>
                    <SelectItem :value="50">50</SelectItem>
                </SelectContent>
            </Select>
        </div>

        <!-- Right: per page selector -->
        <Pagination
            :items-per-page="pagination.per_page"
            :total="pagination.total"
            v-model:page="currentPage"
        >
            <PaginationContent v-slot="{ items }">
                <PaginationPrevious @click="prevPage" />
                <template v-for="(item, index) in items" :key="index">
                    <PaginationItem
                        v-if="item.type === 'page'"
                        :value="item.value"
                        :is-active="item.value === currentPage"
                        @click="changePage(item.value)"
                    >
                        {{ item.value }}
                    </PaginationItem>
                </template>
                <PaginationEllipsis :index="pagination.last_page" />
                <PaginationNext @click="nextPage" />
            </PaginationContent>
        </Pagination>
    </div>
</template>

<script setup lang="ts">
import type { User } from '@/types'
import { ref, watch, computed, h, onMounted } from 'vue'
import type { ColumnDef } from '@tanstack/vue-table'

import { useUserStore } from '@/stores/user'
import { useUiProgressStore } from '@/stores/ui_progress'

import { Badge } from '@/components/ui/badge'
import { RouterLink, useRoute, useRouter, onBeforeRouteUpdate } from 'vue-router'

// Contants and Stores
const route = useRoute()
const router = useRouter()

const userStore = useUserStore()
const ui_progressStore = useUiProgressStore()

const perPage = ref(10)

onBeforeRouteUpdate(async (to, from, next) => {
    const page = Number(to.query.page ?? 1)
    if (!userStore.usersByPage[page]) {
        ui_progressStore.startRouteLoading()
        ui_progressStore.advanceProgress(50)
        await userStore.fetchUsers(page)
        ui_progressStore.advanceProgress(80)
    }
    next()
})

// Computed Properties
const currentPage = computed({
    get: () => Number(route.query.page ?? 1),
    set: (page) => {
        router.push({
            query: { ...route.query, page },
        })
    },
})

const pagination = computed(() => userStore.pagination)

const isInitialLoad = computed(
    () => !userStore.usersByPage[currentPage.value] && userStore.isLoading,
)

const users = computed(() => userStore.getUsersByPage(currentPage.value))
console.log('Users in UsersView:', users.value)
// Methods

function prevPage() {
    if (currentPage.value > 1) currentPage.value--
}

function nextPage() {
    if (currentPage.value < pagination.value.last_page) currentPage.value++
}

function changePage(page: number) {
    currentPage.value = page
}

const columns: ColumnDef<User>[] = [
    {
        accessorKey: 'name',
        header: () =>
            h(
                'div',
                {
                    class: 'text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground',
                },
                'Name',
            ),
        cell: ({ row }) => {
            return h(
                RouterLink,
                {
                    to: { name: 'user-detail', params: { id: row.original.id } },
                    class: 'text-left tx-sm text-foreground hover:bg-secondary block w-full h-full px-2 py-1 rounded',
                },
                () => row.getValue('name'),
            )
        },
    },
    {
        accessorKey: 'username',
        header: () =>
            h(
                'div',
                {
                    class: 'text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground ',
                },
                'username',
            ),
        cell: ({ row }) => {
            return h(
                'div',
                { class: 'text-left text-sm text-foreground' },
                row.getValue('username'),
            )
        },
    },
    {
        accessorKey: 'email',
        header: () =>
            h(
                'div',
                {
                    class: 'text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground ',
                },
                'Email',
            ),
        cell: ({ row }) => {
            return h('div', { class: 'text-left text-sm text-foreground' }, row.getValue('email'))
        },
    },
    {
        accessorKey: 'created_at',
        header: () =>
            h(
                'div',
                {
                    class: 'text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground ',
                },
                'Created At',
            ),
        cell: ({ row }) => {
            return h(
                'div',
                { class: 'text-left text-sm text-foreground' },
                row.getValue('created_at'),
            )
        },
    },
    {
        accessorKey: 'updated_at',
        header: () =>
            h(
                'div',
                {
                    class: 'text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground ',
                },
                'Updated At',
            ),
        cell: ({ row }) => {
            return h(
                'div',
                { class: 'text-left text-sm text-foreground' },
                row.getValue('updated_at'),
            )
        },
    },
    {
        accessorKey: 'permissions',
        header: () =>
            h(
                'div',
                {
                    class: 'text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground ',
                },
                'Permissions',
            ),
        cell: ({ row }) => {
            const perms = row.getValue('permissions') as { id: number; name: string }[]

            return h(
                'div',
                { class: 'flex flex-wrap gap-1' },
                perms.map((p) =>
                    h(
                        Badge,
                        {
                            variant: 'secondary',
                            class: 'text-[11px] font-medium rounded-full px-2 py-0.5',
                        },
                        () => p.name,
                    ),
                ),
            )
        },
    },
]
</script>

<style scoped>
@reference "../../assets/main.css";

:deep(td) {
    @apply p-0;
}

:deep(td > *) {
    @apply px-4 py-3;
}
</style>
