import 'resource/css/nprogress.css'
import 'resource/css/app.css'

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import primeVue from 'primevue/config';
import { theme } from './composables/usePrimeSettings';
import StyleClass from 'primevue/styleclass';
import { useInitialized} from '@/composables/useInitialized';
import { createPinia } from 'pinia'
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';
import Ripple from 'primevue/ripple';
import Tooltip from 'primevue/tooltip';

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia();

createInertiaApp({
    // title: (title) => `${title} - ${appName}`,
    title: (title) => title,
    resolve: (name) => {
        return resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue'))
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(primeVue, theme)
            .use(pinia)
            .use(ToastService)
            .use(ConfirmationService)
            .directive('tooltip', Tooltip)
            .directive('styleclass', StyleClass)
            .directive('ripple', Ripple)
            .mount(el);
    },
    progress: {
        // color: '#4B5563',
        // color: '#ff7675',
        includeCSS: false,
        // color: colors.amber['500'],
    },
});

useInitialized();

// This will set light / dark mode on page load...
// initializeTheme();
