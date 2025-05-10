import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import ThemePlugin from '@/Plugins/theme';
import ZiggyPlugin from '@/Plugins/ziggy';

// Import semua weight Manrope
import '@fontsource/manrope/200.css';
import '@fontsource/manrope/300.css';
import '@fontsource/manrope/400.css';
import '@fontsource/manrope/500.css';
import '@fontsource/manrope/600.css';
import '@fontsource/manrope/700.css';
import '@fontsource/manrope/800.css';

// Import ThemeProvider
import ThemeProvider from './Components/ThemeProvider.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({
            render: () => h(ThemeProvider, null, {
                default: () => h(App, props)
            })
        });
        
        app.use(plugin)
        app.use(ThemePlugin)
        app.use(ZiggyPlugin)
        app.mount(el)
    },
    progress: {
        color: '#4B5563',
    },
});
