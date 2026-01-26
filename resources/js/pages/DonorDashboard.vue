<script setup lang="ts">
import ActivityTimeline from '@/components/ActivityTimeline.vue'; // Import ActivityTimeline
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import MetricCard from '@/components/dashboard/MetricCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    CalendarCheck,
    DollarSign,
    Gift,
    HeartHandshake,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';

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
    timelineEvents: TimelineEntry[];
    pendingProposals: Array<{
        id: number;
        elder: ElderSummary | null;
        amount: number;
        frequency: string;
        relationship_type: string | null;
        notes: string | null;
        expires_at: string | null;
        expires_at_human: string | null;
    }>;
    annualReports?: Array<{
        id: number;
        year: number;
        download_url: string;
        generated_at?: string | null;
    }>;
}>();

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

const pendingProposals = computed(
    () => props.pendingProposals ?? [],
);
const annualReports = computed(() => props.annualReports ?? []);

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

const proposalActionForm = useForm({});
const proposalActionId = ref<number | null>(null);
const proposalActionType = ref<'accept' | 'decline' | null>(null);

const handleProposalAction = (proposalId: number, action: 'accept' | 'decline') => {
    proposalActionId.value = proposalId;
    proposalActionType.value = action;

    proposalActionForm.post(route(`proposals.${action}`, proposalId), {
        preserveScroll: true,
        onFinish: () => {
            proposalActionId.value = null;
            proposalActionType.value = null;
        },
    });
};

const isProcessingAction = (proposalId: number, action: 'accept' | 'decline') => {
    return (
        proposalActionForm.processing &&
        proposalActionId.value === proposalId &&
        proposalActionType.value === action
    );
};

