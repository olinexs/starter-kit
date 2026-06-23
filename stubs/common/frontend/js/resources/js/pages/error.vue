<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()
</script>

<template>
  <VContainer class="fill-height" fluid>
    <VRow justify="center" align="center">
      <VCol cols="12" class="text-center">
        <h1 class="text-h2 mb-2">404</h1>
        <p class="text-body-1 mb-6">Page not found.</p>
        <VBtn color="primary" @click="router.push('/')">Go home</VBtn>
      </VCol>
    </VRow>
  </VContainer>
</template>
