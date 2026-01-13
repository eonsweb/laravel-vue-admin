<script setup lang="ts">
  // TYPES
import type { User } from '@/types'

import { debounce } from '@/composables/useDebounce'
import { buildUsersCacheKey } from '@/utils/cacheKey'

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
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from '@/components/ui/dropdown-menu'

//ICONS
import {  ChevronUp,ChevronDown,X ,MoreVertical, Pencil, Trash2} from 'lucide-vue-next'




/* -------------------------------------------------
 * ROUTER + STORE SETUP
 * ------------------------------------------------- */
const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const { pagination } = storeToRefs(userStore)

/* -------------------------------------------------
 * FLAG TO PREVENT DUPLICATE FETCHES
 * ------------------------------------------------- */
const isFetchingFromRouteUpdate = ref(false)
const hasInitialFetchFromRouteGuard = ref(false)


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
 * SEARCHING STATE
 * ------------------------------------------------- */
const search = computed({
  get:()=>(route.query.search as string) ?? '',
  set:(value:string)=>{
    router.push({
      query:{
        ...route.query,
        search:value || undefined,
        page:1, // reset page on new search
      }
    })
  }
})


/* -------------------------------------------------
* DEBOUNCED SEARCH
* ------------------------------------------------- */
const rawSearch = ref(search.value)

// Create a debounced function to update search
const updateSearch = debounce((value: string) => {
  search.value = value
}, 600,{leading:false,trailing:true})

// Watch rawSearch and call debounced function
watch(rawSearch, (value) => {
  updateSearch(value)
})

/* -------------------------------------------------
 * DATA FROM STORE (CACHED)
 * ------------------------------------------------- */
// Users for current page/sort from store cache

 const users = computed(() =>
  userStore.getUsersByPage(currentPage.value, perPage.value, sortParam.value,search.value) ?? []
)

console.log('loggin users',users.value)

/* -------------------------------------------------
 * WATCHERS
 * ------------------------------------------------- */

// Watch for query changes BUT skip if route update is handling it
watch(
  [currentPage, perPage, sortParam, search],
  async ([page, perPage, sortValue, searchValue], oldValues) => {
    // Skip if:
    // 1. Route update is currently handling the fetch
    // 2. This is the initial load (handled by route guard)
    // 3. Values haven't actually changed
    if (isFetchingFromRouteUpdate.value || hasInitialFetchFromRouteGuard.value) {
      hasInitialFetchFromRouteGuard.value = false // Reset after first skip
      return
    }

    // Skip initial load if route guard already handled it
    if (sessionStorage.getItem('routeGuardFetching')) {
      sessionStorage.removeItem('routeGuardFetching')
      return
    }
    
    // Check if values actually changed
    const [oldPage, oldPerPage, oldSort, oldSearch] = oldValues
    if (page === oldPage && perPage === oldPerPage && 
        sortValue === oldSort && searchValue === oldSearch) {
      return
    }
    
    const cacheKey = buildUsersCacheKey(page, perPage, sortValue,searchValue)
    if (!userStore.usersByPage[cacheKey]) {
      await userStore.fetchUsers(page, perPage, {
        sort: sortValue,
        search: searchValue,
      })
    }
  },
  // { immediate: true },
)

/* -----------------------------
 * ROUTE CHANGE
 * ----------------------------- */
onBeforeRouteUpdate(async (to, _, next) => {
  const page = Number(to.query.page ?? 1)
  const size = Number(to.query.per_page ?? perPage.value)
  const sort = to.query.sort as string | undefined
  const search = to.query.search as string | undefined

  // Set flag to prevent watcher from fetching
  isFetchingFromRouteUpdate.value = true

  const cacheKey = buildUsersCacheKey(page, size, sort, search)

  // Only fetch if not cached
  if (!userStore.usersByPage[cacheKey]) {
    try {
      await userStore.fetchUsers(page, size, { 
        sort: sort,
        search: search 
      })
    } catch (error) {
      console.error('Failed to fetch users:', error)
      // Optionally handle error (show message, redirect, etc.)
    }
  }

   // Reset flag after a small delay to ensure watcher doesn't trigger
  setTimeout(() => {
    isFetchingFromRouteUpdate.value = false
  }, 50)
  
  next()
})


/* -----------------------------
 * Tanstack Vue Table
 * ----------------------------- */
/**
 * Tanstack Data Table
 */

function onEdit(user: User) {
  // Example navigation or modal
  console.log('Edit user', user.id)
  // router.push({ name: 'users.edit', params: { id: user.id } })
}

function onDelete(user: User) {
  console.log('Delete user', user.id)
  // You can open confirmation dialog here
  // userStore.deleteUser(user.id)
}


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
                'h-3 w-3 transition-opacity -mb-0.5',
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

function renderActionsCell(row: any) {
  const user = row.original as User

  return h(
    'div',
    { class: 'flex justify-end' },
    [
      h(
        DropdownMenu,
        {},
        {
          default: () => [
            h(
              DropdownMenuTrigger,
              { asChild: true },
              () =>
                h(
                  Button,
                  { variant: 'ghost', size: 'sm', class: 'h-8 w-8 p-0' },
                  () => h(MoreVertical, { class: 'h-4 w-4' })
                )
            ),

            h(
              DropdownMenuContent,
              { align: 'end', class: 'w-36' },
              () => [
                h(
                  DropdownMenuItem,
                  { onClick: () => onEdit(user) },
                  () => [
                    h(Pencil, { class: 'h-4 w-4 mr-2' }),
                    'Edit',
                  ]
                ),
                h(
                  DropdownMenuItem,
                  { onClick: () => onDelete(user) },
                  () => [
                    h(Trash2, { class: 'h-4 w-4 mr-2 text-red-600' }),
                    'Delete',
                  ]
                ),
              ]
            ),
          ],
        }
      ),
    ]
  )
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
  {
    id:'actions',
    enableSorting:false,
    header: () =>
      h(  
        'div',
        { class: 'text-right text-xs font-semibold uppercase tracking-wide text-muted-foreground ' },
        'Actions',
      ),
      cell:({row})=>renderActionsCell(row)
  }
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
     <div class="p-4 flex justify-end">
      <div class="relative max-w-sm">
        <Input 
          v-model="rawSearch" 
          placeholder="Search users..." 
          class="pr-8"
        />
        <Button 
          v-if="rawSearch" 
          @click="rawSearch = ''" 
          variant="ghost" 
          size="sm"
          title="Clear search"
          class="absolute right-2 top-1/2 transform -translate-y-1/2 h-6 w-6 p-0"
        >
          <X class="h-4 w-4" />
        </Button>
      </div>
    </div>

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
