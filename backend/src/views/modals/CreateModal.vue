<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
            </DialogHeader>

            <!-- Your form goes here -->
            <form class="space-y-4" @submit.prevent="$emit('submit', formData)">
                <div
                    v-for="field in fields"
                    :key="field.name"
                    class="grid w-full max-w-sm items-center gap-1.5"
                >
                    <Label :for="field.name">{{ field.label }}</Label>

                    <!-- Password -->
                    <div v-if="field.type === 'password'" class="relative">
                        <Input
                            :id="field.name"
                            :name="field.name"
                            :type="showPassword ? 'text' : 'password'"
                            v-model="formData[field.name]"
                            :placeholder="field.placeholder"
                        />
                        <button
                            type="button"
                            class="absolute right-3 top-2.5"
                            @click="togglePassword"
                        >
                            <component
                                :is="showPassword ? LucideEyeOff : LucideEye"
                                :size="18"
                                class="text-muted-foreground"
                            />
                        </button>
                    </div>

                    <!-- Textarea -->
                    <Textarea
                        v-else-if="field.type === 'textarea'"
                        :id="field.name"
                        :name="field.name"
                        v-model="formData[field.name]"
                        :placeholder="field.placeholder"
                    />

                    <!-- File -->
                    <Input
                        v-else-if="field.type === 'file'"
                        type="file"
                        :id="field.name"
                        @change="formData[field.name] = $event.target.files?.[0]"
                    />

                    <!-- Default inputs -->
                    <Input
                        v-else
                        :type="field.type"
                        :id="field.name"
                        :name="field.name"
                        v-model="formData[field.name]"
                        :placeholder="field.placeholder"
                    />
                </div>
            </form>

            <DialogFooter>
                <Button variant="outline" @click="$emit('update:open', false)"> Cancel </Button>
                <Button> Create </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
<script setup lang="ts">
import { LucideEye, LucideEyeOff } from 'lucide-vue-next'
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import type { FormField } from '@/types/form'

defineProps<{
    open: boolean
    title: string
    fields: FormField[]
}>()

defineEmits<{
    (e: 'update:open', value: boolean): void
    (e: 'submit', formData: Record<string, any>): void
}>()

const formData = ref<Record<string, any>>({})

const showPassword = ref(false)
const togglePassword = () => {
    showPassword.value = !showPassword.value
}
</script>
