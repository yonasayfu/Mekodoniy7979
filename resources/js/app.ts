import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import * as routes from './routes';
import * as activityLogsRoutes from './routes/activity-logs';
import * as apiRoutes from './routes/api';
import * as appearanceRoutes from './routes/appearance';
import * as branchesRoutes from './routes/branches';
import * as campaignsRoutes from './routes/campaigns';
import * as donationsRoutes from './routes/donations';
import * as donorsRoutes from './routes/donors';
import * as eldersRoutes from './routes/elders';
import * as exportsRoutes from './routes/exports';
import * as guestRoutes from './routes/guest';
import * as loginRoutes from './routes/login';
import * as mailboxRoutes from './routes/mailbox';
import * as mailpitRoutes from './routes/mailpit';
import * as notificationsRoutes from './routes/notifications';
import * as onboardingRoutes from './routes/onboarding';
import * as passwordRoutes from './routes/password';
import * as profileRoutes from './routes/profile';
import * as registerRoutes from './routes/register';
import * as reportsRoutes from './routes/reports';
import * as rolesRoutes from './routes/roles';
import * as samplesRoutes from './routes/samples';
import * as sanctumRoutes from './routes/sanctum';
import * as sponsorshipsRoutes from './routes/sponsorships';
import * as staffRoutes from './routes/staff';
import * as storageRoutes from './routes/storage';
import * as twoFactorRoutes from './routes/two-factor';
import * as twoFactorEmailRecoveryRoutes from './routes/two-factor-email-recovery';
import * as usersRoutes from './routes/users';
import * as verificationRoutes from './routes/verification';
import * as visitsRoutes from './routes/visits';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Create a global route function similar to Ziggy
declare global {
    function route(name: string, parameters?: any, options?: any): string;
}

globalThis.route = (name: string, parameters?: any, options?: any) => {
    // Handle the special case for simple route names
    if (name === 'dashboard') {
        return routes.dashboard.url(parameters);
    }
    if (name === 'home') {
        return routes.home.url(parameters);
    }
    
    const routeParts = name.split('.');
    let routeObj: any = {
        routes,
        'activity-logs': activityLogsRoutes,
        api: apiRoutes,
        appearance: appearanceRoutes,
        branches: branchesRoutes,
        campaigns: campaignsRoutes,
        donations: donationsRoutes,
        donors: donorsRoutes,
        elders: eldersRoutes,
        exports: exportsRoutes,
        guest: guestRoutes,
        login: loginRoutes,
        mailbox: mailboxRoutes,
        mailpit: mailpitRoutes,
        notifications: notificationsRoutes,
        onboarding: onboardingRoutes,
        password: passwordRoutes,
        profile: profileRoutes,
        register: registerRoutes,
        reports: reportsRoutes,
        roles: rolesRoutes,
        samples: samplesRoutes,
        sanctum: sanctumRoutes,
        sponsorships: sponsorshipsRoutes,
        staff: staffRoutes,
        storage: storageRoutes,
        'two-factor': twoFactorRoutes,
        'two-factor-email-recovery': twoFactorEmailRecoveryRoutes,
        users: usersRoutes,
        verification: verificationRoutes,
        visits: visitsRoutes,
    };

    for (const part of routeParts) {
        if (routeObj && typeof routeObj === 'object' && part in routeObj) {
            routeObj = routeObj[part];
        } else {
            throw new Error(`Route '${name}' not found`);
        }
    }

    if (!routeObj || typeof routeObj.url !== 'function') {
        throw new Error(`Route '${name}' not found or invalid`);
    }

    return routeObj.url(parameters);
};

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
        const app = createApp({ render: () => h(App, props) }).use(plugin);

        // Make route function globally available
        app.config.globalProperties.route = globalThis.route;
        window.route = globalThis.route;

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
