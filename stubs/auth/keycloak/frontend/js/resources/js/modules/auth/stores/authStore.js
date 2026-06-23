import { defineStore } from 'pinia'
import { ref } from 'vue'
import authService from '../services/authService'

export const useAuthStore = defineStore('auth', () => {
  const user  = ref(null)
  const token = ref(localStorage.getItem('auth_token'))

  function setSession(data) {
    token.value = data.token
    user.value  = data.data
    localStorage.setItem('auth_token', data.token)
  }

  function clearSession() {
    token.value = null
    user.value  = null
    localStorage.removeItem('auth_token')
  }

  // Kick off the Keycloak redirect.
  function beginLogin() {
    return authService.beginLogin()
  }

  // Handle the redirect back: exchange code → access_token → Sanctum token.
  async function completeLogin(code, state) {
    const accessToken = await authService.exchangeCode(code, state)
    const { data } = await authService.loginWithToken(accessToken)
    setSession(data)
    return data
  }

  async function logout() {
    try { await authService.logout() } catch { /* token may already be invalid */ }
    clearSession()
  }

  async function fetchMe() {
    const { data } = await authService.me()
    user.value = data.data
    return user.value
  }

  return { user, token, beginLogin, completeLogin, logout, fetchMe }
})
