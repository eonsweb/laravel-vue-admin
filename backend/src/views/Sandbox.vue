<script setup lang="ts">
import type { User } from '@/types'
import { ref, computed, h, watch } from 'vue'

import type {
  ColumnDef,
  ColumnFiltersState,
  ExpandedState,
  SortingState,
  VisibilityState,
} from '@tanstack/vue-table'
import {
  FlexRender,
  getCoreRowModel,
  getExpandedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Button } from '@/components/ui/button'

import { useUserStore } from '@/stores/user'

import { ArrowUpDown, ChevronDown, MoreHorizontal } from 'lucide-vue-next'
import { useUiProgressStore } from '@/stores/ui_progress'

import { Badge } from '@/components/ui/badge'
import { RouterLink, useRoute, useRouter, onBeforeRouteUpdate } from 'vue-router'

import { Select, SelectContent, SelectTrigger, SelectValue } from '@/components/ui/select'

import { storeToRefs } from 'pinia'

// Contants and Stores
const route = useRoute()
const router = useRouter()

const userStore = useUserStore()

const pageSizeOptions: number[] = [10, 25, 50, 100]

const { pagination } = storeToRefs(userStore)

const sorting = ref<SortingState>([])

// Current page from query
const currentPage = computed({
  get: () => Number(route.query.page ?? 1),
  set: (page) => {
    router.push({
      query: { ...route.query, page },
    })
  },
})

//Table rows (from cache)
const users = computed(() => userStore.getUsersByPage(currentPage.value, perPage.value) ?? [])

// Per Page from query OR Store default
const perPage = computed<number>({
  get: () => Number(route.query.per_page ?? userStore.pagination.per_page),
  set: (size) => {
    router.push({
      query: {
        ...route.query,
        per_page: size,
        page: 1,
      },
    })
  },
})

//Result Range
const resultsRange = computed(() => {
  const total = userStore.pagination.total

  if (!total) {
    return { from: 0, to: 0, total: 0 }
  }
  const from = (currentPage.value - 1) * perPage.value + 1
  const to = Math.min(currentPage.value * perPage.value, total)
  return { from, to, total }
})

//Page count for Tanstack
const pageCount = computed(() => userStore.pagination.last_page)

//Fetch when page or perPage changes
watch(
  () => [currentPage.value, perPage.value],
  async ([page, size]) => {
    await userStore.fetchUsers(page, size)
  },
  { immediate: true },
)

watch(
  sorting,
  async () => {
    const sortField = sorting.value[0]?.id ?? null
    const desc = sorting.value[0]?.desc ?? false

    // Build Laravel style sort param
    const sortParam = sortField
      ? (desc ? `-${sortField}` : sortField)
      : undefined

    await userStore.fetchUsers(currentPage.value, perPage.value, {
      sort: sortParam,
    })
  },
  { deep: true }
)



//Fetch on route change
onBeforeRouteUpdate(async (to, _, next) => {
  const page = Number(to.query.page ?? 1)
  const size = Number(to.query.per_page ?? perPage.value)

  const cache = userStore.getUsersByPage(page, size)

  if (!cache || !cache.length) {
    await userStore.fetchUsers(page, size)
  }
  next()
})

