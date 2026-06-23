<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const drawer = ref(true)

// Minimal nav — extend or replace with your module nav items.
const navItems = [
  { title: 'Dashboard', icon: 'ri-dashboard-line', to: '/dashboard' },
]

function logout() {
  localStorage.removeItem('auth_token')
  router.push('/login')
}
</script>

<template>
  <VApp>
    <VNavigationDrawer v-model="drawer" :width="260">
      <div class="pa-4 text-h6">{PROJECT_NAME}</div>
      <VDivider />
      <VList nav density="comfortable">
        <VListItem
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          :prepend-icon="item.icon"
          :title="item.title"
        />
      </VList>
    </VNavigationDrawer>

    <VAppBar flat border>
      <VAppBarNavIcon @click="drawer = !drawer" />
      <VSpacer />
      <VBtn variant="text" prepend-icon="ri-logout-box-line" @click="logout">
        Logout
      </VBtn>
    </VAppBar>

    <VMain>
      <VContainer fluid>
        <RouterView />
      </VContainer>
    </VMain>
  </VApp>
</template>
