<script lang="ts" setup>
import { cn } from '@/lib/utils'

defineProps<{
  href?: string
  isActive?: boolean
}>()

const emit = defineEmits(['click'])
</script>

<template>
  <a
    :href="href"
    @click="emit('click', $event)"
    :class="
      cn(
        'flex items-center justify-center rounded-md px-3 py-1 text-sm font-medium transition-colors',
        'hover:bg-accent hover:text-accent-foreground',
        isActive ? 'bg-primary text-primary-foreground' : 'text-muted-foreground',
      )
    "
  >
    <slot />
  </a>
</template>

<style scoped></style>
