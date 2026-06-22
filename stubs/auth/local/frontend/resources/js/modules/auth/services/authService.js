import { api } from '@/plugins/axios'

// Local auth — email + password against Sanctum.
export default {
  login: credentials => api.post('/auth/login', credentials),
  logout: () => api.post('/auth/logout'),
  me: () => api.get('/auth/me'),
}
