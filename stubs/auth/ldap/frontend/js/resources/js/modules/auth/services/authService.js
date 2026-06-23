import { api } from '@/plugins/axios'

// LDAP auth — username + password bound against Active Directory,
// backend issues a Sanctum token on success.
export default {
  login: credentials => api.post('/auth/login', credentials),
  logout: () => api.post('/auth/logout'),
  me: () => api.get('/auth/me'),
}
