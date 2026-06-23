import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useToastStore = defineStore('toast', () => {
  const toasts = ref([])

  function show(message, type = 'success', timeout = 4000) {
    const id = Date.now()
    toasts.value.push({ id, message, type })
    setTimeout(() => dismiss(id), timeout)
  }

  function dismiss(id) {
    toasts.value = toasts.value.filter(t => t.id !== id)
  }

  function success(msg) { show(msg, 'success') }
  function error(msg)   { show(msg, 'error') }
  function info(msg)    { show(msg, 'info') }
  function warn(msg)    { show(msg, 'warning') }

  return { toasts, show, dismiss, success, error, info, warn }
})
