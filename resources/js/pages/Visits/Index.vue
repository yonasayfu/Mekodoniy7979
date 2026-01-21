<script setup lang="ts">
import GlassCard from '@/components/GlassCard.vue';
import Pagination from '@/components/Pagination.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { useTableFilters } from '@/composables/useTableFilters';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/lib/confirm';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    AlertTriangle,
    BusFront,
    ChevronLeft,
    ChevronRight,
    Edit3,
    Eye,
    Languages,
    Search,
    Trash2,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted } from 'vue';

interface VisitSummary {
    id: number;
    user_id: number;
    user_name: string;
    elder_id: number;
    elder_full_name: string;
    branch_id: number;
    branch_name: string;
    visit_date: string;
    purpose: string;
    notes: string | null;
    status: 'pending' | 'approved' | 'rejected' | 'completed';
    needs_translator?: boolean;
    needs_transport?: boolean;
    approval_deadline?: string | null;
}

interface StatCard {
    label: string;
    value: number;
    tone?: 'primary' | 'success' | 'muted';
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationMeta {
    from?: number | null;
}

interface CalendarVisit {
    id: number;
    time: string;
    elder_name: string;
    visitor_name: string;
    status: string;
}

interface CalendarDay {
    date: string;
    label: string;
    isCurrentMonth: boolean;
    isToday: boolean;
    visits: CalendarVisit[];
}

interface CalendarPayload {
    monthLabel: string;
    currentMonth: string;
    previousUrl: string | null;
    nextUrl: string | null;
    days: CalendarDay[];
}

interface SlaAlert {
    id: number;
    elder_name: string;
    visitor_name: string;
    branch_name: string;
    deadline: string | null;
    state: 'warning' | 'breached';
    deadline_for_humans?: string | null;
}

const props = defineProps<{
    visits: {
        data: VisitSummary[];
        links: PaginationLink[];
        meta?: PaginationMeta;
    };
    stats?: StatCard[];
    filters?: {
        search?: string;
        sort?: string;
        direction?: 'asc' | 'desc';
        per_page?: number;
    };
    can: {
        create: boolean;
        edit: boolean;
        delete: boolean;
    };
    print?: boolean;
    calendar?: CalendarPayload;
    slaAlerts?: SlaAlert[];
}>();

const can = computed(
    () =>
        props.can ?? {
            create: false,
            edit: false,
            delete: false,
        },
);

const canCreate = computed(() => can.value.create);
const canEdit = computed(() => can.value.edit);
const canDelete = computed(() => can.value.delete);

const tableFilters = useTableFilters({
    route: '/visits',
    initial: {
        search: props.filters?.search ?? '',
        sort: props.filters?.sort ?? '',
        direction: props.filters?.direction ?? 'asc',
        per_page: props.filters?.per_page ?? 5,
    },
});

const { search, sort, direction, perPage, apply, toggleSort } = tableFilters;

const printMode = computed(() => props.print ?? false);
let printTimer: number | undefined;

const printDocumentTitle = 'Visits Directory';
const printTimestamp = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
}).format(new Date());

const triggerPrint = () => {
    const originalTitle = document.title;
    document.title = printDocumentTitle;
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

const buildQueryString = (extra: Record<string, unknown> = {}) => {
    const params = new URLSearchParams();

    if (search.value) {
        params.set('search', search.value);
    }

    if (sort.value) {
        params.set('sort', sort.value);
        params.set('direction', direction.value);
    }

    params.set('per_page', String(perPage.value));

    Object.entries(extra).forEach(([key, value]) => {
        if (value === undefined || value === null || value === '') {
            return;
        }

        params.set(key, String(value));
    });

    const query = params.toString();

    return query ? `?${query}` : '';
};

const exportCsv = () => {
    const query = buildQueryString();
    // Assuming a visits export route exists
    window.open(`/visits/export${query}`, '_blank', 'noopener=yes');
};

const printCurrent = () => {
    triggerPrint();
};

const stats = computed<StatCard[]>(() => props.stats ?? []);
const visitList = computed<VisitSummary[]>(() => props.visits?.data ?? []);
const hasResults = computed<boolean>(() => visitList.value.length > 0);
const paginationLinks = computed(() => props.visits?.links ?? []);
const paginationFrom = computed(() => props.visits?.meta?.from ?? 1);
const calendar = computed<CalendarPayload>(() => {
    return (
        props.calendar ?? {
            monthLabel: '',
            currentMonth: '',
            previousUrl: null,
            nextUrl: null,
            days: [],
        }
    );
});
const slaAlerts = computed<SlaAlert[]>(() => props.slaAlerts ?? []);
const calendarWeekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
const dateFormatter = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
});
const deadlineFormatter = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
});

