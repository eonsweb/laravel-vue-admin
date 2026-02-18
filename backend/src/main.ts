import './assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import VueQueryPlugin, { vueQueryPluginOptions } from './lib/VueQuery'

import router from './router'
import App from './App.vue'

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(VueQueryPlugin, vueQueryPluginOptions)
app.mount('#app')
