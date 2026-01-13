<template>
  <div class="min-h-screen flex items-center justify-center bg-muted/40">
    <Card class="w-full max-w-md p-6 shadow-lg rounded-xl">
      <CardHeader class="space-y-1 text-center">
        <CardTitle class="text-2xl font-semibold">Welcome back</CardTitle>
        <CardDescription>Enter your credentials to continue</CardDescription>
      </CardHeader>

      <CardContent>
        <div class="space-y-4">
          <div class="grid gap-2">
            <Label for="email">Email</Label>
            <div class="relative">
              <Input
                id="email"
                type="email"
                v-model="email"
                placeholder=""
                class="pr-10 pl-3 w-full"
              />
              <LucideMail
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"
                size="20"
              />
            </div>
          </div>

          <!-- Password Input with Eye toggle inside -->
          <div class="grid gap-2">
            <Label for="password">Password</Label>
            <div class="relative flex items-center">
              <Input
                id="password"
                :type="showPassword ? 'text' : 'password'"
                v-model="password"
                placeholder=""
                class="pr-10 pl-3 w-full"
              />
              <button type="button" class="absolute right-3" @click="togglePassword">
                <component
                  :is="showPassword ? LucideEyeOff : LucideEye"
                  size="20"
                  class="text-gray-400 cursor-pointer"
                />
              </button>
            </div>
          </div>
        </div>
      </CardContent>

      <CardFooter>
        <Button class="w-full my-7" @click="handleLogin" :disabled="auth.isLoading">
          <span v-if="!auth.isLoading">Login</span>
          <div v-else class="flex items-center">
            <LoaderCircle  class="animate-spin mr-2" size="20" />
            Logging in...
          </div>
          
        </Button>
      </CardFooter>
    </Card>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { LucideMail, LucideEye, LucideEyeOff,LoaderCircle, Loader } from 'lucide-vue-next'

import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

const email = ref('')
const password = ref('')
const showPassword = ref(false)

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const handleLogin = async () => {
  try {
    await auth.login(email.value, password.value)
    router.push({ name: 'dashboard' })
  } catch (error: any) {
    alert('Login failed. Check your credentials.')
  }
}
</script>
