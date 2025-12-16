<script setup lang="ts">
import GlassCard from '@/components/GlassCard.vue';
import Pagination from '@/components/Pagination.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { useTableFilters } from '@/composables/useTableFilters';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/lib/confirm';
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit3, Eye, Search, Trash2 } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted } from 'vue';

interface BranchSummary {
    id: number;
    name: string;
    location: string;
    contact_person: string;
    contact_email: string;
    contact_phone: string;
    notes: string | null;
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

const props = defineProps<{
    branches: {
        data: BranchSummary[];
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
}>();

const can = computed(
    () =>
        props.can ?? {
            create: false,
            edit: false,
            delete: false,
        },
);

const canCreate = computed(() => props.can.create);
const canEdit = computed(() => props.can.edit);
const canDelete = computed(() => props.can.delete);

const tableFilters = useTableFilters({
    route: '/branches',
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

const printDocumentTitle = 'Branches Directory';
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
    // Assuming a branches export route exists
    window.open(`/branches/export${query}`, '_blank', 'noopener=yes');
};

const printCurrent = () => {
    triggerPrint();
};

const stats = computed<StatCard[]>(() => props.stats ?? []);
const branchList = computed<BranchSummary[]>(() => props.branches?.data ?? []);
const hasResults = computed<boolean>(() => branchList.value.length > 0);
const paginationLinks = computed(() => props.branches?.links ?? []);
const paginationFrom = computed(() => props.branches?.meta?.from ?? 1);

const destroy = async (branch: BranchSummary) => {
    const accepted = await confirmDialog({
        title: 'Remove branch?',
        message: `This will delete the branch: ${branch.name}. This action cannot be undone.`,
        confirmText: 'Remove',
        cancelText: 'Cancel',
    });

    if (!accepted) {
        return;
    }

    router.delete(`/branches/${branch.id}`, {
        preserveScroll: true,
    });
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
    <Head title="Branches" />

    <AppLayout :breadcrumbs="[{ title: 'Branches', href: '/branches' }]">
        <div class="space-y-6">
            <ResourceToolbar
                title="Branch Directory"
                description="Manage organizational branches."
                :create-route="canCreate ? '/branches/create' : undefined"
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
                <p class="text-sm">Branches Directory</p>
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
                        placeholder="Search branches (name, location, contact)"
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
                                @click="toggleSort('name')"
                            >
                                Branch Name
                                <span
                                    v-if="sort === 'name'"
                                    class="ml-1 text-[10px] text-slate-400"
                                >
                                    {{ direction === 'asc' ? '⬆️' : '⬇️' }}
                                </span>
                            </th>
                            <th
                                class="px-5 py-3 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase dark:text-slate-400"
                            >
                                Location
                            </th>
                            <th
                                class="px-5 py-3 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase dark:text-slate-400"
                            >
                                Contact Person
                            </th>
                            <th
                                class="px-5 py-3 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase dark:text-slate-400"
                            >
                                Contact Info
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
                                colspan="6"
                                class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-300"
                            >
                                No branches match your filters yet. Add a new
                                branch to get started.
                            </td>
                        </tr>
                        <tr
                            v-for="(branch, index) in branchList"
                            v-else
                            :key="branch.id"
                            class="hover:bg-slate-50/70 dark:hover:bg-slate-900/50"
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
                                    :href="route('branches.show', branch.id)"
                                    class="transition hover:text-indigo-600 dark:hover:text-indigo-300"
                                >
                                    {{ branch.name }}
                                </Link>
                            </td>
                            <td
                                class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300"
                            >
                                {{ branch.location }}
                            </td>
                            <td
                                class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300"
                            >
                                {{ branch.contact_person }}
                            </td>
                            <td
                                class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300"
                            >
                                <div
                                    class="font-medium text-slate-700 dark:text-slate-200"
                                >
                                    {{ branch.contact_email }}
                                </div>
                                <div
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
                                    {{ branch.contact_phone }}
                                </div>
                            </td>
                            <td
                                class="px-5 py-4 text-right text-sm print:hidden"
                            >
                                <div class="flex justify-end gap-2">
                                    <Link
                                        :href="
                                            route('branches.show', branch.id)
                                        "
                                        class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300"
                                        title="View branch details"
                                    >
                                        <Eye class="size-4" />
                                        <span class="sr-only">View</span>
                                    </Link>
                                    <Link
                                        v-if="canEdit"
                                        :href="
                                            route('branches.edit', branch.id)
                                        "
                                        class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300"
                                        title="Edit branch"
                                    >
                                        <Edit3 class="size-4" />
                                        <span class="sr-only">Edit</span>
                                    </Link>
                                    <button
                                        v-if="canDelete"
                                        type="button"
                                        class="inline-flex items-center rounded-md p-2 text-red-500 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
                                        title="Remove branch"
                                        @click="destroy(branch)"
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
