import { api } from '@/plugins/axios'

export interface LoginCredentials {
  email: string
  password: string
}

// Local auth — email + password against Sanctum.
export default {
  login: (credentials: LoginCredentials) => api.post('/auth/login', credentials),
  logout: () => api.post('/auth/logout'),
  me: () => api.get('/auth/me'),
}
