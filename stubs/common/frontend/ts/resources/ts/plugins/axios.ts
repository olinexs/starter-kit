import type { App } from 'vue'
import type { AxiosInstance, InternalAxiosRequestConfig } from 'axios'
import axios from 'axios'

const api: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  withCredentials: true,
  headers: { Accept: 'application/json' },
})

api.interceptors.request.use((config: InternalAxiosRequestConfig) => {
  const token = localStorage.getItem('auth_token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

api.interceptors.response.use(
  res => res,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  },
)

export default function (app: App) {
  app.config.globalProperties.$api = api
}

export { api }
