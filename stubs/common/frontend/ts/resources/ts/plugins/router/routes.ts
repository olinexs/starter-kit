import type { RouteRecordRaw } from 'vue-router'

export const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('@/layouts/DefaultLayout.vue'),
    children: [
      { path: '', redirect: '/dashboard' },
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('@/pages/dashboard.vue'),
        meta: { requiresAuth: true },
      },
    ],
  },
  {
    path: '/',
    component: () => import('@/layouts/BlankLayout.vue'),
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('@/pages/login.vue'),
        meta: { guestOnly: true },
      },
      {
        path: ':pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/pages/error.vue'),
      },
    ],
  },
]
