<template>
    <div class="min-h-screen flex items-center justify-center bg-muted/40">
        <Card class="w-full max-w-md p-6 shadow-lg rounded-xl">
            <CardHeader class="space-y-1 text-center">
                <CardTitle class="text-2xl font-semibold">Welcome back</CardTitle>
                <CardDescription>Enter your credentials to continue</CardDescription>
            </CardHeader>

            <form @submit.prevent="onSubmit">
                <CardContent>
                    <p
                        v-if="errors._form"
                        class="text-sm text-destructive text-center mb-4 p-3 bg-destructive/10 border border-destructive/20 rounded-md"
                    >
                        {{ errors._form }}
                    </p>
                    <div class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <div class="relative">
                                <Field name="email" v-slot="{ field }">
                                    <Input
                                        v-bind="field"
                                        id="email"
                                        type="email"
                                        class="pr-10 pl-3 w-full"
                                    />
                                </Field>
                                <LucideMail
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"
                                    :size="20"
                                />
                            </div>
                            <p v-if="errors.email" class="text-sm text-destructive">
                                {{ errors.email }}
                            </p>
                        </div>

                        <!-- Password Input with Eye toggle inside -->
                        <div class="grid gap-2">
                            <Label for="password">Password</Label>
                            <div class="relative flex items-center">
                                <Field name="password" v-slot="{ field }">
                                    <Input
                                        v-bind="field"
                                        :type="showPassword ? 'text' : 'password'"
                                        id="password"
                                        class="pr-10 pl-3 w-full"
                                    />
                                </Field>
                                <button
                                    type="button"
                                    class="absolute right-3"
                                    @click="togglePassword"
                                >
                                    <component
                                        :is="showPassword ? LucideEyeOff : LucideEye"
                                        :size="20"
                                        class="text-gray-400 cursor-pointer"
                                    />
                                </button>
                            </div>

                            <p v-if="errors.password" class="text-sm text-destructive">
                                {{ errors.password }}
                            </p>
                        </div>
                    </div>
                </CardContent>

                <CardFooter>
                    <Button
                        class="w-full my-7"
                        type="submit"
                        :disabled="isSubmitting || auth.isLoading"
                    >
                        <span v-if="!isSubmitting && !auth.isLoading">Login</span>
                        <div v-else class="flex items-center">
                            <LoaderCircle class="animate-spin mr-2" :size="20" />
                            Logging in...
                        </div>
                    </Button>
                </CardFooter>
            </form>
        </Card>
    </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { LucideMail, LucideEye, LucideEyeOff, LoaderCircle, Loader } from 'lucide-vue-next'

import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

import { useForm, Field } from 'vee-validate'
import { loginSchema, type LoginForm } from '@/schemas/login.shema'
import { toTypedSchema } from '@vee-validate/zod'
import type { AuthErrorResponse } from '@/types'

const auth = useAuthStore()
const router = useRouter()

const showPassword = ref(false)

const togglePassword = () => {
    showPassword.value = !showPassword.value
}

const { handleSubmit, errors, isSubmitting, setErrors, setFieldError } = useForm<LoginForm>({
    validationSchema: toTypedSchema(loginSchema),
    initialValues: {
        email: '',
        password: '',
    },
})
const onSubmit = handleSubmit(async (values) => {
    try {
        await auth.login(values.email, values.password)
        router.push({ name: 'dashboard' })
    } catch (error: any) {
        const err = error as AuthErrorResponse

        if (err.status === 401) {
            setErrors({
                _form: error.message,
            })
            return
        }
        setErrors({
            _form: 'Something went wrong. Please try again.',
        })
    }
})
</script>
