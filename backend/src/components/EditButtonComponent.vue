<template>
    <div>
        <a :href="`/users/${id}/edit`" title="Edit User">
            <Ellipsis class="size-6 text-gray-900" />
        </a>
    </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { SquarePen, Ellipsis } from 'lucide-vue-next'
defineProps<{
    id: number
}>()
</script>

<style scoped></style>
