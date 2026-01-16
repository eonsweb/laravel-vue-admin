<template>
    <div v-if="user">User: {{ user.name }}</div>
    <div v-else>Loading...</div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from '@/stores/user'
import type { User } from '@/types'

const route = useRoute()
const userStore = useUserStore()

const userId = parseInt(route.params.id as string)

onMounted(async () => {
    await userStore.fetchUserById(userId)
})

const user = computed(() => userStore.user)
</script>

<style scoped></style>
