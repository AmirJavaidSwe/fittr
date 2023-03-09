import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import AppLayout from './Layouts/AppLayout.vue'; 

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: name => {
        const page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));
        // set persistent default page layout, except for home and auth pages (AppLayout depends on logged in user)
        page.then((module) => {
            if ( name != 'Welcome' && !name.startsWith('Auth/') ) {
                module.default.layout = module.default.layout || AppLayout;
            }
          });
        return page;
    },
    progress: { 
        // The delay after which the progress bar will
        // appear during navigation, in milliseconds.
        delay: 500,
    
        // The color of the progress bar.
        color: '#4B5563',
    
        // Whether to include the default NProgress styles.
        includeCSS: true,
    
        // Whether the NProgress spinner will be shown.
        showSpinner: false,
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .component('font-awesome-icon', FontAwesomeIcon)
            .mount(el);
    },
});
