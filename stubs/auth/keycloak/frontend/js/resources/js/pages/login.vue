<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/modules/auth/stores/authStore'

const router = useRouter()
const store  = useAuthStore()

const loading = ref(false)
const error   = ref('')

// On return from Keycloak the URL carries ?code & ?state — finish the flow.
onMounted(async () => {
  const params = new URLSearchParams(window.location.search)
  const code   = params.get('code')
  const state  = params.get('state')

  if (!code) return

  loading.value = true
  try {
    await store.completeLogin(code, state)
    // Clean the query string, then enter the app.
    window.history.replaceState({}, '', '/login')
    router.push('/dashboard')
  } catch (e) {
    error.value = e.response?.data?.message ?? e.message ?? 'Login failed'
  } finally {
    loading.value = false
  }
})

async function signIn() {
  error.value = ''
  loading.value = true
  try {
    await store.beginLogin() // redirects away
  } catch (e) {
    error.value = e.message ?? 'Could not start login'
    loading.value = false
  }
}
</script>

<template>
  <VContainer class="fill-height" fluid>
    <VRow justify="center" align="center">
      <VCol cols="12" sm="8" md="4">
        <VCard class="pa-6 text-center">
          <VCardTitle class="mb-4">{PROJECT_NAME}</VCardTitle>

          <template v-if="loading">
            <VProgressCircular indeterminate color="primary" class="my-4" />
            <p class="text-body-2">Signing you in…</p>
          </template>

          <template v-else>
            <VAlert v-if="error" type="error" variant="tonal" class="mb-4">
              {{ error }}
            </VAlert>
            <VBtn block color="primary" prepend-icon="ri-shield-keyhole-line" @click="signIn">
              Sign in with SSO
            </VBtn>
          </template>
        </VCard>
      </VCol>
    </VRow>
  </VContainer>
</template>
