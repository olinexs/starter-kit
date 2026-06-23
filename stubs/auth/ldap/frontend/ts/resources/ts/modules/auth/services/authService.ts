import { api } from '@/plugins/axios'

export interface LoginCredentials {
  username: string
  password: string
}

// LDAP auth — username + password bound against Active Directory,
// backend issues a Sanctum token on success.
export default {
  login: (credentials: LoginCredentials) => api.post('/auth/login', credentials),
  logout: () => api.post('/auth/logout'),
  me: () => api.get('/auth/me'),
}
