<script setup lang="ts">
/**
 * NavItems.vue — main navigation menu.
 *
 * After running `php artisan module:make {ModuleName}`, add the module here:
 *   { title: 'Module Title', icon: 'mdi-icon-name', to: '/route-path' }
 *
 * Icon reference: https://pictogrammers.com/library/mdi/
 */

interface NavItem {
  title: string
  icon: string
  to: string
}

const navItems: NavItem[] = [
  { title: 'Dashboard', icon: 'mdi-view-dashboard-outline', to: '/dashboard' },
]
</script>

<template>
  <v-list nav density="compact">
    <v-list-item
      v-for="item in navItems"
      :key="item.title"
      :to="item.to"
      :prepend-icon="item.icon"
      :title="item.title"
      rounded="lg"
      active-color="primary"
    />
  </v-list>
</template>