const myDonationsUrl = route('donors.donations.index', undefined, false);
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

            <div class="flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-slate-200/70 bg-white/80 p-4 shadow-sm dark:border-slate-800/70 dark:bg-slate-900/60">
                <div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Manage your giving</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Every pledge you record becomes a private member record—use the links to revisit receipts, edit cadences, and stay on top of transfers.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <Link
                        class="rounded-full border border-indigo-500/70 bg-indigo-500/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-indigo-700 transition hover:bg-indigo-500/20 dark:border-indigo-400/60 dark:bg-indigo-500/10 dark:text-indigo-200"
                        :href="myDonationsUrl"
                    >
                        View my donations
                    </Link>
                </div>
            </div>
            <p class="mt-2 text-[11px] text-slate-500 dark:text-slate-400">
                Prefer to browse everything you’ve given? Open the same
                <Link
                    :href="myDonationsUrl"
                    class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400"
                >
                    My donations
                </Link>
                page to review history and click “Manage pledge” for any gift.
            </p>

            <GlassCard variant="lite" padding="p-0">
                <div
                    class="border-b border-slate-200/70 px-5 py-4 dark:border-slate-800/60"
                >
                    <div
                        class="text-sm font-semibold text-slate-800 dark:text-slate-100"
                    >
                        Match invitations
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Pending proposals awaiting your response.
                    </p>
                </div>

                <div
                    v-if="pendingProposals.length"
                    class="divide-y divide-slate-200/70 dark:divide-slate-800/60"
                >
                    <div
                        v-for="proposal in pendingProposals"
                        :key="proposal.id"
                        class="flex flex-col gap-4 px-5 py-4 text-sm text-slate-600 dark:text-slate-300 md:flex-row md:items-center"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="h-12 w-12 overflow-hidden rounded-full border border-slate-200/70 dark:border-slate-700"
                            >
                                <img
                                    v-if="proposal.elder"
                                    :src="proposal.elder.profile_photo_url"
                                    :alt="`${proposal.elder.first_name} ${proposal.elder.last_name}`"
                                    class="h-full w-full object-cover"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center bg-slate-100 text-xs font-semibold text-slate-500"
                                >
                                    ?
                                </div>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-slate-900 dark:text-slate-100"
                                >
                                    {{
                                        proposal.elder
                                            ? `${proposal.elder.first_name} ${proposal.elder.last_name}`
                                            : 'Elder'
                                    }}
                                </p>
                                <p
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
                                    {{
                                        proposal.relationship_type
                                            ? `${proposal.relationship_type} invitation`
                                            : 'Open sponsorship'
                                    }}
                                </p>
                                <p
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
                                    Expires
                                    {{ proposal.expires_at_human ?? 'soon' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex-1 space-y-2">
                            <p
                                class="text-sm font-medium text-slate-900 dark:text-slate-100"
                            >
                                {{ proposal.amount }} ETB ·
                                {{ proposal.frequency }}
                            </p>
                            <p
                                v-if="proposal.notes"
                                class="text-xs text-slate-500 dark:text-slate-400"
                            >
                                {{ proposal.notes }}
                            </p>
                        </div>
                        <div class="flex flex-col gap-2 md:flex-row">
                            <GlassButton
                                size="sm"
                                variant="primary"
                                :disabled="isProcessingAction(proposal.id, 'accept')"
                                @click="handleProposalAction(proposal.id, 'accept')"
                            >
                                <span
                                    v-if="
                                        isProcessingAction(proposal.id, 'accept')
                                    "
                                    >Accepting…</span
                                >
                                <span v-else>Accept</span>
                            </GlassButton>
                            <GlassButton
                                size="sm"
                                variant="secondary"
                                :disabled="isProcessingAction(proposal.id, 'decline')"
                                @click="handleProposalAction(proposal.id, 'decline')"
                            >
                                <span
                                    v-if="
                                        isProcessingAction(
                                            proposal.id,
                                            'decline',
                                        )
                                    "
                                    >Declining…</span
                                >
                                <span v-else>Decline</span>
                            </GlassButton>
                        </div>
                    </div>
                </div>
                <div
                    v-else
                    class="px-5 py-6 text-sm text-slate-500 dark:text-slate-400"
                >
                    No pending invitations right now. We’ll notify you when a new
                    match arrives.
                </div>
            </GlassCard>

            <GlassCard variant="lite" v-if="annualReports.length" padding="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            Annual Impact Books
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Download yearly summaries of your relationship with the elders.
                        </p>
                    </div>
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        {{ annualReports.length }} available
                    </span>
                </div>
                <div class="mt-4 space-y-3">
                    <div
                        v-for="report in annualReports"
                        :key="report.id"
                        class="flex flex-col gap-2 rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-3 text-sm text-slate-600 dark:border-slate-800/60 dark:bg-slate-900/60"
                    >
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-slate-900 dark:text-white">
                                {{ report.year }}
                            </p>
                            <a
                                :href="report.download_url"
                                target="_blank"
                                class="text-xs font-semibold text-indigo-600 hover:underline dark:text-indigo-300"
                            >
                                Download PDF
                            </a>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Generated on {{ report.generated_at || '—' }}
                        </p>
                    </div>
                </div>
            </GlassCard>

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

            <GlassCard variant="lite" v-if="annualReports.length" padding="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            Annual Impact Books
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Download yearly summaries of your relationship with the elders.
                        </p>
                    </div>
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        {{ annualReports.length }} available
                    </span>
                </div>
                <div class="mt-4 space-y-3">
                    <div
                        v-for="report in annualReports"
                        :key="report.id"
                        class="flex flex-col gap-2 rounded-2xl border border-slate-200/70 bg-white/80 px-4 py-3 text-sm text-slate-600 dark:border-slate-800/60 dark:bg-slate-900/60"
                    >
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-slate-900 dark:text-white">
                                {{ report.year }}
                            </p>
                            <a
                                :href="report.download_url"
                                target="_blank"
                                class="text-xs font-semibold text-indigo-600 hover:underline dark:text-indigo-300"
                            >
                                Download PDF
                            </a>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Generated on {{ report.generated_at || '—' }}
                        </p>
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
                                Download Annual Statement
                            </p>
                            <Link
                                :href="route('receipts.annual', new Date().getFullYear())"
                            >
                                <GlassButton size="sm" variant="secondary"
                                    >Download</GlassButton
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
