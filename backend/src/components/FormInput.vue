<template>
    <div>
        <template v-if="type === 'text'">
            <div class="grid w-full max-w-sm items-center gap-1.5">
                <Label :for="name">{{ name }}</Label>
                <Input type="text" :id="name" :name="name" :placeholder="placeholder" />
            </div>
        </template>
        <template v-else-if="type === 'email'">
            <div class="grid w-full max-w-sm items-center gap-1.5">
                <Label :for="name">{{ name }}</Label>
                <Input type="email" :id="name" :name="name" />
            </div>
        </template>
        <template v-else-if="type === 'password'">
            <div class="grid w-full max-w-sm items-center gap-1.5">
                <div class="relative flex items-center">
                    <Label :for="name">{{ name }}</Label>
                    <Input
                        :type="showPassword ? 'text' : 'password'"
                        :id="name"
                        :name="name"
                        :placeholder="placeholder"
                    />
                    <button type="button" class="absolute right-3" @click="togglePassword">
                        <component
                            :is="showPassword ? LucideEyeOff : LucideEye"
                            :size="20"
                            class="text-gray-400 cursor-pointer"
                        />
                    </button>
                </div>
            </div>
        </template>
        <template v-else-if="type === 'number'">
            <div class="grid w-full max-w-sm items-center gap-1.5">
                <Label :for="name">{{ name }}</Label>
                <Input type="number" :id="name" :name="name" step="0.01" />
            </div>
        </template>
        <template v-else-if="type === 'textarea'">
            <div class="grid w-full max-w-sm items-center gap-1.5">
                <Label :for="name">{{ name }}</Label>
                <textarea :id="name" :name="name" :placeholder="placeholder" />
            </div>
        </template>
        <template v-else-if="type === 'file'">
            <div class="grid w-full max-w-sm items-center gap-1.5">
                <Label for="picture">Picture</Label>
                <Input :id="name" type="file" />
            </div>
        </template>
    </div>
</template>

<script setup lang="ts">
import { LucideEye, LucideEyeOff } from 'lucide-vue-next'
import { ref } from 'vue'
defineProps<{
    type: 'text' | 'email' | 'password' | 'number' | 'textarea' | 'file'
    name: string
    label: string
    placeholder?: string
}>()

const showPassword = ref(false)
const togglePassword = () => {
    showPassword.value = !showPassword.value
}
</script>

<style scoped></style>
