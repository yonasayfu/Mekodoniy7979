<script setup lang="ts">
import ActivityTimeline from '@/components/ActivityTimeline.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '@/components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
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
    sponsorship: {
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
        amount: number | null;
        frequency: string | null;
        start_date: string | null;
        end_date: string | null;
        status: 'pending' | 'active' | 'completed' | 'cancelled';
        notes: string | null;
    };
    activity: ActivityEntry[];
    breadcrumbs: { title: string; href: string }[];
    print?: boolean;
    can?: {
        unmatch?: boolean;
    };
}>();

const statusBadgeClass = computed(() => {
    switch (props.sponsorship.status) {
        case 'active':
            return 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-200';
        case 'pending':
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-200';
        case 'completed':
            return 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-200';
        case 'cancelled':
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
    `Sponsorship Details - ${props.sponsorship.user?.name ?? 'Unknown User'} to ${props.sponsorship.elder?.first_name ?? 'Unknown Elder First Name'} ${props.sponsorship.elder?.last_name ?? 'Unknown Elder Last Name'}`;

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

const unmatchForm = useForm({
    reason: '',
});

const submitUnmatch = () => {
    if (!props.can?.unmatch || !props.sponsorship.id) {
        return;
    }

    const confirmed = window.confirm(
        'This will end the sponsorship and return the elder to the public pool. Continue?',
    );

    if (!confirmed) {
        return;
    }

    unmatchForm.post(route('sponsorships.unmatch', props.sponsorship.id), {
        preserveScroll: true,
        onSuccess: () => {
            unmatchForm.reset('reason');
        },
    });
};
</script>

<template>
    <Head
        :title="`Sponsorship - ${props.sponsorship.user?.name ?? 'Unknown User'} to ${props.sponsorship.elder?.first_name ?? 'Unknown Elder First Name'} ${props.sponsorship.elder?.last_name ?? 'Unknown Elder Last Name'}`"
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
                            Sponsorship details
                        </h1>
                        <p class="text-sm">
                            Sponsorship by
                            {{ props.sponsorship.user?.name ?? 'Unknown User' }}
                            for
                            {{
                                props.sponsorship.elder?.first_name ??
                                'Unknown Elder First Name'
                            }}
                            {{
                                props.sponsorship.elder?.last_name ??
                                'Unknown Elder Last Name'
                            }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <GlassButton as="span" size="sm" variant="secondary">
                            <Link
                                :href="route('sponsorships.index')"
                                class="flex items-center gap-2"
                            >
                                <ArrowLeft class="size-4" />
                                <span>Back to list</span>
                            </Link>
                        </GlassButton>

                        <GlassButton as="span" size="sm" variant="primary">
                            <Link
                                v-if="props.sponsorship.id"
                                :href="
                                    route(
                                        'sponsorships.edit',
                                        props.sponsorship.id,
                                    )
                                "
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
                    Sponsorship Details:
                    {{ props.sponsorship.user?.name ?? 'Unknown User' }} to
                    {{
                        props.sponsorship.elder?.first_name ??
                        'Unknown Elder First Name'
                    }}
                    {{
                        props.sponsorship.elder?.last_name ??
                        'Unknown Elder Last Name'
                    }}
                </p>
                <p class="text-xs text-slate-500">
                    Printed {{ printTimestamp }}
                </p>
                <hr class="print-divider" />
            </div>

            <GlassCard
                v-if="props.can?.unmatch"
                variant="lite"
                padding="p-5"
                class="border border-rose-200/70 bg-rose-50/80 dark:border-rose-500/40 dark:bg-rose-500/5"
            >
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-rose-700 dark:text-rose-200">
                            Need to unmatch this elder?
                        </h2>
                        <p class="text-sm text-rose-600 dark:text-rose-200/80">
                            Closing the sponsorship will notify the donor and make the elder available again.
                        </p>
                    </div>
                    <div class="flex-1 space-y-3">
                        <textarea
                            v-model="unmatchForm.reason"
                            rows="2"
                            placeholder="Optional note for the donor"
                            class="w-full rounded-lg border border-rose-200/70 bg-white/80 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-rose-400 focus:outline-none focus:ring-rose-200 dark:border-rose-500/40 dark:bg-slate-900/40 dark:text-slate-100"
                        ></textarea>
                        <InputError
                            v-if="unmatchForm.errors.reason"
                            :message="unmatchForm.errors.reason"
                        />
                        <GlassButton
                            size="sm"
                            variant="danger"
                            class="ml-auto flex items-center gap-2"
                            :disabled="unmatchForm.processing"
                            @click="submitUnmatch"
                        >
                            <span v-if="unmatchForm.processing">Closing…</span>
                            <span v-else>Unmatch elder</span>
                        </GlassButton>
                    </div>
                </div>
            </GlassCard>

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
                                    Sponsorship Information
                                </p>
                                <div
                                    class="space-y-2 rounded-lg border border-slate-200/70 bg-white/70 p-4 text-sm shadow-sm dark:border-slate-800/50 dark:bg-slate-900/60"
                                >
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Donor
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            <Link
                                                v-if="
                                                    props.sponsorship.user?.id
                                                "
                                                :href="
                                                    route(
                                                        'users.show',
                                                        props.sponsorship.user
                                                            .id,
                                                    )
                                                "
                                                class="text-indigo-600 hover:underline"
                                            >
                                                {{
                                                    props.sponsorship.user
                                                        ?.name ?? 'Unknown User'
                                                }}
                                            </Link>
                                            <span v-else class="text-slate-500"
                                                >Unknown User</span
                                            >
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
                                                v-if="
                                                    props.sponsorship.elder?.id
                                                "
                                                :href="
                                                    route(
                                                        'elders.show',
                                                        props.sponsorship.elder
                                                            .id,
                                                    )
                                                "
                                                class="text-indigo-600 hover:underline"
                                            >
                                                {{
                                                    props.sponsorship.elder
                                                        ?.first_name ??
                                                    'Unknown Elder First Name'
                                                }}
                                                {{
                                                    props.sponsorship.elder
                                                        ?.last_name ??
                                                    'Unknown Elder Last Name'
                                                }}
                                            </Link>
                                            <span v-else class="text-slate-500"
                                                >Unknown Elder</span
                                            >
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Amount
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{
                                                props.sponsorship.amount ?? '-'
                                            }}
                                            ETB
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Frequency
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{
                                                props.sponsorship.frequency ??
                                                '-'
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Start Date
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{
                                                props.sponsorship.start_date ??
                                                '-'
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            End Date
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{
                                                props.sponsorship.end_date ??
                                                '-'
                                            }}
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
                                            {{ props.sponsorship.status }}
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
                                            {{ props.sponsorship.notes ?? '-' }}
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
                        Audit trail for updates to this sponsorship.
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
