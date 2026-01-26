<script setup lang="ts">
import MetricCard from '@/components/dashboard/MetricCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useRoute } from '@/composables/useRoute';
import http from '@/lib/http';
import { dashboard } from '@/routes';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    CalendarClock,
    ClipboardList,
    Download as DownloadIcon,
    Megaphone,
    ShieldCheck,
    UserCheck,
    Users,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

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

type PendingGuestDonation = {
    id: number;
    amount: number;
    currency: string | null;
    guest_name: string | null;
    guest_email: string | null;
    guest_phone: string | null;
    payment_gateway: string | null;
    payment_status: string | null;
    cadence: string | null;
    deduction_schedule: string | null;
    payment_reference: string | null;
    payment_id: string | null;
    elder_name: string | null;
    elder_relationship: string | null;
    branch_name: string | null;
    donation_type: string | null;
    notes: string | null;
    receipt_url: string | null;
    mandate_url: string | null;
    created_at: string | null;
};

const route = useRoute();
const currencyFormatter = new Intl.NumberFormat('en-ET', {
    style: 'currency',
    currency: 'ETB',
    maximumFractionDigits: 0,
});

const formatCurrency = (value?: number | null) =>
    currencyFormatter.format(value ?? 0);

const progressPercent = (value?: number) => {
    const percent = Math.round((value ?? 0) * 100);
    return `${Math.min(100, Math.max(0, percent))}%`;
};

const props = defineProps<{
    metrics: Metric[];
    pendingGuestDonations: PendingGuestDonation[];
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

const pendingGuestDonationList = computed(
    (): PendingGuestDonation[] => props.pendingGuestDonations ?? [],
);

const hasPendingGuestDonations = computed(
    () => pendingGuestDonationList.value.length > 0,
);

const confirmingDonationId = ref<number | null>(null);

const formatTimestamp = (value?: string | null) => {
    if (!value) {
        return 'Unknown time';
    }
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }
    return date.toLocaleString();
};

const confirmGuestDonation = async (donation: PendingGuestDonation) => {
    if (!donation.id) {
        return;
    }

    confirmingDonationId.value = donation.id;

    try {
        await http.post(
            route('donations.confirm', { donation: donation.id }),
            { send_receipt: true },
        );
        router.reload({ only: ['pendingGuestDonations'] });
    } catch (error) {
        console.error('Error confirming guest donation', error);
    } finally {
        confirmingDonationId.value = null;
    }
};
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

            <section class="mt-6 rounded-3xl border border-slate-200/70 bg-white/90 p-6 shadow-sm dark:border-slate-800/70 dark:bg-slate-900/60">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">
                            Pending guest donations
                        </p>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-200">
                            Download receipts/mandates and confirm verified transfers without leaving the dashboard.
                        </p>
                    </div>
                    <span
                        v-if="hasPendingGuestDonations"
                        class="text-xs font-semibold uppercase tracking-[0.4em] text-indigo-600 dark:text-indigo-300"
                    >
                        {{ pendingGuestDonationList.length }} unresolved
                    </span>
                </div>

                <div class="mt-6 space-y-4">
                    <div
                        v-if="!hasPendingGuestDonations"
                        class="rounded-2xl border border-dashed border-slate-200/60 bg-slate-50 px-4 py-6 text-sm text-slate-500 dark:border-slate-800/60 dark:bg-slate-900/40 dark:text-slate-400"
                    >
                        No pending guest donations—everything is reconciled.
                    </div>

                    <article
                        v-for="donation in pendingGuestDonationList"
                        :key="donation.id"
                        class="rounded-2xl border border-slate-200/70 bg-white p-4 shadow-sm dark:border-slate-800/70 dark:bg-slate-900/60"
                    >
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ donation.guest_name || 'Anonymous guest' }}
                                    <span class="ml-2 text-xs font-normal text-slate-500 dark:text-slate-400">
                                        {{ donation.amount }} {{ donation.currency ?? 'ETB' }}
                                    </span>
                                </p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    {{ donation.elder_name ? `Elder: ${donation.elder_name}` : 'No elder assigned yet' }}
                                    <span v-if="donation.elder_relationship">
                                        • Relationship: {{ donation.elder_relationship }}
                                    </span>
                                </p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Branch:
                                    <span class="font-semibold text-slate-700 dark:text-white">
                                        {{ donation.branch_name ?? 'Branch unknown' }}
                                    </span>
                                </p>
                            </div>
                            <span
                                class="rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em]"
                                :class="donation.payment_status === 'confirmed'
                                    ? 'border-emerald-500 bg-emerald-50 text-emerald-600 dark:border-emerald-400/70 dark:bg-emerald-500/10'
                                    : 'border-amber-400 bg-amber-50 text-amber-700 dark:border-amber-500/60 dark:bg-amber-600/20'"
                            >
                                {{ (donation.payment_status ?? 'pending').toUpperCase() }}
                            </span>
                        </div>

                        <p class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                            Gateway: {{ donation.payment_gateway ?? 'Manual' }} • Reference:
                            <span class="font-semibold text-slate-700 dark:text-white">
                                {{ donation.payment_reference ?? 'N/A' }}
                            </span>
                        </p>
                        <div
                            v-if="donation.elder_funding_goal"
                            class="mt-3 space-y-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-[11px] text-slate-500 dark:border-slate-800 dark:bg-slate-900/40 dark:text-slate-400"
                        >
                            <div class="flex items-center justify-between">
                                <span>Campaign goal</span>
                                <span class="font-semibold text-slate-900 dark:text-white">
                                    {{ formatCurrency(donation.elder_funding_goal) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Remaining</span>
                                <span
                                    :class="donation.elder_is_funded
                                        ? 'text-emerald-600 dark:text-emerald-200'
                                        : 'text-amber-600 dark:text-amber-200'"
                                >
                                    {{
                                        donation.elder_is_funded
                                            ? 'Fully funded'
                                            : formatCurrency(donation.elder_funding_needed)
                                    }}
                                </span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                                <div
                                    class="h-full rounded-full bg-indigo-500 dark:bg-indigo-400"
                                    :style="{ width: progressPercent(donation.elder_funding_progress) }"
                                ></div>
                            </div>
                        </div>
                        <p v-if="donation.cadence" class="text-[11px] text-slate-500 dark:text-slate-400">
                            Cadence: {{ donation.cadence }}
                            <span v-if="donation.deduction_schedule">• {{ donation.deduction_schedule }}</span>
                        </p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Recorded: {{ formatTimestamp(donation.created_at) }}
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a
                                v-if="donation.receipt_url"
                                :href="donation.receipt_url"
                                target="_blank"
                                rel="noreferrer"
                                class="rounded-full border border-indigo-200 px-3 py-1 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 dark:border-indigo-500/50 dark:text-indigo-200"
                            >
                                View receipt
                            </a>
                            <a
                                v-if="donation.mandate_url"
                                :href="donation.mandate_url"
                                target="_blank"
                                rel="noreferrer"
                                class="rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:text-slate-100"
                            >
                                Download mandate
                            </a>
                            <button
                                type="button"
                                class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-semibold text-white transition hover:bg-emerald-500 disabled:bg-slate-300 disabled:text-slate-500"
                                :disabled="
                                    donation.payment_status === 'confirmed' ||
                                    confirmingDonationId === donation.id
                                "
                                @click.prevent="confirmGuestDonation(donation)"
                            >
                                {{ confirmingDonationId === donation.id ? 'Confirming...' : 'Confirm payment' }}
                            </button>
                        </div>
                        <p v-if="donation.notes" class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                            Notes: {{ donation.notes }}
                        </p>
                    </article>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
