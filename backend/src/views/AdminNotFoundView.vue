<template>
    <div class="flex justify-center items-center h-screen">
        <div class="text-7xl px-8 font-bold border-r pb-8">404</div>
        <div class="px-8">
            <h1 class="text-5xl font-bold mb-4">Page Not Found</h1>
            <p class="text-gray-600 mb-4">Please check the url in address bar and try again</p>
            <div>
                <Button class="cursor-pointer" @click="goBack">Go back home</Button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()
const goBack = () => {
    router.push({ name: 'dashboard' })
}
</script>

<style scoped></style>
