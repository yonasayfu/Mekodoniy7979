<script setup lang="js">
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
    Download,
    Folder,
    LayoutGrid,
    ScrollText,
    Shield,
    UserCog,
    Users,
    ClipboardList,
    Globe2,
    MessageCircle,
    Settings,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

defineProps({
    class: {
        type: String,
        default: '',
    },
});

const page = usePage();

const iconMap = {
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
};

const hasPermission = (permission, permissions = []) => {
    if (!permission) {
        return true;
    }

    return permissions.includes(permission);
};

const sidebarGroups = computed(() => {
    const permissions = page.props.auth?.permissions ?? [];
    const groups = page.props.navigation?.sidebar ?? [];

    return groups
        .map((group, index) => {
            const items = (group.items ?? [])
                .filter((item) =>
                    hasPermission(item.permission ?? null, permissions),
                )
                .map((item) => ({
                    ...item,
                    href: item.href === '/dashboard' ? dashboard() : item.href,
                    icon:
                        typeof item.icon === 'string' && iconMap[item.icon]
                            ? iconMap[item.icon]
                            : item.icon,
                }));

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
                        <Link :href="dashboard()">
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
