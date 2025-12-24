<script setup lang="ts">
import ActivityTimeline from '@/components/ActivityTimeline.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
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
    visit: {
        id: number;
        user_id: number;
        user: {
            id: number;
            name: string;
        };
        elder_id: number;
        elder: {
            id: number;
            first_name: string;
            last_name: string;
        };
        branch_id: number;
        branch: {
            id: number;
            name: string;
        };
        visit_date: string;
        purpose: string;
        notes: string | null;
        status: 'pending' | 'approved' | 'rejected' | 'completed';
    };
    activity: ActivityEntry[];
    breadcrumbs: { title: string; href: string }[];
    print?: boolean;
}>();

const statusBadgeClass = computed(() => {
    switch (props.visit.status) {
        case 'approved':
            return 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-200';
        case 'pending':
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-200';
        case 'completed':
            return 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-200';
        case 'rejected':
            return 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-200';
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

const buildPrintTitle = () =>
    `Visit Details - ${props.visit.user.name} to ${props.visit.elder.first_name} ${props.visit.elder.last_name}`;

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
    <Head
        :title="`Visit - ${props.visit.user.name} to ${props.visit.elder.first_name} ${props.visit.elder.last_name}`"
    />

    <AppLayout :breadcrumbs="props.breadcrumbs">
        <div class="space-y-6">
            <div class="liquidGlass-wrapper print:hidden">
                <span class="liquidGlass-inner-shine" aria-hidden="true" />
                <div
                    class="liquidGlass-content flex flex-col gap-4 px-5 py-5 md:flex-row md:items-center md:justify-between"
                >
                    <div>
                        <h1
                            class="text-2xl font-semibold text-slate-900 dark:text-slate-100"
                        >
                            Visit details
                        </h1>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            Visit by {{ props.visit.user.name }} for
                            {{ props.visit.elder.first_name }}
                            {{ props.visit.elder.last_name }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <GlassButton as="span" size="sm" variant="secondary">
                            <Link
                                :href="route('visits.index')"
                                class="flex items-center gap-2"
                            >
                                <ArrowLeft class="size-4" />
                                <span>Back to list</span>
                            </Link>
                        </GlassButton>

                        <GlassButton as="span" size="sm" variant="primary">
                            <Link
                                :href="route('visits.edit', props.visit.id)"
                                class="flex items-center gap-2"
                            >
                                <Edit3 class="size-4" />
                                <span>Edit</span>
                            </Link>
                        </GlassButton>

                        <GlassButton
                            size="sm"
                            type="button"
                            class="flex items-center gap-2"
                            variant="warning"
                            @click="printRecord"
                        >
                            <Printer class="size-4" />
                            <span>Print</span>
                        </GlassButton>
                    </div>
                </div>
            </div>

            <div class="hidden text-center text-slate-800 print:block">
                <img
                    src="/images/logo.svg"
                    alt="Logo"
                    class="print-logo mx-auto mb-3 h-12 w-auto"
                />
                <h1 class="text-xl font-semibold">{{ $page.props.name }}</h1>
                <p class="text-sm">
                    Visit Details: {{ props.visit.user.name }} to
                    {{ props.visit.elder.first_name }}
                    {{ props.visit.elder.last_name }}
                </p>
                <p class="text-xs text-slate-500">
                    Printed {{ printTimestamp }}
                </p>
                <hr class="print-divider" />
            </div>

            <GlassCard
                padding="p-0"
                class="print:border print:bg-white print:shadow-none"
            >
                <div
                    class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 dark:border-slate-800/60 dark:bg-slate-900/60 print:border print:bg-white"
                >
                    <div
                        class="flex flex-col gap-6 p-6 md:flex-row md:items-start"
                    >
                        <div class="grid flex-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <p
                                    class="text-xs font-semibold tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                >
                                    Visit Information
                                </p>
                                <div
                                    class="space-y-2 rounded-lg border border-slate-200/70 bg-white/70 p-4 text-sm shadow-sm dark:border-slate-800/50 dark:bg-slate-900/60"
                                >
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Visitor
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'users.show',
                                                        visit.user.id,
                                                    )
                                                "
                                                class="text-indigo-600 hover:underline"
                                            >
                                                {{ props.visit.user.name }}
                                            </Link>
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Elder
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'elders.show',
                                                        visit.elder.id,
                                                    )
                                                "
                                                class="text-indigo-600 hover:underline"
                                            >
                                                {{
                                                    props.visit.elder.first_name
                                                }}
                                                {{
                                                    props.visit.elder.last_name
                                                }}
                                            </Link>
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Branch
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'branches.show',
                                                        visit.branch.id,
                                                    )
                                                "
                                                class="text-indigo-600 hover:underline"
                                            >
                                                {{ props.visit.branch.name }}
                                            </Link>
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Visit Date
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ props.visit.visit_date }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Purpose
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ props.visit.purpose }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Status
                                        </p>
                                        <span
                                            class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                                            :class="statusBadgeClass"
                                        >
                                            {{ props.visit.status }}
                                        </span>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Notes
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ props.visit.notes ?? '-' }}
                                        </p>
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
                class="print:border print:bg-white print:shadow-none"
            >
                <div>
                    <h2
                        class="text-sm font-semibold text-slate-900 dark:text-slate-100"
                    >
                        Recent activity
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Audit trail for updates to this visit.
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
