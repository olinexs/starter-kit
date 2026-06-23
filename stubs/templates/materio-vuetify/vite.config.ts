import { fileURLToPath } from 'node:url'
import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { defineConfig } from 'vite'
import vuetify from 'vite-plugin-vuetify'
import svgLoader from 'vite-svg-loader'

// Paths remapped: src/ → resources/ts/
export default defineConfig({
  plugins: [
    vue({
      template: {
        compilerOptions: {
          isCustomElement: tag => tag === 'swiper-container' || tag === 'swiper-slide',
        },
      },
    }),
    vueJsx(),
    vuetify({
      styles: { configFile: 'resources/ts/assets/styles/variables/_vuetify.scss' },
    }),
    Components({
      dirs: ['resources/ts/@core/components', 'resources/ts/components'],
      dts: true,
    }),
    AutoImport({
      imports: ['vue', '@vueuse/core', '@vueuse/math', 'pinia'],
      dirs: [
        './resources/ts/@core/utils',
        './resources/ts/@core/composable/',
        './resources/ts/composables/',
      ],
      vueTemplate: true,
      ignore: ['useCookies', 'useStorage'],
      eslintrc: { enabled: true, filepath: './.eslintrc-auto-import.json' },
    }),
    svgLoader(),
  ],
  define: { 'process.env': {} },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/ts', import.meta.url)),
      '@themeConfig': fileURLToPath(new URL('./themeConfig', import.meta.url)),
      '@core': fileURLToPath(new URL('./resources/ts/@core', import.meta.url)),
      '@layouts': fileURLToPath(new URL('./resources/ts/@layouts', import.meta.url)),
      '@images': fileURLToPath(new URL('./resources/ts/assets/images/', import.meta.url)),
      '@styles': fileURLToPath(new URL('./resources/ts/assets/styles/', import.meta.url)),
      '@configured-variables': fileURLToPath(new URL('./resources/ts/assets/styles/variables/_template.scss', import.meta.url)),
    },
  },
  build: { chunkSizeWarningLimit: 5000 },
  optimizeDeps: {
    exclude: ['vuetify'],
    entries: ['./resources/ts/**/*.vue'],
  },
  server: {
    port: 5173,
    proxy: {
      '/api': { target: 'http://localhost:8000', changeOrigin: true },
    },
  },
})
