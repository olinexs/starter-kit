import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useToastStore = defineStore('toast', () => {
  const snackbar = ref(false)
  const message  = ref('')
  const color    = ref('success')
  const timeout  = ref(3000)

  function show(msg, type = 'success', ms = 3000) {
    message.value  = msg
    color.value    = type
    timeout.value  = ms
    snackbar.value = true
  }

  return {
    snackbar,
    message,
    color,
    timeout,
    show,
    success: (msg) => show(msg, 'success'),
    error:   (msg) => show(msg, 'error'),
    info:    (msg) => show(msg, 'info'),
    warning: (msg) => show(msg, 'warning'),
  }
})