const goToCalendarMonth = (url?: string | null) => {
    if (!url) {
        return;
    }

    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
    });
};

const formatDateTime = (value?: string | null) => {
    if (!value) {
        return '—';
    }

    return dateFormatter.format(new Date(value));
};

const formatDeadline = (value?: string | null) => {
    if (!value) {
        return '—';
    }

    return deadlineFormatter.format(new Date(value));
};

const destroy = async (visit: VisitSummary) => {
    const accepted = await confirmDialog({
        title: 'Remove visit?',
        message: `This will delete the visit for ${visit.elder_full_name} by ${visit.user_name}. This action cannot be undone.`,
        confirmText: 'Remove',
        cancelText: 'Cancel',
    });

    if (!accepted) {
        return;
    }

    router.delete(`/visits/${visit.id}`, {
        preserveScroll: true,
    });
};

const statusTone = (status: string) => {
    switch (status) {
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
};

const deadlineSeverity = (visit: VisitSummary) => {
    if (visit.status !== 'pending' || !visit.approval_deadline) {
        return null;
    }

    const deadline = new Date(visit.approval_deadline).getTime();
    const now = Date.now();
    const twelveHours = 1000 * 60 * 60 * 12;

    if (deadline < now) {
        return 'breached';
    }

    if (deadline - now <= twelveHours) {
        return 'warning';
    }

    return null;
};

const statTone = (tone?: string) => {
    switch (tone) {
        case 'success':
            return 'text-emerald-600 dark:text-emerald-300';
        case 'muted':
            return 'text-slate-500 dark:text-slate-300';
        default:
            return 'text-indigo-600 dark:text-indigo-300';
    }
};
</script>

<template>
    <Head title="Visits" />

    <AppLayout :breadcrumbs="[{ title: 'Visits', href: '/visits' }]">
        <div class="space-y-6">
            <ResourceToolbar
                title="Visit Management"
                description="Manage visits scheduled for elders."
                :create-route="canCreate ? '/visits/create' : undefined"
                :show-create="canCreate"
                @export="exportCsv"
                @print="printCurrent"
            />

            <div class="hidden text-center text-slate-800 print:block">
                <img
                    src="/images/logo.svg"
                    alt="Logo"
                    class="print-logo mx-auto mb-3 h-12 w-auto"
                />
                <h1 class="text-xl font-semibold">{{ $page.props.name }}</h1>
                <p class="text-sm">Visits Directory</p>
                <p class="text-xs text-slate-500">
                    Printed {{ printTimestamp }}
                </p>
                <hr class="print-divider" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 print:hidden">
                <GlassCard
                    v-for="metric in stats"
                    :key="metric.label"
                    variant="lite"
                    padding="px-5 py-6"
                    content-class="space-y-1"
                >
                    <p
                        class="text-xs font-medium tracking-wide text-slate-500 uppercase dark:text-slate-400"
                    >
                        {{ metric.label }}
                    </p>
                    <p
                        class="text-3xl font-semibold"
                        :class="statTone(metric.tone)"
                    >
                        {{ metric.value }}
                    </p>
                </GlassCard>
            </div>

            <div class="grid gap-6 lg:grid-cols-3 print:hidden">
                <GlassCard
                    variant="lite"
                    padding="px-5 py-5"
                    class="lg:col-span-2"
                >
                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                            >
                                Visit calendar
                            </p>
                            <h3
                                class="text-xl font-semibold text-slate-900 dark:text-slate-100"
                            >
                                {{ calendar.monthLabel }}
                            </h3>
                        </div>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800/60"
                                @click="goToCalendarMonth(calendar.previousUrl)"
                            >
                                <ChevronLeft class="mr-1 size-4" />
                                Prev
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800/60"
                                @click="goToCalendarMonth(calendar.nextUrl)"
                            >
                                Next
                                <ChevronRight class="ml-1 size-4" />
                            </button>
                        </div>
                    </div>
                    <div
                        class="mt-4 grid grid-cols-7 text-center text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500"
                    >
                        <div
                            v-for="weekday in calendarWeekdays"
                            :key="weekday"
                            class="py-1"
                        >
                            {{ weekday }}
                        </div>
                    </div>
                    <div class="mt-2 grid grid-cols-7 gap-2">
                        <div
                            v-for="day in calendar.days"
                            :key="day.date"
                            :class="[
                                'rounded-2xl border px-2 py-2 text-xs transition',
                                day.isCurrentMonth
                                    ? 'border-slate-200 dark:border-slate-700'
                                    : 'border-transparent bg-slate-50 text-slate-400 dark:bg-slate-900/30',
                                day.isToday
                                    ? 'ring-2 ring-indigo-300 dark:ring-indigo-500/60'
                                    : '',
                            ]"
                        >
                            <div
                                :class="[
                                    'inline-flex h-6 w-6 items-center justify-center rounded-full text-[11px] font-semibold',
                                    day.isToday
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-200',
                                ]"
                            >
                                {{ day.label }}
                            </div>
                            <div
                                v-for="visit in day.visits"
                                :key="visit.id"
                                class="mt-2 rounded-xl border border-transparent bg-indigo-50/80 px-2 py-1 text-[11px] text-indigo-900 dark:bg-indigo-500/10 dark:text-indigo-100"
                            >
                                <p class="font-semibold">
                                    {{ visit.time }}
                                    <span
                                        class="ml-2 font-normal text-slate-600 dark:text-slate-300"
                                    >
                                        {{ visit.elder_name }}
                                    </span>
                                </p>
                                <p class="text-slate-500 dark:text-slate-400">
                                    {{ visit.visitor_name }}
                                </p>
                            </div>
                        </div>
                    </div>
                </GlassCard>

                <GlassCard variant="lite" padding="px-5 py-5">
                    <div class="flex items-center gap-2 text-slate-500">
                        <AlertTriangle class="size-5 text-amber-500" />
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide"
                            >
                                Approval SLA
                            </p>
                            <p
                                class="text-sm text-slate-600 dark:text-slate-300"
                            >
                                Pending visits needing branch action
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 space-y-3">
                        <div
                            v-if="!slaAlerts.length"
                            class="rounded-xl border border-slate-200/70 bg-white/80 px-3 py-4 text-center text-sm text-slate-500 dark:border-slate-800/60 dark:bg-slate-900/60"
                        >
                            All caught up. No pending approvals right now.
                        </div>
                        <div
                            v-for="alert in slaAlerts"
                            v-else
                            :key="alert.id"
                            class="rounded-2xl border border-slate-100/70 bg-white/90 px-3 py-3 text-sm shadow-sm dark:border-slate-800/50 dark:bg-slate-900/70"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <p
                                    class="font-semibold text-slate-900 dark:text-slate-100"
                                >
                                    {{ alert.elder_name }}
                                </p>
                                <span
                                    :class="[
                                        'rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide',
                                        alert.state === 'breached'
                                            ? 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-100'
                                            : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-100',
                                    ]"
                                >
                                    {{
                                        alert.state === 'breached'
                                            ? 'Breached'
                                            : 'Due soon'
                                    }}
                                </span>
                            </div>
                            <p
                                class="mt-1 text-xs text-slate-500 dark:text-slate-300"
                            >
                                {{ alert.visitor_name }} – {{ alert.branch_name }}
                            </p>
                            <p
                                class="mt-1 text-xs font-medium text-slate-600 dark:text-slate-200"
                            >
                                Deadline in
                                {{ alert.deadline_for_humans || 'n/a' }}
                            </p>
                        </div>
                    </div>
                </GlassCard>
            </div>

            <div
                class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between print:hidden"
            >
                <div class="search-glass relative w-full max-w-sm">
                    <span
                        class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"
                    >
                        <Search class="size-4" />
                    </span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search visits (donor, elder, purpose)"
                        class="w-full rounded-lg border border-transparent bg-white/80 py-2 pr-3 pl-10 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <label
                        for="perPage"
                        class="text-xs tracking-wide text-slate-500 uppercase dark:text-slate-400"
                        >Per Page</label
                    >
                    <select
                        id="perPage"
                        v-model.number="perPage"
                        class="rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                    >
                        <option :value="5">5</option>
                        <option :value="10">10</option>
                        <option :value="25">25</option>
                        <option :value="50">50</option>
                        <option :value="100">100</option>
                    </select>
                </div>
            </div>

            <div
                class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/70 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/50 print:border print:bg-white print:shadow-none"
            >
                <table
                    class="print-table min-w-full divide-y divide-slate-200 dark:divide-slate-800"
                >
                    <thead class="bg-slate-50/80 dark:bg-slate-900/80">
                        <tr>
                            <th
                                class="px-5 py-3 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase dark:text-slate-400"
                            >
                                #
                            </th>
                            <th
                                class="cursor-pointer px-5 py-3 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase select-none dark:text-slate-400"
                                @click="toggleSort('user_name')"
                            >
                                Visitor
                                <span
                                    v-if="sort === 'user_name'"
                                    class="ml-1 text-[10px] text-slate-400"
                                >
                                    {{ direction === 'asc' ? '⬆️' : '⬇️' }}
                                </span>
                            </th>
                            <th
                                class="cursor-pointer px-5 py-3 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase select-none dark:text-slate-400"
                                @click="toggleSort('elder_full_name')"
                            >
                                Elder
                                <span
                                    v-if="sort === 'elder_full_name'"
                                    class="ml-1 text-[10px] text-slate-400"
                                >
                                    {{ direction === 'asc' ? '⬆️' : '⬇️' }}
                                </span>
                            </th>
                            <th
                                class="px-5 py-3 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase dark:text-slate-400"
                            >
                                Branch
                            </th>
                            <th
                                class="px-5 py-3 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase dark:text-slate-400"
                            >
                                Visit Date
                            </th>
                            <th
                                class="px-5 py-3 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase dark:text-slate-400"
                            >
                                Purpose
                            </th>
                            <th
                                class="px-5 py-3 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase dark:text-slate-400"
                            >
                                Logistics
                            </th>
                            <th
                                class="px-5 py-3 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase dark:text-slate-400"
                            >
                                Status
                            </th>
                            <th
                                class="px-5 py-3 text-right text-xs font-semibold tracking-wide text-slate-500 uppercase dark:text-slate-400 print:hidden"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-slate-200 bg-white/90 dark:divide-slate-800 dark:bg-slate-950/40 print:bg-white"
                    >
                        <tr v-if="!hasResults">
                            <td
                                colspan="8"
                                class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-300"
                            >
                                No visits match your filters yet. Add a new
                                visit to get started.
                            </td>
                        </tr>
                        <tr
                            v-for="(visit, index) in visitList"
                            v-else
                            :key="visit.id"
                            :class="[
                                'hover:bg-slate-50/70 dark:hover:bg-slate-900/50',
                                deadlineSeverity(visit) === 'breached'
                                    ? 'bg-red-50/60 dark:bg-red-500/5'
                                    : '',
                            ]"
                        >
                            <td
                                class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300"
                            >
                                {{ paginationFrom + index }}
                            </td>
                            <td
                                class="px-5 py-4 text-sm font-medium text-slate-900 dark:text-slate-100"
                            >
                                <Link
                                    :href="route('users.show', visit.user_id)"
                                    class="transition hover:text-indigo-600 dark:hover:text-indigo-300"
                                >
                                    {{ visit.user_name }}
                                </Link>
                            </td>
                            <td
                                class="px-5 py-4 text-sm font-medium text-slate-900 dark:text-slate-100"
                            >
                                <Link
                                    :href="route('elders.show', visit.elder_id)"
                                    class="transition hover:text-indigo-600 dark:hover:text-indigo-300"
                                >
                                    {{ visit.elder_full_name }}
                                </Link>
                            </td>
                            <td
                                class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300"
                            >
                                {{ visit.branch_name }}
                            </td>
                            <td
                                class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300"
                            >
                                {{ formatDateTime(visit.visit_date) }}
                            </td>
                            <td
                                class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300"
                            >
                                {{ visit.purpose }}
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-if="visit.needs_translator"
                                        class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-200"
                                    >
                                        <Languages class="size-3.5" />
                                        Translator
                                    </span>
                                    <span
                                        v-if="visit.needs_transport"
                                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200"
                                    >
                                        <BusFront class="size-3.5" />
                                        Transport
                                    </span>
                                    <span
                                        v-if="!visit.needs_translator && !visit.needs_transport"
                                        class="text-xs text-slate-400"
                                    >
                                        —
                                    </span>
                                </div>
                            </td>
                            <td
                                class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300"
                            >
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="statusTone(visit.status)"
                                >
                                    {{ visit.status }}
                                </span>
                                <p
                                    v-if="visit.approval_deadline && visit.status === 'pending'"
                                    class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                                >
                                    SLA: {{ formatDeadline(visit.approval_deadline) }}
                                </p>
                            </td>
                            <td
                                class="px-5 py-4 text-right text-sm print:hidden"
                            >
                                <div class="flex justify-end gap-2">
                                    <Link
                                        :href="route('visits.show', visit.id)"
                                        class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300"
                                        title="View visit details"
                                    >
                                        <Eye class="size-4" />
                                        <span class="sr-only">View</span>
                                    </Link>
                                    <Link
                                        v-if="canEdit"
                                        :href="route('visits.edit', visit.id)"
                                        class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300"
                                        title="Edit visit"
                                    >
                                        <Edit3 class="size-4" />
                                        <span class="sr-only">Edit</span>
                                    </Link>
                                    <button
                                        v-if="canDelete"
                                        type="button"
                                        class="inline-flex items-center rounded-md p-2 text-red-500 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
                                        title="Remove visit"
                                        @click="destroy(visit)"
                                    >
                                        <Trash2 class="size-4" />
                                        <span class="sr-only">Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end print:hidden">
                <Pagination :links="paginationLinks" />
            </div>
        </div>
    </AppLayout>
</template>

<style>
@media print {
    @page {
        size: A4 landscape;
        margin: 1.5cm;
    }

    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        background-color: #ffffff !important;
        color: #0f172a !important;
        height: auto !important;
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

    .print-table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    .print-table thead tr {
        background-color: #f8fafc !important;
    }

    .print-table th,
    .print-table td {
        border: 1px solid #e2e8f0 !important;
        padding: 6px 8px !important;
        font-size: 12px !important;
        color: #0f172a !important;
        background-color: #ffffff !important;
    }

    .min-h-screen {
        min-height: auto !important;
    }

    main {
        page-break-after: avoid;
    }

    .liquidGlass-wrapper,
    .liquidGlass-content {
        background: #ffffff !important;
        box-shadow: none !important;
    }

    .liquidGlass-inner-shine {
        display: none !important;
    }

    .app-sidebar {
        display: none !important;
    }
}
</style>
