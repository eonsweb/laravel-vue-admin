<template>
    <div>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Users</h1>
                <p class="text-sm text-muted-foreground">
                    Manage application users and permissions
                </p>
            </div>

            <Button class="cursor-pointer" @click="isCreateModalOpen = true"> + New User </Button>
        </div>
        <CreateModal
            v-model:open="isCreateModalOpen"
            title="Create New User"
            :fields="userFormFields"
        />
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
// Modals
import CreateModal from '@/views/modals/CreateModal.vue'
import type { FormField } from '@/types/form'

/* -------------------------------------------------
 * USER FORM STATE + MODALS
 * ------------------------------------------------- */
const isCreateModalOpen = ref(false)

const userFormFields: FormField[] = [
    { name: 'name', label: 'Name', type: 'text', placeholder: 'John Doe' },
    { name: 'username', label: 'Username', type: 'text', placeholder: 'johndoe' },
    { name: 'email', label: 'Email', type: 'email' },
    { name: 'bio', label: 'Bio', type: 'textarea', placeholder: 'Enter user bio' },
]
</script>
