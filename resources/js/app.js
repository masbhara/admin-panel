import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from 'ziggy-js'
import '../css/app.css'
// Import Manrope font
import '@fontsource/manrope';
// Import ThemeProvider
import ThemeProvider from './Components/ThemeProvider.vue';

createInertiaApp({
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({
      render: () => h(ThemeProvider, null, {
        default: () => h(App, props)
      })
    })
    app.use(plugin)
    app.use(ZiggyVue)
    app.mount(el)
  },
})
