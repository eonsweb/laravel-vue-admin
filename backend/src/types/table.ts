import type { ColumnDef, PaginationState, SortingState } from '@tanstack/vue-table'

export interface TableProps<TData> {
    data: TData[]
    columns: ColumnDef<TData>[]
    pageCount: number
    pagination: PaginationState
    sorting: SortingState
    isFetching?: boolean
}