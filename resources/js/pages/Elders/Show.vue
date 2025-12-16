<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ActivityTimeline from '@/components/ActivityTimeline.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit3, Printer } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted } from 'vue';

type ActivityEntry = {
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
    created_at?: string | null;
    created_at_for_humans?: string | null;
};

const props = defineProps<{
    elder: {
        id: number;
        branch_id: number;
        branch: {
            id: number;
            name: string;
        };
        first_name: string;
        last_name: string;
        full_name: string;
        date_of_birth: string | null;
        gender: string | null;
        address: string | null;
        city: string | null;
        country: string | null;
        phone: string | null;
        bio: string | null;
        profile_picture_path: string | null;
        priority_level: 'low' | 'medium' | 'high';
        health_status: string | null;
        special_needs: string | null;
        monthly_expenses: number | null;
        video_url: string | null;
    };
    activity: ActivityEntry[];
    breadcrumbs: { title: string; href: string }[];
    print?: boolean;
}>();

const priorityBadgeClass = computed(() => {
    switch (props.elder.priority_level) {
        case 'high':
            return 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-200';
        case 'medium':
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-200';
        case 'low':
            return 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-200';
        default:
            return 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300';
    }
});

const printMode = computed(() => props.print ?? false);
let printTimer: number | undefined;

const printTimestamp = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
}).format(new Date());

const buildPrintTitle = () => `Elder Profile - ${props.elder.first_name} ${props.elder.last_name}`;

const triggerPrint = () => {
    const originalTitle = document.title;
    document.title = buildPrintTitle();
    window.print();
    document.title = originalTitle;
};

const closeAfterPrint = () => {
    if (printMode.value && window.opener && !window.opener.closed) {
        window.close();
    }
};

onMounted(() => {
    if (printMode.value) {
        printTimer = window.setTimeout(() => {
            triggerPrint();
        }, 150);
        window.addEventListener('afterprint', closeAfterPrint);
    }
});

onBeforeUnmount(() => {
    if (printTimer) {
        window.clearTimeout(printTimer);
    }

    window.removeEventListener('afterprint', closeAfterPrint);
});

const printRecord = () => {
    triggerPrint();
};
</script>

<template>
    <Head :title="`Elder - ${props.elder.first_name} ${props.elder.last_name}`" />

    <AppLayout :breadcrumbs="props.breadcrumbs">
        <div class="space-y-6">
            <div class="liquidGlass-wrapper print:hidden">
                <span class="liquidGlass-inner-shine" aria-hidden="true" />
                <div class="liquidGlass-content flex flex-col gap-4 px-5 py-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                            Elder profile
                        </h1>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            Detailed view for {{ props.elder.first_name }} {{ props.elder.last_name }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <GlassButton as="span" size="sm" variant="secondary">
                            <Link :href="route('elders.index')" class="flex items-center gap-2">
                                <ArrowLeft class="size-4" />
                                <span>Back to list</span>
                            </Link>
                        </GlassButton>

                        <GlassButton as="span" size="sm" variant="primary">
                            <Link :href="route('elders.edit', props.elder.id)" class="flex items-center gap-2">
                                <Edit3 class="size-4" />
                                <span>Edit</span>
                            </Link>
                        </GlassButton>

                        <GlassButton size="sm" type="button" class="flex items-center gap-2" variant="warning" @click="printRecord">
                            <Printer class="size-4" />
                            <span>Print</span>
                        </GlassButton>
                    </div>
                </div>
            </div>

            <div class="hidden print:block text-center text-slate-800">
                <img src="/images/logo.svg" alt="Logo" class="mx-auto mb-3 h-12 w-auto print-logo" />
                <h1 class="text-xl font-semibold">{{ $page.props.name }}</h1>
                <p class="text-sm">Elder Profile: {{ props.elder.first_name }} {{ props.elder.last_name }}</p>
                <p class="text-xs text-slate-500">Printed {{ printTimestamp }}</p>
                <hr class="print-divider" />
            </div>

            <GlassCard padding="p-0" class="print:shadow-none print:bg-white print:border">
                <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 dark:border-slate-800/60 dark:bg-slate-900/60 print:border print:bg-white">
                    <div class="flex flex-col gap-6 p-6 md:flex-row md:items-start">
                        <div class="flex flex-col items-center gap-3 md:w-1/4">
                            <div class="relative flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-white shadow-md dark:border-slate-700 dark:bg-slate-950">
                                <img
                                    v-if="elder.profile_picture_path"
                                    :src="`/storage/${elder.profile_picture_path}`"
                                    :alt="elder.first_name + ' ' + elder.last_name"
                                    class="h-full w-full object-cover"
                                />
                                <span v-else class="text-3xl font-semibold text-slate-500 dark:text-slate-400">
                                    {{ elder.first_name.charAt(0) }}{{ elder.last_name.charAt(0) }}
                                </span>
                            </div>
                            <div class="text-center">
                                <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                    {{ props.elder.first_name }} {{ props.elder.last_name }}
                                </p>
                                <span
                                    class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="priorityBadgeClass"
                                >
                                    {{ props.elder.priority_level }}
                                </span>
                            </div>
                        </div>

                        <div class="grid flex-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                    General Information
                                </p>
                                <div class="space-y-2 rounded-lg border border-slate-200/70 bg-white/70 p-4 text-sm shadow-sm dark:border-slate-800/50 dark:bg-slate-900/60">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Branch</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.branch.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Date of Birth</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.date_of_birth ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Gender</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.gender ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Address</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.address ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">City, Country</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.city ?? '-' }}, {{ props.elder.country ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Phone</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.phone ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Bio</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.bio ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                    Health & Financial
                                </p>
                                <div class="space-y-2 rounded-lg border border-slate-200/70 bg-white/70 p-4 text-sm shadow-sm dark:border-slate-800/50 dark:bg-slate-900/60">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Health Status</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.health_status ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Special Needs</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.special_needs ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Monthly Expenses</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.monthly_expenses ?? '-' }} ETB</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Video</p>
                                        <div v-if="props.elder.video_url" class="mt-1">
                                            <video controls class="w-full rounded-lg" :src="`/storage/${props.elder.video_url}`"></video>
                                        </div>
                                        <p v-else class="font-medium text-slate-900 dark:text-slate-100">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </GlassCard>

            <GlassCard
                v-if="props.activity.length"
                variant="lite"
                content-class="space-y-4"
                :disable-shine="true"
                class="print:shadow-none print:bg-white print:border"
            >
                <div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        Recent activity
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Audit trail for updates to this elder profile.
                    </p>
                </div>
                <ActivityTimeline :entries="props.activity" />
            </GlassCard>
        </div>
    </AppLayout>
</template>

<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 1.5cm;
    }

    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        background-color: #ffffff !important;
        color: #0f172a !important;
    }

    .print-logo {
        max-height: 48px;
    }

    .print-divider {
        border: 0;
        border-top: 1px solid #cbd5f5;
        margin: 1rem auto 1.5rem;
        width: 100%;
    }

    .liquidGlass-wrapper,
    .liquidGlass-content {
        background: #ffffff !important;
        box-shadow: none !important;
    }

    .liquidGlass-inner-shine {
        display: none !important;
    }

    .rounded-lg,
    .rounded-xl {
        background: #ffffff !important;
        box-shadow: none !important;
        border-color: #e2e8f0 !important;
    }

    .text-slate-500,
    .text-slate-400,
    .text-slate-600 {
        color: #334155 !important;
    }
}
</style>
