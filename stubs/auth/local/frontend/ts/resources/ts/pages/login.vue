<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/modules/auth/stores/authStore'

const router = useRouter()
const store  = useAuthStore()

const form    = ref({ email: '', password: '' })
const loading = ref(false)
const error   = ref('')

async function submit() {
  loading.value = true
  error.value = ''
  try {
    await store.login(form.value)
    router.push('/dashboard')
  } catch (e: any) {
    error.value = e.response?.data?.message ?? 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <VContainer class="fill-height" fluid>
    <VRow justify="center" align="center">
      <VCol cols="12" sm="8" md="4">
        <VCard class="pa-6">
          <VCardTitle class="text-center mb-4">{PROJECT_NAME}</VCardTitle>
          <VForm @submit.prevent="submit">
            <VTextField
              v-model="form.email"
              label="Email"
              type="email"
              class="mb-3"
              autofocus
            />
            <VTextField
              v-model="form.password"
              label="Password"
              type="password"
              class="mb-3"
            />
            <VAlert v-if="error" type="error" variant="tonal" class="mb-3">
              {{ error }}
            </VAlert>
            <VBtn type="submit" block color="primary" :loading="loading">
              Sign in
            </VBtn>
          </VForm>
        </VCard>
      </VCol>
    </VRow>
  </VContainer>
</template>
