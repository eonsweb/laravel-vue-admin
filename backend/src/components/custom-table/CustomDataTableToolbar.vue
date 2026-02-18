<script setup lang="ts">
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { X } from 'lucide-vue-next'

interface Props {
  search?: string
  showSearch?: boolean
  placeholder?: string
}

const props = withDefaults(defineProps<Props>(), {
  showSearch: true,
  placeholder: 'Search...',
})

const emit = defineEmits<{
  (e: 'update:search', value: string): void
  (e: 'clear-search'): void
}>()
</script>

<template>
  <div
    class="flex flex-wrap items-center justify-between rounded-t-md gap-3 p-4 border-b bg-background"
  >
    <!-- Left side: search -->
    <div v-if="showSearch" class="relative max-w-sm w-full">
      <Input
        :model-value="search"
        :placeholder="placeholder"
        class="pr-8"
        @update:model-value="emit('update:search', $event)"
      />

      <Button
        v-if="search"
        size="sm"
        variant="ghost"
        class="absolute right-2 top-1/2 -translate-y-1/2 h-6 w-6 p-0"
        @click="emit('clear-search')"
      >
        <X class="h-4 w-4" />
      </Button>
    </div>

    <!-- Right side: actions -->
    <div class="flex items-center gap-2">
      <slot name="actions" />
    </div>
  </div>
</template>
