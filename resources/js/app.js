import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import AppLayout from './Layouts/AppLayout.vue'; 

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core';

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

/* import specific icons */
import { 
    faUserSecret,
    faHome,
    faGaugeHigh,
    faUserTie,
    faGears,
    faCoffee,
    faHeart,
    faPencil,
    faChevronRight,
} from '@fortawesome/free-solid-svg-icons';

/* add icons to the library */
library.add(
    faUserSecret,
    faHome,
    faGaugeHigh,
    faUserTie,
    faGears,
    faCoffee,
    faHeart,
    faPencil,
    faChevronRight,
);

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
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .component('font-awesome-icon', FontAwesomeIcon)
            .mount(el);
    },
});

InertiaProgress.init({ 
    // The delay after which the progress bar will
    // appear during navigation, in milliseconds.
    delay: 500,

    // The color of the progress bar.
    color: '#4B5563',

    // Whether to include the default NProgress styles.
    includeCSS: true,

    // Whether the NProgress spinner will be shown.
    showSpinner: false,
});
