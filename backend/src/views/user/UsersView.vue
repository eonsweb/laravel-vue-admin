<script setup lang="ts">
import type { User } from '@/types'
import type { ColumnDef } from '@tanstack/vue-table'

import { computed, h } from 'vue'
import { useRoute, useRouter } from 'vue-router'

import { useUsersQuery } from '@/hooks/useUsersQuery'
import { useRouteTableState } from '@/composables/useRouteTableState'
import { useDebouncedSearch } from '@/composables/useDebouncedSearch'

import DataTable from '@/components/custom-table/CustomDataTable.vue'
import DataTableToolbar from '@/components/custom-table/CustomDataTableToolbar.vue'
import DataTableFooter from '@/components/custom-table/CustomDataTableFooter.vue'

import { Button } from '@/components/ui/button'
import { ArrowUp, ArrowDown } from 'lucide-vue-next'

/* --------------------------------------------
 * Router search param (feature responsibility)
 * -------------------------------------------- */

const route = useRoute()
const router = useRouter()

const searchQuery = computed({
  get: () => (route.query.search as string) ?? '',
  set: (value: string) => {
    router.push({
      query: {
        ...route.query,
        search: value || undefined,
        page: 1,
      },
    })
  },
})

/* --------------------------------------------
 * Debounced Search
 * -------------------------------------------- */

const { value: search, reset } = useDebouncedSearch(
  searchQuery.value,
  (value) => {
    searchQuery.value = value
  },
  { delay: 600 },
)

/* --------------------------------------------
 * Route table state
 * -------------------------------------------- */

const {
  page,
  perPage,
  pagination,
  sorting,
  updatePagination,
  updateSorting,
} = useRouteTableState()

/* --------------------------------------------
 * Vue Query
 * -------------------------------------------- */

const { data, isFetching } = useUsersQuery(
  page.value,
  perPage.value,
  {
    sort: sorting.value[0]
      ? sorting.value[0].desc
        ? `-${sorting.value[0].id}`
        : sorting.value[0].id
      : undefined,
    search: searchQuery.value,
  },
  true,
)

const users = computed(() => data.value?.data ?? [])
const pageCount = computed(() => data.value?.meta.last_page ?? 0)

const resultsRange = computed(() => {
  const total = data.value?.meta.total ?? 0

  if (!total) {
    return { from: 0, to: 0, total: 0 }
  }

  const from = pagination.value.pageIndex * pagination.value.pageSize + 1
  const to = Math.min(
    (pagination.value.pageIndex + 1) * pagination.value.pageSize,
    total,
  )

  return { from, to, total }
})


/* --------------------------------------------
 * Columns (unchanged)
 * -------------------------------------------- */

function sortableHeader(label: string) {
  return ({ column }: any) =>
    h(
      Button,
      {
        variant: 'ghost',
        onClick: () => column.toggleSorting(),
      },
      () => [
        label,
        h(ArrowUp, { class: 'h-3 w-3' }),
        h(ArrowDown, { class: 'h-3 w-3' }),
      ],
    )
}

const columns: ColumnDef<User>[] = [
  {
    accessorKey: 'name',
    header: sortableHeader('Name'),
  },
  {
    accessorKey: 'email',
    header: sortableHeader('Email'),
  },
]
</script>

<template>
  <div class="space-y-0">
    <DataTableToolbar
      :search="search"
      placeholder="Search users..."
      @update:search="search = $event"
      @clear-search="reset"
    >
      <template #actions>
        <Button size="sm">Create User</Button>
      </template>
    </DataTableToolbar>

    <DataTable
      :data="users"
      :columns="columns"
      :page-count="pageCount"
      :pagination="pagination"
      :sorting="sorting"
      :is-fetching="isFetching"
      @pagination-change="updatePagination"
      @sorting-change="updateSorting"
    />

    <DataTableFooter
      :page-index="pagination.pageIndex"
      :page-size="pagination.pageSize"
      :page-count="pageCount"
      :results-range="resultsRange"
      @page-change="(pageIndex) => updatePagination({ ...pagination, pageIndex })"
      @page-size-change="perPage = $event"
    />
  </div>
</template>
