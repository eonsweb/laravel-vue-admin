import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import type { SortingState, PaginationState } from '@tanstack/vue-table'

export function useRouteTableState() {
    const route = useRoute()
    const router = useRouter()

    /* --------------------------------------------
     * Pagination
     * -------------------------------------------- */

    const page = computed({
        get: () => Number(route.query.page ?? 1),
        set: (value: number) => {
            router.push({
                query: { ...route.query, page: value },
            })
        },
    })

    const perPage = computed({
        get: () => Number(route.query.per_page ?? 10),
        set: (value: number) => {
            router.push({
                query: {
                    ...route.query,
                    per_page: value,
                    page: 1,
                },
            })
        },
    })

    const pagination = computed<PaginationState>(() => ({
        pageIndex: page.value - 1,
        pageSize: perPage.value,
    }))

    function updatePagination(state: PaginationState) {
        page.value = state.pageIndex + 1
    }

    /* --------------------------------------------
     * Sorting (Laravel style)
     * -------------------------------------------- */

    const sorting = computed<SortingState>(() => {
        const sort = route.query.sort as string | undefined
        if (!sort) return []

        const desc = sort.startsWith('-')
        const id = desc ? sort.slice(1) : sort

        return [{ id, desc }]
    })

    function updateSorting(state: SortingState) {
        const first = state[0]

        const sortParam = first ? (first.desc ? `-${first.id}` : first.id) : undefined

        const query = {
            ...route.query,
            sort: sortParam,
            page: 1,
        }

        if (!sortParam) delete query.sort

        router.push({ query })
    }

    return {
        page,
        perPage,
        pagination,
        sorting,
        updatePagination,
        updateSorting,
    }
}
