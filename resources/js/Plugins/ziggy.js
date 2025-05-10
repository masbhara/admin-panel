import { route } from '@/ziggy';

const ZiggyPlugin = {
    install: (app) => {
        // Mendefinisikan route untuk Options API
        app.config.globalProperties.$route = route;
        
        // Mendefinisikan route untuk Composition API
        app.provide('route', route);
        
        // Mendefinisikan route untuk template
        app.config.globalProperties.route = route;
        
        // Mendefinisikan route sebagai global function
        if (typeof window !== 'undefined') {
            window.route = route;
        }
    }
};

export default ZiggyPlugin;