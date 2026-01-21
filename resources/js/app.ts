import '../css/app.css';

import { drainQueue } from '@/lib/offlineQueue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h, ref } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { Ziggy } from './ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const isOnline = ref<boolean>(navigator.onLine);

window.addEventListener('online', () => {
    isOnline.value = true;
    window.dispatchEvent(new CustomEvent('app:online'));
    drainQueue().catch(() => {
        // keep queue for next attempt
    });
});

window.addEventListener('offline', () => {
    isOnline.value = false;
    window.dispatchEvent(new CustomEvent('app:offline'));
});

function initializeTheme() {
    const theme = localStorage.getItem('theme') || 'system';
    if (
        theme === 'dark' ||
        (theme === 'system' &&
            window.matchMedia('(prefers-color-scheme: dark)').matches)
    ) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy);

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

declare global {
    interface Window {
        __APP_ONLINE__?: typeof isOnline;
    }
}

window.__APP_ONLINE__ = isOnline;
