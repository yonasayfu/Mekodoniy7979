<script setup lang="ts">
import ActivityTimeline from '@/components/ActivityTimeline.vue'; // Import ActivityTimeline
import GlassCard from '@/components/GlassCard.vue';
import MetricCard from '@/components/dashboard/MetricCard.vue';
import { useRoute } from '@/composables/useRoute';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItemType } from '@/types';
import { Head } from '@inertiajs/vue3';
import {
    CalendarCheck,
    DollarSign,
    Gift,
    HeartHandshake,
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
};

interface ElderSummary {
    id: number;
    first_name: string;
    last_name: string;
    profile_photo_url: string;
    priority_level: 'low' | 'medium' | 'high';
}

interface TimelineEntry {
    // Define TimelineEntry type
    id: number | string;
    action: string;
    description?: string | null;
    causer?: {
        id: number | string | null;
        name?: string | null;
    } | null;
    changes?: {
        before?: Record<string, unknown> | null;
        after?: Record<string, unknown> | null;
    } | null;
    occurred_at: string;
    occurred_at_for_humans?: string | null;
}

const props = defineProps<{
    metrics: Metric[];
    myElders: ElderSummary[];
    recentActivity: Array<{
        id: number | string;
        description: string | null;
        action: string | null;
        causer: string | null;
        occurred_at: string | null;
    }>;
    timelineEvents: TimelineEntry[]; // New prop for timeline events
}>();

const route = useRoute();

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Donor Dashboard',
        href: dashboard().url, // This should be updated to donors.dashboard later
    },
];

const iconRegistry = {
    Gift,
    HeartHandshake,
    CalendarCheck,
    DollarSign,
} as const;

const resolvedMetrics = computed(() =>
    (props.metrics ?? []).map((metric) => ({
        ...metric,
        icon: metric.icon
            ? (iconRegistry[metric.icon as keyof typeof iconRegistry] ?? Gift)
            : null,
    })),
);

const trafficLightClass = (priority: string) => {
    switch (priority) {
        case 'high':
            return 'bg-red-500';
        case 'medium':
            return 'bg-yellow-500';
        case 'low':
            return 'bg-green-500';
        default:
            return 'bg-gray-500';
    }
};
</script>

<template>
    <Head title="Donor Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 px-4 py-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <MetricCard
                    v-for="metric in resolvedMetrics"
                    :key="metric.label"
                    :label="metric.label"
                    :value="metric.value"
                    :description="metric.description"
                    :change="metric.change"
                    :icon="metric.icon ?? undefined"
                />
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <GlassCard variant="lite" padding="p-0" class="lg:col-span-2">
                    <div
                        class="border-b border-slate-200/70 px-5 py-4 dark:border-slate-800/60"
                    >
                        <div
                            class="flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-100"
                        >
                            <HeartHandshake class="size-5 text-indigo-500" />
                            My Supported Elders
                        </div>
                    </div>
                    <div
                        class="divide-y divide-slate-200/70 dark:divide-slate-800/60"
                    >
                        <div
                            v-for="elder in myElders"
                            :key="elder.id"
                            class="flex items-center gap-3 px-5 py-4 text-sm text-slate-600 dark:text-slate-300"
                        >
                            <div
                                class="relative h-10 w-10 overflow-hidden rounded-full"
                            >
                                <img
                                    :src="elder.profile_photo_url"
                                    :alt="`${elder.first_name} ${elder.last_name}`"
                                    class="h-full w-full object-cover"
                                />
                                <span
                                    class="absolute right-0 bottom-0 h-3 w-3 rounded-full border border-white dark:border-slate-800"
                                    :class="
                                        trafficLightClass(elder.priority_level)
                                    "
                                ></span>
                            </div>
                            <div class="flex-1">
                                <p
                                    class="font-semibold text-slate-800 dark:text-slate-100"
                                >
                                    {{ elder.first_name }} {{ elder.last_name }}
                                </p>
                                <p
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
                                    Priority: {{ elder.priority_level }}
                                </p>
                            </div>
                            <div
                                class="flex flex-wrap items-center gap-2 text-xs"
                            >
                                <Link
                                    :href="route('elders.public.show', elder.id, false)"
                                    class="text-indigo-600 hover:underline"
                                    >View Profile</Link
                                >
                                <GlassButton size="sm" variant="secondary"
                                    >Pay Now</GlassButton
                                >
                                <GlassButton size="sm" variant="secondary"
                                    >Schedule Visit</GlassButton
                                >
                            </div>
                        </div>
                        <div
                            v-if="!myElders.length"
                            class="px-5 py-6 text-sm text-slate-500 dark:text-slate-400"
                        >
                            You are not currently supporting any elders.
                        </div>
                    </div>
                </GlassCard>

                <GlassCard variant="lite" padding="p-0">
                    <div
                        class="border-b border-slate-200/70 px-5 py-4 text-sm font-semibold text-slate-800 dark:border-slate-800/60 dark:text-slate-100"
                    >
                        Quick Actions
                    </div>
                    <div
                        class="divide-y divide-slate-200/70 dark:divide-slate-800/60"
                    >
                        <div
                            class="flex items-center justify-between gap-3 px-5 py-4 text-sm"
                        >
                            <p
                                class="font-medium text-slate-800 dark:text-slate-100"
                            >
                                Make a One-Time Donation
                            </p>
                            <Link :href="route('guest.donation')">
                                <GlassButton size="sm" variant="primary"
                                    >Donate Now</GlassButton
                                >
                            </Link>
                        </div>
                        <div
                            class="flex items-center justify-between gap-3 px-5 py-4 text-sm"
                        >
                            <p
                                class="font-medium text-slate-800 dark:text-slate-100"
                            >
                                Manage Recurring Donations
                            </p>
                            <GlassButton size="sm" variant="secondary"
                                >View Subscriptions</GlassButton
                            >
                        </div>
                        <div
                            class="flex items-center justify-between gap-3 px-5 py-4 text-sm"
                        >
                            <p
                                class="font-medium text-slate-800 dark:text-slate-100"
                            >
                                Update Profile Information
                            </p>
                            <Link :href="route('profile.edit')">
                                <GlassButton size="sm" variant="secondary"
                                    >Edit Profile</GlassButton
                                >
                            </Link>
                        </div>
                        <div
                            class="px-5 py-6 text-sm text-slate-500 dark:text-slate-400"
                        >
                            More actions coming soon!
                        </div>
                    </div>
                </GlassCard>
            </div>

            <GlassCard
                v-if="props.timelineEvents.length"
                variant="lite"
                content-class="space-y-4"
                :disable-shine="true"
            >
                <div>
                    <h2
                        class="text-sm font-semibold text-slate-900 dark:text-slate-100"
                    >
                        Your Impact Timeline
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        See how your contributions are making a difference.
                    </p>
                </div>
                <ActivityTimeline :entries="props.timelineEvents" />
            </GlassCard>
        </div>
    </AppLayout>
</template>
