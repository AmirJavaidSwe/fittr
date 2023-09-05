import './bootstrap';
// import "sweetalert2/dist/sweetalert2.css"; //<- seems to be injecting styles to the page, no need to bundle?
import "floating-vue/dist/style.css";
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import AppLayout from './Layouts/AppLayout.vue';
import StoreLayout from './Layouts/StoreLayout.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import directive from './directive';
import FloatingVue from "floating-vue";
import "@splidejs/vue-splide/css/core";


const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));
        page.then((module) => {
            switch (true) {
                // service store layout
                case name.startsWith('Store/'):
                    module.default.layout = StoreLayout;
                    break;

                // below pages don't use layout
                case ['Welcome', 'Onboarding'].includes(name) || name.startsWith('Auth/'):
                    break;

                // admin and partners
                default:
                    module.default.layout = AppLayout;
                    break;
            }
        });
        return page;
    },
    progress: {
        delay: 500,
        color: '#4B5563',
        includeCSS: true,
        showSpinner: false,
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        app.use(ZiggyVue, Ziggy);
        app.component('font-awesome-icon', FontAwesomeIcon);
        app.use(directive); // Register the custom directive here
        app.use(FloatingVue);

        return app.mount(el);
    },
});