const columns: ColumnDef<User>[] = [
  {
    accessorKey: 'rowNumber',
    header: () =>
      h(
        'div',
        {
          class:
            'text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground py-4',
        },
        '#',
      ),
      enableSorting:false,
    cell: ({ row }) => {
      const start = (currentPage.value - 1) * perPage.value
      return h('div', { class: 'text-left text-sm text-foreground' }, start + row.index + 1)
    },
  },
   {
    accessorKey: 'name',
    header: ({ column }) =>
      h(Button, {
        variant: 'ghost',
        class:'cursor-pointer',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Name', h(ArrowUpDown, { class: 'ml-2 w-4 h-4' })]),
  },
  {
    accessorKey: 'username',
    header: () =>
      h(
        'div',
        { class: 'text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground ' },
        'username',
      ),
    cell: ({ row }) => {
      return h('div', { class: 'text-left text-sm text-foreground' }, row.getValue('username'))
    },
  },

  {
    accessorKey: 'email',
    header: () =>
      h(
        'div',
        { class: 'text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground ' },
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
        { class: 'text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground ' },
        'Created At',
      ),
    cell: ({ row }) => {
      return h('div', { class: 'text-left text-sm text-foreground' }, row.getValue('created_at'))
    },
  },
  {
    accessorKey: 'updated_at',
    header: () =>
      h(
        'div',
        { class: 'text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground ' },
        'Updated At',
      ),
    cell: ({ row }) => {
      return h('div', { class: 'text-left text-sm text-foreground' }, row.getValue('updated_at'))
    },
  },
  {
    accessorKey: 'permissions',
    header: () =>
      h(
        'div',
        { class: 'text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground ' },
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
            { variant: 'secondary', class: 'text-[11px] font-medium rounded-full px-2 py-0.5' },
            () => p.name,
          ),
        ),
      )
    },
  },
]

const table = useVueTable<User>({
  data: users, // ✅ ComputedRef<User[]>
  columns,

  manualPagination: true,
  manualSorting:true,

  pageCount: pageCount.value,

  state: {
    pagination: {
      pageIndex: currentPage.value - 1,
      pageSize: userStore.activePageSize ?? userStore.pagination.per_page,
    },
    sorting:sorting.value
  },

  onPaginationChange: (updater) => {
    const next =
      typeof updater === 'function'
        ? updater({
            pageIndex: currentPage.value - 1,
            pageSize: perPage.value,
          })
        : updater

    currentPage.value = next.pageIndex + 1
  },
   onSortingChange: (updater) => {
    sorting.value = typeof updater === 'function' ? updater(sorting.value) : updater
  },

  getCoreRowModel: getCoreRowModel(),
})



</script>

<template>
  <div class="rounded-t-md border shadow-sm overflow-x-auto bg-white">
    <Table>
      <TableHeader class="bg-gray-50">
        <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
          <TableHead v-for="header in headerGroup.headers" :key="header.id" :class="header.column.getCanSort() ? 'cursor-pointer' : ''">
            <FlexRender
              v-if="!header.isPlaceholder"
              :render="header.column.columnDef.header"
              :props="header.getContext()"
            />
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <template v-if="table.getRowModel().rows?.length">
          <TableRow v-for="row in table.getRowModel().rows" :key="row.id">
            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
              <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
            </TableCell>
          </TableRow>
        </template>
        <template v-else>
          <TableRow>
            <TableCell :colspan="columns.length" class="h-24 text-center">
              No users found.
            </TableCell>
          </TableRow>
        </template>
      </TableBody>
    </Table>
  </div>
  <div
    class="flex items-center justify-between py-4 px-4 space-x-2 bg-white shadow-sm rounded-b-md mt-0"
  >
    <div class="text-sm text-muted-foreground">
      Showing {{ resultsRange.from }} to {{ resultsRange.to }} of {{ resultsRange.total }} results
    </div>

    <!-- Per page -->
    <!-- Rows per page -->
    <div class="flex items-center gap-3">
      <span class="text-sm text-muted-foreground whitespace-nowrap"> Rows per page </span>

      <Select
        :model-value="String(perPage)"
        @update:model-value="(value) => (perPage = Number(value))"
      >
        <SelectTrigger class="h-8 w-[90px]">
          <SelectValue />
        </SelectTrigger>

        <SelectContent>
          <SelectItem v-for="size in pageSizeOptions" :key="size" :value="String(size)">
            {{ size }}
          </SelectItem>
        </SelectContent>
      </Select>
    </div>

    <div class="flex items-center gap-2">
      <Button variant="outline" size="sm" :disabled="currentPage === 1" @click="currentPage--">
        Previous
      </Button>

      <Button
        variant="outline"
        size="sm"
        :disabled="currentPage === pagination.last_page"
        @click="currentPage++"
      >
        Next
      </Button>
    </div>
  </div>
</template>
