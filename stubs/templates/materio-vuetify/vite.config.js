import { fileURLToPath } from 'node:url'
import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { defineConfig } from 'vite'
import vuetify from 'vite-plugin-vuetify'
import svgLoader from 'vite-svg-loader'

// Paths remapped: src/ → resources/js/
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
      styles: { configFile: 'resources/js/assets/styles/variables/_vuetify.scss' },
    }),
    Components({
      dirs: ['resources/js/@core/components', 'resources/js/components'],
      dts: true,
    }),
    AutoImport({
      imports: ['vue', '@vueuse/core', '@vueuse/math', 'pinia'],
      dirs: [
        './resources/js/@core/utils',
        './resources/js/@core/composable/',
        './resources/js/composables/',
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
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
      '@themeConfig': fileURLToPath(new URL('./themeConfig.js', import.meta.url)),
      '@core': fileURLToPath(new URL('./resources/js/@core', import.meta.url)),
      '@layouts': fileURLToPath(new URL('./resources/js/@layouts', import.meta.url)),
      '@images': fileURLToPath(new URL('./resources/js/assets/images/', import.meta.url)),
      '@styles': fileURLToPath(new URL('./resources/js/assets/styles/', import.meta.url)),
      '@configured-variables': fileURLToPath(new URL('./resources/js/assets/styles/variables/_template.scss', import.meta.url)),
    },
  },
  build: { chunkSizeWarningLimit: 5000 },
  optimizeDeps: {
    exclude: ['vuetify'],
    entries: ['./resources/js/**/*.vue'],
  },
  server: {
    port: 5173,
    proxy: {
      '/api': { target: 'http://localhost:8000', changeOrigin: true },
    },
  },
})
