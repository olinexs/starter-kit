import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import { createVuetify } from 'vuetify'
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

import App from './App.vue'
import { routes } from './plugins/router/routes'
import { vuetifyConfig } from './plugins/vuetify'

const pinia  = createPinia()
const router = createRouter({ history: createWebHistory(), routes })
const vuetify = createVuetify(vuetifyConfig)

createApp(App)
  .use(pinia)
  .use(router)
  .use(vuetify)
  .mount('#app')
