<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

interface ResultsRange {
  from: number
  to: number
  total: number
}

interface Props {
  pageIndex: number
  pageSize: number
  pageCount: number
  resultsRange: ResultsRange
  pageSizeOptions?: number[]
}

const props = withDefaults(defineProps<Props>(), {
  pageSizeOptions: () => [10, 25, 50, 100],
})

const emit = defineEmits<{
  (e: 'page-change', pageIndex: number): void
  (e: 'page-size-change', pageSize: number): void
}>()
</script>

<template>
  <div
    class="flex items-center justify-between py-4 px-4 space-x-2 rounded-b-md bg-background shadow-sm border-t"
  >
    <!-- Results range -->
    <div class="text-sm text-muted-foreground">
      Showing {{ resultsRange.from }} to {{ resultsRange.to }} of
      {{ resultsRange.total }} results
    </div>

    <!-- Rows per page -->
    <div class="flex items-center gap-3">
      <span class="text-sm text-muted-foreground whitespace-nowrap">
        Rows per page
      </span>

      <Select
        :model-value="String(pageSize)"
        @update:model-value="emit('page-size-change', Number($event))"
      >
        <SelectTrigger class="h-8 w-[90px]">
          <SelectValue />
        </SelectTrigger>

        <SelectContent>
          <SelectItem
            v-for="size in pageSizeOptions"
            :key="size"
            :value="String(size)"
          >
            {{ size }}
          </SelectItem>
        </SelectContent>
      </Select>
    </div>

    <!-- Navigation -->
    <div class="flex items-center gap-2">
      <Button
        variant="outline"
        size="sm"
        :disabled="pageIndex === 0"
        @click="emit('page-change', pageIndex - 1)"
      >
        Previous
      </Button>

      <Button
        variant="outline"
        size="sm"
        :disabled="pageIndex + 1 >= pageCount"
        @click="emit('page-change', pageIndex + 1)"
      >
        Next
      </Button>
    </div>
  </div>
</template>
