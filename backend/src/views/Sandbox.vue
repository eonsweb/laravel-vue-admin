<script setup lang="ts">
  // TYPES
import type { User } from '@/types'

//VUE CORE
import { ref, computed, h, watch } from 'vue'
import { RouterLink, useRoute, useRouter, onBeforeRouteUpdate } from 'vue-router'

//STATE
import { storeToRefs } from 'pinia'
import { useUserStore } from '@/stores/user'


//TABLE (TANSTACK)
import type {
  ColumnDef,
  SortingState,
} from '@tanstack/vue-table'
import {
  FlexRender,
  getCoreRowModel,
  useVueTable,
} from '@tanstack/vue-table'


//UI COMPONENTS
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Select, SelectItem,SelectContent, SelectTrigger, SelectValue } from '@/components/ui/select'

//ICONS
import { ArrowDown,ArrowUp, ChevronUp,ChevronDown } from 'lucide-vue-next'


/* -------------------------------------------------
 * ROUTER + STORE SETUP
 * ------------------------------------------------- */
const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const { pagination } = storeToRefs(userStore)

/* -------------------------------------------------
* PAGINATION STATE
* ------------------------------------------------- */
const pageSizeOptions: number[] = [10, 25, 50, 100]

//CURRENT PAGE (synced with query param `page`)
const currentPage = computed({
  get: () => Number(route.query.page ?? 1),
  set: (page) => {
    router.push({
      query: { ...route.query, page },
    })
  },
})

//Items per page (query param `per_page`)
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

/**
 * Page count for TanStack manual pagination mode
 */
const pageCount = computed(() => userStore.pagination.last_page)

/**
 * Total pagination info text "Showing x–y of z"
 */
const resultsRange = computed(() => {
  const total = userStore.pagination.total

  if (!total) {
    return { from: 0, to: 0, total: 0 }
  }
  const from = (currentPage.value - 1) * perPage.value + 1
  const to = Math.min(currentPage.value * perPage.value, total)
  return { from, to, total }
})


/* -------------------------------------------------
 * SORTING STATE
 * ------------------------------------------------- */
/**
 * Laravel-style sort param from URL
 * examples:
 *   name      → asc
 *   -name     → desc
 */
//Tanstack table sorting state
const sortParam = computed(() => route.query.sort as string | undefined)

// Laravel-style sort param for fetch/getUsersByPage
const sorting = computed<SortingState>({
  get: () => {
    const sort = route.query.sort as string | undefined
    if (!sort) return []

    const desc = sort.startsWith('-')
    const id = desc ? sort.slice(1) : sort

    return [{ id, desc }]
  },

  set: value => {
  const first = value[0]

  const sortParam = first
    ? first.desc ? `-${first.id}` : first.id
    : undefined

  const nextQuery: Record<string, any> = {
    ...route.query,
    sort: sortParam,
    page: 1,
  }

  if (!sortParam) delete nextQuery.sort
  if (currentPage.value === 1) delete nextQuery.page

  // avoid pushing identical query
  if (JSON.stringify(nextQuery) === JSON.stringify(route.query)) return

  router.push({ query: nextQuery })

  },
})

/* -------------------------------------------------
 * DATA FROM STORE (CACHED)
 * ------------------------------------------------- */
// Users for current page/sort from store cache

 const users = computed(() =>
  userStore.getUsersByPage(currentPage.value, perPage.value, sortParam.value) ?? []
)

console.log(users.value)

/* -------------------------------------------------
 * WATCHERS
 * ------------------------------------------------- */
// Fetch users when page, perPage, or sorting changes
watch(
  [currentPage, perPage, sortParam],
  async ([page, perPage, sortValue]) => {
    await userStore.fetchUsers(page, perPage, {sort:sortValue})
  },
  { immediate: true },
)

/* -----------------------------
 * ROUTE CHANGE
 * ----------------------------- */
onBeforeRouteUpdate(async (to, _, next) => {
  const page = Number(to.query.page ?? 1)
  const size = Number(to.query.per_page ?? perPage.value)
  const sort = to.query.sort as string | undefined
  const cache = userStore.getUsersByPage(page, size, sort)
  if (!cache || !cache.length) await userStore.fetchUsers(page, size, { sort: sort })
  next()
})


/* -----------------------------
 * Tanstack Vue Table
 * ----------------------------- */
/**
 * Tanstack Data Table
 */

function sortableHeader(label: string) {
  return ({ column }: any) => {
    return h(
      Button,
      {
        variant: 'ghost',
        class: 'cursor-pointer select-none flex items-center gap-2',
        onClick: () => column.toggleSorting(),
      },
      () => [
        h('span', label),
        h(
          'span',
          { class: 'flex flex-col leading-none -my-1' },
          [
            h(ChevronUp, {
              class: [
                'h-3 w-3 transition-opacity -mb-1',
                column.getIsSorted() === 'asc' ? 'opacity-100 stroke-3' : 'opacity-20',
              ].join(' '),
            }),
            h(ChevronDown, {
              class: [
                'h-3 w-3 transition-opacity -mt-0.5',
                column.getIsSorted() === 'desc' ? 'opacity-100 stroke-3' : 'opacity-20',
              ].join(' '),
            }),
          ]
        )
      ]
    )
  }
}

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
    enableSorting:true,
    header:sortableHeader('Name'),
  },
  {
    accessorKey: 'username',
    enableSorting:true,
    header:sortableHeader('Username'),
    
  },

  {
   accessorKey: 'email',
    enableSorting:true,
    header:sortableHeader('Email')
  },
  {
    accessorKey: 'updated_at',
    enableSorting:true,
    header:sortableHeader('Updated at')
  },
  {
    accessorKey: 'permissions',
    header: () =>
      h(
        'div',
        { class: 'text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground ' },
        'Permissions',
      ),
      enableSorting:false,
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
  enableSortingRemoval: true,
  pageCount: pageCount.value,

  state: {
    pagination: {
      pageIndex: currentPage.value - 1,
      pageSize: perPage.value
    },
    get sorting() {
    return sorting.value
  }
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
    sorting.value = 
        typeof updater === 'function' ? updater(sorting.value) : updater
  },

  getCoreRowModel: getCoreRowModel(),
})



</script>

<template>
  <div class="rounded-t-md border shadow-sm overflow-x-auto bg-background">
    <Table>
      <TableHeader class="bg-muted dark:bg-muted/20">
        <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
          <TableHead 
            v-for="header in headerGroup.headers" 
            :key="header.id" 
            :class="header.column.getCanSort() ? 'cursor-pointer' : ''"
            
          >
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
    class="flex items-center justify-between py-4 px-4 space-x-2 bg-background shadow-sm rounded-b-md mt-0"
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
