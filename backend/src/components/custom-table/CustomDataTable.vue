<script setup lang="ts" generic="TData">
import type {
  PaginationState,
  SortingState,
} from '@tanstack/vue-table'

import type { TableProps } from '@/types/table'

import {
  FlexRender,
  getCoreRowModel,
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


const props = defineProps<TableProps<TData>>()

const emit = defineEmits<{
  (e: 'pagination-change', value: PaginationState): void
  (e: 'sorting-change', value: SortingState): void
}>()

const table = useVueTable({
  get data() {
    return props.data
  },
  get columns() {
    return props.columns
  },

  manualPagination: true,
  manualSorting: true,
  enableSortingRemoval: true,

  pageCount: props.pageCount,

  state: {
    get pagination() {
      return props.pagination
    },
    get sorting() {
      return props.sorting
    },
  },

  onPaginationChange: (updater) => {
    const next =
      typeof updater === 'function'
        ? updater(props.pagination)
        : updater

    emit('pagination-change', next)
  },

  onSortingChange: (updater) => {
    const next =
      typeof updater === 'function'
        ? updater(props.sorting)
        : updater

    emit('sorting-change', next)
  },

  getCoreRowModel: getCoreRowModel(),
})
</script>

<template>
  <div class="border shadow-sm bg-background">
    <div v-if="isFetching" class="px-4 py-2 text-sm text-muted-foreground">
      Updating...
    </div>

    <Table>
      <TableHeader>
        <TableRow
          v-for="headerGroup in table.getHeaderGroups()"
          :key="headerGroup.id"
        >
          <TableHead
            v-for="header in headerGroup.headers"
            :key="header.id"
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
        <template v-if="table.getRowModel().rows.length">
          <TableRow
            v-for="row in table.getRowModel().rows"
            :key="row.id"
          >
            <TableCell
              v-for="cell in row.getVisibleCells()"
              :key="cell.id"
            >
              <FlexRender
                :render="cell.column.columnDef.cell"
                :props="cell.getContext()"
              />
            </TableCell>
          </TableRow>
        </template>

        <template v-else>
          <TableRow>
            <TableCell
              :colspan="columns.length"
              class="h-24 text-center"
            >
              No results found.
            </TableCell>
          </TableRow>
        </template>
      </TableBody>
    </Table>
    
  </div>
</template>
