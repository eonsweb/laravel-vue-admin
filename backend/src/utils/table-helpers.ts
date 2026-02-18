// utils/table-helpers.ts
import { h } from 'vue'
import { Button } from '@/components/ui/button'
import { ChevronUp, ChevronDown } from 'lucide-vue-next'
import type { ColumnDef } from '@tanstack/vue-table'
import { Badge } from '@/components/ui/badge'

export function createSortableHeader(label: string) {
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
                h('span', { class: 'flex flex-col leading-none -my-1' }, [
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
                ]),
            ],
        )
    }
}

export function createBadgeCell<T>(accessorKey: keyof T, badgeClass?: string): ColumnDef<T> {
    return {
        accessorKey: accessorKey as string,
        header: ({ column }) => createSortableHeader(String(accessorKey))({ column }),
        cell: ({ row }) => {
            const values = row.getValue(accessorKey as string)
            if (!values || !Array.isArray(values)) return null

            return h(
                'div',
                { class: 'flex flex-wrap gap-1' },
                values.map((value: any) =>
                    h(
                        Badge,
                        {
                            variant: 'secondary',
                            class: `text-[11px] font-medium rounded-full px-2 py-0.5 ${badgeClass || ''}`,
                        },
                        () => value.name || value,
                    ),
                ),
            )
        },
    }
}
