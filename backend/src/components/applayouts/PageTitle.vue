<template>
    <div class="font-bold text-2l">
        {{ page_title }}
    </div>
</template>

<script setup lang="ts">
import { ref, defineProps } from 'vue'

defineProps({
    page_title: {
        type: String,
        required: true,
    },
})
</script>
