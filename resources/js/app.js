import './bootstrap';
import '../css/app.css';
import 'floating-vue/dist/style.css'
import '@css/overrideTooltip.css'

import {createApp, h} from 'vue';
import {createInertiaApp} from '@inertiajs/inertia-vue3';
import {InertiaProgress} from '@inertiajs/progress';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {ZiggyVue} from '../../vendor/tightenco/ziggy/dist/vue.m';
import {VTooltip} from 'floating-vue'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Inertia} from "@inertiajs/inertia";

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Mixpost';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: name => {
        const page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));

        page.then((module) => {
            module.default.layout = module.default.layout || AuthenticatedLayout;
        });

        return page;
    },
    setup({el, app, props, plugin}) {
        return createApp({render: () => h(app, props)})
            .use(plugin)
            .directive('tooltip', VTooltip)
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
});

InertiaProgress.init({color: '#4F46BB'});

// Refresh page on history operation
let stale = false;

window.addEventListener('popstate', () => {
    stale = true;
});

Inertia.on('navigate', (event) => {
    const page = event.detail.page;

    if (stale) {
        Inertia.get(page.url, {}, { replace: true, preserveScroll: true, preserveState: false });
    }

    stale = false;
});
