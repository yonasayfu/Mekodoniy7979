<script setup lang="ts">
import MetricCard from '@/components/dashboard/MetricCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useRoute } from '@/composables/useRoute';
import { dashboard } from '@/routes';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    CalendarClock,
    ClipboardList,
    Download as DownloadIcon,
    Megaphone,
    ShieldCheck,
    UserCheck,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';

type Metric = {
    label: string;
    value: number | string;
    description?: string;
    change?: {
        direction: 'up' | 'down' | 'flat';
        percentage: number;
        label?: string;
    } | null;
    icon?: string;
    href?: string; // Added href prop
};

type QuickLink = {
    label: string;
    description: string;
    href: string;
    icon: typeof Users;
    tone: string;
};

const route = useRoute();

const props = defineProps<{
    metrics: Metric[];
}>();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const iconRegistry = {
    Users,
    UserCheck,
    ShieldCheck,
    Download: DownloadIcon,
} as const;

const quickLinks = computed<QuickLink[]>(() => [
    {
        label: 'Manage Elders',
        description: 'Update profiles, health notes, and priority flags.',
        href: route('elders.index', undefined, false),
        icon: Users,
        tone: 'text-blue-600 dark:text-blue-300',
    },
    {
        label: 'Review Sponsorships',
        description: 'Approve matches and monitor commitments.',
        href: route('sponsorships.index', undefined, false),
        icon: ClipboardList,
        tone: 'text-rose-600 dark:text-rose-300',
    },
    {
        label: 'Plan Visits',
        description: 'Confirm upcoming visits and send reminders.',
        href: route('visits.index', undefined, false),
        icon: CalendarClock,
        tone: 'text-emerald-600 dark:text-emerald-300',
    },
    {
        label: 'Campaign Builder',
        description: 'Launch regional drives and track momentum.',
        href: route('campaigns.index', undefined, false),
        icon: Megaphone,
        tone: 'text-purple-600 dark:text-purple-300',
    },
    {
        label: 'Team & Roles',
        description: 'Invite staff and assign permissions safely.',
        href: route('staff.index', undefined, false),
        icon: ShieldCheck,
        tone: 'text-amber-600 dark:text-amber-300',
    },
    {
        label: 'Reports Hub',
        description: 'View performance, impact, and cash flow.',
        href: route('reports.index', undefined, false),
        icon: DownloadIcon,
        tone: 'text-slate-600 dark:text-slate-300',
    },
]);

const suppressedMetrics = ['Team Growth'];

const resolvedMetrics = computed(() =>
    (props.metrics ?? [])
        .filter((metric) => !suppressedMetrics.includes(metric.label))
        .map((metric) => ({
            ...metric,
            icon: metric.icon
                ? (iconRegistry[metric.icon as keyof typeof iconRegistry] ?? Users)
                : null,
        })),
);

const highlightLabels = new Set([
    'Total Elders',
    'Total Sponsorships',
    'Active Sponsorships',
]);

const highlightCards = computed(() =>
    resolvedMetrics.value
        .filter((metric) => highlightLabels.has(metric.label))
        .map((metric) => ({
            label: metric.label,
            value: metric.value,
            description: metric.description,
            icon: metric.icon,
            href: metric.href,
        })),
);

const metricCards = computed(() =>
    resolvedMetrics.value.filter(
        (metric) => !highlightLabels.has(metric.label),
    ),
);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 px-4 py-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <MetricCard
                    v-for="metric in metricCards"
                    :key="metric.label"
                    :label="metric.label"
                    :value="metric.value"
                    :description="metric.description"
                    :change="metric.change"
                    :icon="metric.icon ?? undefined"
                    :href="metric.href ?? undefined"
                />
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <Link
                    v-for="link in quickLinks"
                    :key="link.href"
                    :href="link.href"
                    class="flex h-full flex-col rounded-2xl border border-slate-200/60 bg-white/80 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-slate-800/50 dark:bg-slate-900/60"
                >
                    <div
                        class="inline-flex size-10 items-center justify-center rounded-xl bg-slate-100/80 dark:bg-slate-800/80"
                    >
                        <component :is="link.icon" class="size-5" :class="link.tone" />
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">
                        {{ link.label }}
                    </h3>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                        {{ link.description }}
                    </p>
                </Link>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <Link
                    v-for="card in highlightCards"
                    :key="card.label"
                    :href="card.href ?? '#'"
                    class="group flex h-full flex-col justify-between rounded-2xl border border-slate-200/70 bg-white/80 p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-slate-800/60 dark:bg-slate-900/60"
                >
                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        >
                            {{ card.label }}
                        </p>
                        <p
                            class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white"
                        >
                            {{ card.value }}
                        </p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                            {{ card.description ?? 'View the full list for more detail.' }}
                        </p>
                    </div>

                    <div
                        class="mt-6 flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-slate-500 transition group-hover:text-slate-900 dark:text-slate-400 dark:group-hover:text-white"
                    >
                        <span>Open list</span>
                        <div
                            v-if="card.icon"
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-500 dark:bg-indigo-500/20"
                        >
                            <component :is="card.icon" class="size-4" />
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
