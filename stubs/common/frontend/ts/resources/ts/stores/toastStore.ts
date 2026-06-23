import { defineStore } from 'pinia'
import { ref } from 'vue'

type ToastType = 'success' | 'error' | 'info' | 'warning'

interface Toast {
  id: number
  message: string
  type: ToastType
}

export const useToastStore = defineStore('toast', () => {
  const toasts = ref<Toast[]>([])

  function show(message: string, type: ToastType = 'success', timeout = 4000) {
    const id = Date.now()
    toasts.value.push({ id, message, type })
    setTimeout(() => dismiss(id), timeout)
  }

  function dismiss(id: number) {
    toasts.value = toasts.value.filter(t => t.id !== id)
  }

  function success(msg: string) { show(msg, 'success') }
  function error(msg: string)   { show(msg, 'error') }
  function info(msg: string)    { show(msg, 'info') }
  function warn(msg: string)    { show(msg, 'warning') }

  return { toasts, show, dismiss, success, error, info, warn }
})
