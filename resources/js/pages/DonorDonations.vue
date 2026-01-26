<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="My donations" />

        <div class="flex flex-col gap-6 px-4 py-6 lg:px-8">
            <div class="flex flex-col gap-4 rounded-3xl border border-slate-200/80 bg-white/80 p-6 shadow-sm dark:border-slate-800/70 dark:bg-slate-900/60">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">My giving</p>
                    <h1 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Track your donations</h1>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                        Review every pledge, download receipts, and manage each commitment from one place.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <Link
                        :href="dashboardUrl"
                        class="inline-flex items-center justify-center rounded-full border border-indigo-500/70 bg-indigo-500/10 px-4 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-500/20 dark:border-indigo-400/60 dark:bg-indigo-500/10 dark:text-indigo-200"
                    >
                        Donor dashboard
                    </Link>
                    <Link
                        v-if="latestManageUrl"
                        :href="latestManageUrl"
                        class="inline-flex items-center justify-center rounded-full border border-emerald-500/70 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-500/20 dark:border-emerald-400/60 dark:bg-emerald-500/10 dark:text-emerald-200"
                    >
                        Manage my latest pledge
                    </Link>
                    <Link
                        :href="guestDonationUrl"
                        class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-indigo-500 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-300 dark:hover:text-indigo-300"
                    >
                        Record a new gift
                    </Link>
                </div>
                <div class="flex flex-wrap gap-6 text-sm text-slate-600 dark:text-slate-300">
                    <p class="font-semibold text-slate-900 dark:text-white">
                        Total donated: {{ formatCurrency(totalDonations) }}
                    </p>
                    <p>
                        Pending partner review: <span class="font-semibold text-amber-600 dark:text-amber-200">{{ pendingDonations }}</span>
                    </p>
                </div>
            </div>

            <section class="space-y-4">
                <article
                    v-if="!hasDonations"
                    class="rounded-3xl border border-dashed border-slate-200/70 bg-slate-50 px-6 py-10 text-center text-sm text-slate-500 dark:border-slate-800/60 dark:bg-slate-900/50 dark:text-slate-400"
                >
                    You haven’t recorded any donations yet. Start by logging a meal or sponsorship so you can track and manage it here.
                    <div class="mt-4 flex justify-center">
                        <Link
                            :href="guestDonationUrl"
                            class="rounded-full border border-indigo-600 bg-indigo-600/10 px-5 py-2 text-xs font-semibold uppercase tracking-[0.4em] text-indigo-700 transition hover:border-indigo-500 hover:bg-indigo-500/20 dark:text-indigo-200"
                        >
                            Log a donation
                        </Link>
                    </div>
                </article>

                <article
                    v-for="donation in donations"
                    :key="donation.id"
                    class="space-y-3 rounded-3xl border border-slate-200/80 bg-white/90 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800/70 dark:bg-slate-900/70"
                >
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em] text-slate-400">{{ donation.donation_mode === 'sponsorship' ? 'Sponsorship' : 'One-time meal' }}</p>
                            <p class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">
                                {{ formatCurrency(donation.amount) }}
                                <span class="ml-2 text-sm font-normal text-slate-500 dark:text-slate-400">{{ donation.currency ?? 'ETB' }}</span>
                            </p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                {{ donation.elder_name ?? 'General donation' }}
                                <span v-if="donation.relationship">
                                    · Relationship: {{ donation.relationship }}
                                </span>
                            </p>
                        </div>
                        <span
                            class="rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em]"
                            :class="statusClass(donation.payment_status)">
                            {{ donation.payment_status ? donation.payment_status.toUpperCase() : 'PENDING' }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm text-slate-600 dark:text-slate-300">
                        <p>
                            Reference:
                            <span class="font-semibold text-slate-900 dark:text-white">
                                {{ donation.payment_reference ?? 'Awaiting reference' }}
                            </span>
                        </p>
                        <p v-if="donation.cadence">
                            Cadence:
                            <span class="font-semibold text-slate-900 dark:text-white">
                                {{ donation.cadence }}
                            </span>
                        </p>
                        <p v-else>
                            Cadence: <span class="font-semibold text-slate-900 dark:text-white">Not set</span>
                        </p>
                        <p v-if="donation.deduction_schedule">
                            Deduction Schedule:
                            <span class="font-semibold text-slate-900 dark:text-white">
                                {{ donation.deduction_schedule }}
                            </span>
                        </p>
                    </div>
                    <p v-if="donation.notes" class="text-sm text-slate-500 dark:text-slate-400">
                        Notes: {{ donation.notes }}
                    </p>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <Link
                            v-if="donation.receipt_url"
                            :href="donation.receipt_url"
                            target="_blank"
                            rel="noreferrer"
                            class="rounded-full border border-indigo-200 px-3 py-1 text-indigo-600 hover:bg-indigo-50 dark:border-indigo-500/50 dark:text-indigo-200"
                        >
                            View receipt
                        </Link>
                        <Link
                            v-if="donation.mandate_url"
                            :href="donation.mandate_url"
                            target="_blank"
                            rel="noreferrer"
                            class="rounded-full border border-slate-200 px-3 py-1 text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:text-slate-100"
                        >
                            Download mandate
                        </Link>
                        <Link
                            v-if="donation.manage_url"
                            :href="donation.manage_url"
                            class="rounded-full border border-emerald-200 px-3 py-1 text-emerald-700 hover:bg-emerald-50 dark:border-emerald-400/60 dark:text-emerald-200"
                        >
                            Manage pledge
                        </Link>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Recorded: {{ formatTimestamp(donation.created_at) }}
                    </p>
                </article>
            </section>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

type DonationItem = {
    id: number;
    amount: number;
    currency: string | null;
    status: string | null;
    donation_type: string | null;
    donation_mode: 'one_time' | 'sponsorship' | null;
    cadence: string | null;
    deduction_schedule: string | null;
    recurrence_duration: number | null;
    payment_gateway: string | null;
    payment_status: string | null;
    payment_reference: string | null;
    notes: string | null;
    relationship: string | null;
    created_at: string | null;
    elder_name: string | null;
    elder_relationship: string | null;
    branch_name: string | null;
    receipt_url: string | null;
    mandate_url: string | null;
    manage_url: string | null;
};

const props = defineProps<{
    donations: DonationItem[];
    totalDonations: number;
    pendingDonations: number;
}>();

const breadcrumbs = [
    {
        title: 'My donations',
        href: route('donors.donations.index', undefined, false),
    },
];

const guestDonationUrl = route('guest.donation', undefined, false);
const dashboardUrl = route('donors.dashboard', undefined, false);

const hasDonations = computed(() => (props.donations ?? []).length > 0);
const latestManageUrl = computed(() => props.donations?.[0]?.manage_url ?? null);

const formatCurrency = (value?: number | null) =>
    new Intl.NumberFormat('en-ET', {
        style: 'currency',
        currency: 'ETB',
        maximumFractionDigits: 0,
    }).format(value ?? 0);

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

const statusClass = (status?: string | null) => {
    switch (status) {
        case 'confirmed':
        case 'completed':
            return 'border-emerald-400 bg-emerald-50 text-emerald-600 dark:border-emerald-500/60 dark:bg-emerald-500/10 dark:text-emerald-200';
        case 'awaiting_receipt':
        case 'pending':
            return 'border-amber-400 bg-amber-50 text-amber-700 dark:border-amber-500/60 dark:bg-amber-600/20 dark:text-amber-200';
        default:
            return 'border-slate-300 bg-slate-50 text-slate-600 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-300';
    }
};
</script>
