/**
 * Root router — import and spread module routes here after running:
 *   php artisan module:make {ModuleName}
 *
 * Example:
 *   import authRoutes from '@/modules/auth/routes'
 *   export const routes = [...authRoutes, ...otherRoutes]
 */

export const routes = [
  {
    path: '/',
    redirect: '/dashboard',
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFound.vue'),
  },
]
