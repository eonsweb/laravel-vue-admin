import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import Components from 'unplugin-vue-components/vite'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
    tailwindcss(),
    Components({
      // 1. Scan these folders for components
      dirs: ['src/components', 'src/views'],

      // 2. File extensions to scan
      extensions: ['vue', 'tsx', 'jsx'],

      // 3. Generate TypeScript declarations
      dts: 'src/components.d.ts',

      // 5. Custom resolvers (only if needed)
      resolvers: [
        // Example: for Element Plus
        // (componentName) => {
        //   if (componentName.startsWith('El'))
        //     return { name: componentName, from: 'element-plus' }
        // }
      ],
    }),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
})
