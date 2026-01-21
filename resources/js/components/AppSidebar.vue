<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    ClipboardList,
    DollarSign,
    Download,
    Folder,
    Globe2,
    Heart,
    LayoutGrid,
    MessageCircle,
    ScrollText,
    Settings,
    Shield,
    UserCog,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

withDefaults(
    defineProps<{
        class?: string;
    }>(),
    {
        class: '',
    },
);

const page = usePage();

const iconMap: Record<string, any> = {
    LayoutGrid,
    Download,
    ScrollText,
    Users,
    UserCog,
    Shield,
    Folder,
    BookOpen,
    ClipboardList,
    Globe2,
    MessageCircle,
    Settings,
    DollarSign,
    Heart,
};

const hasPermission = (
    permission: string | null,
    permissions: string[] = [],
) => {
    if (!permission) {
        return true;
    }

    if (permissions.includes(permission)) {
        return true;
    }

    return permissions.some((p) => {
        if (p === '*') return true;
        if (p.endsWith('.*')) {
            const prefix = p.slice(0, -2);
            return permission.startsWith(`${prefix}.`);
        }
        return false;
    });
};

const currentUrl = computed(() => page.url ?? '/');

const normalizePath = (value: string) => {
    if (! value) {
        return '/';
    }

    if (! value.startsWith('/')) {
        return value;
    }

    return value.replace(/\/+$/, '') || '/';
};

const resolveHref = (href: string | null | undefined) => {
    if (! href) return '#';

    if (href === '/dashboard') {
        return dashboard().url ?? '/dashboard';
    }

    return href;
};

const isActive = (href: string) => {
    const target = normalizePath(href);
    const current = normalizePath(currentUrl.value);

    if (target === '/') {
        return current === '/';
    }

    return current === target || current.startsWith(`${target}/`);
};

const sidebarGroups = computed(() => {
    const permissions = page.props.auth?.permissions ?? [];
    const roles = page.props.auth?.roles ?? [];
    let groups = page.props.navigation?.sidebar ?? [];

    if (roles.includes('External') || roles.includes('Donor')) {
        groups = [
            {
                id: 'donor-quick',
                label: 'My Giving',
                items: [
                    {
                        title: 'Donor Dashboard',
                        href: '/donors/dashboard',
                        icon: 'Heart',
                        permission: null,
                    },
                    {
                        title: 'Impact Reports',
                        href: '/reports',
                        icon: 'ScrollText',
                        permission: null,
                    },
                    {
                        title: 'Make a Donation',
                        href: '/guest-donation',
                        icon: 'DollarSign',
                        permission: null,
                    },
                ],
            },
            {
                id: 'support',
                label: 'Support',
                items: [
                    {
                        title: 'Contact Team',
                        href: '/notifications',
                        icon: 'MessageCircle',
                        permission: null,
                    },
                    {
                        title: 'Outbound Log',
                        href: '/outbound',
                        icon: 'ClipboardList',
                        permission: 'notifications.view',
                    },
                ],
            },
        ];
    }

    return groups
        .map((group, index) => {
            const items = (group.items ?? [])
                .filter((item) =>
                    hasPermission(item.permission ?? null, permissions),
                )
                .map((item) => {
                    const href = resolveHref(item.href);

                    return {
                        ...item,
                        href,
                        active: isActive(href),
                        icon:
                            typeof item.icon === 'string' && iconMap[item.icon]
                                ? iconMap[item.icon]
                                : item.icon,
                    };
                });

            if (!items.length) {
                return null;
            }

            return {
                id: group.label ?? `group-${index}`,
                label: group.label ?? null,
                icon:
                    typeof group.icon === 'string' &&
                    iconMap[group.icon]
                        ? iconMap[group.icon]
                        : group.icon,
                items,
            };
        })
        .filter(Boolean);
});
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="inset"
        class="app-sidebar"
        :class="class"
    >
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard().url ?? '/dashboard'">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :groups="sidebarGroups" />
        </SidebarContent>
    </Sidebar>
</template>
