<script setup lang="ts">
import GlassCard from '@/components/GlassCard.vue';
import Pagination from '@/components/Pagination.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { useTableFilters } from '@/composables/useTableFilters';
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDialog } from '@/lib/confirm';
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit3, Eye, Search, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';

interface CampaignSummary {
    id: number;
    title: string;
    slug: string;
    status: 'draft' | 'active' | 'ended';
    starts_at: string | null;
    ends_at: string | null;
    goal_amount: number | null;
    goal_currency: string | null;
    created_at?: string | null;
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
    campaigns: {
        data: CampaignSummary[];
        links: PaginationLink[];
        meta?: PaginationMeta;
    };
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

const canCreate = computed(() => props.can?.create ?? false);
const canEdit = computed(() => props.can?.edit ?? false);
const canDelete = computed(() => props.can?.delete ?? false);

const tableFilters = useTableFilters({
    route: '/campaigns',
    initial: {
        search: props.filters?.search ?? '',
        sort: props.filters?.sort ?? '',
        direction: props.filters?.direction ?? 'asc',
        per_page: props.filters?.per_page ?? 10,
    },
});

const { search, perPage, apply, toggleSort } = tableFilters;

const campaignList = computed<CampaignSummary[]>(
    () => props.campaigns?.data ?? [],
);
const hasResults = computed<boolean>(() => campaignList.value.length > 0);
const paginationLinks = computed(() => props.campaigns?.links ?? []);

const destroy = async (campaign: CampaignSummary) => {
    const accepted = await confirmDialog({
        title: 'Remove campaign?',
        message: `This will delete the campaign: ${campaign.title}. This action cannot be undone.`,
        confirmText: 'Remove',
        cancelText: 'Cancel',
    });

    if (!accepted) {
        return;
    }

    router.delete(`/campaigns/${campaign.id}`, {
        preserveScroll: true,
    });
};

const statusTone = (status: string) => {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-200';
        case 'draft':
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-200';
        case 'ended':
            return 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200';
        default:
            return 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200';
    }
};
</script>

<template>
    <Head title="Campaigns" />

    <AppLayout :breadcrumbs="[{ title: 'Campaigns', href: '/campaigns' }]">
        <div class="space-y-6">
            <ResourceToolbar
                title="Campaigns"
                description="Create and manage fundraising campaigns."
                :create-route="canCreate ? '/campaigns/create' : undefined"
                :show-create="canCreate"
            />

            <GlassCard>
                <div class="flex flex-col gap-4">
                    <div
                        class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between"
                    >
                        <div class="relative w-full md:max-w-sm">
                            <Search
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400"
                            />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search campaigns..."
                                class="w-full rounded-lg border border-slate-200 bg-white py-2 pr-3 pl-9 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                @keydown.enter.prevent="apply"
                            />
                        </div>

                        <div class="flex items-center gap-2">
                            <select
                                v-model="perPage"
                                class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                @change="apply"
                            >
                                <option :value="5">5</option>
                                <option :value="10">10</option>
                                <option :value="25">25</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-slate-200 dark:divide-slate-800"
                        >
                            <thead>
                                <tr
                                    class="text-left text-xs font-semibold tracking-wide text-slate-500 uppercase"
                                >
                                    <th class="px-4 py-3">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-2"
                                            @click="toggleSort('title')"
                                        >
                                            <span>Title</span>
                                        </button>
                                    </th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Goal</th>
                                    <th class="px-4 py-3">Dates</th>
                                    <th class="px-4 py-3 text-right">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-100 text-sm dark:divide-slate-800"
                            >
                                <tr v-if="!hasResults">
                                    <td
                                        class="px-4 py-6 text-center text-slate-500"
                                        colspan="5"
                                    >
                                        No campaigns found.
                                    </td>
                                </tr>

                                <tr
                                    v-for="campaign in campaignList"
                                    :key="campaign.id"
                                >
                                    <td class="px-4 py-3">
                                        <div
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ campaign.title }}
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            {{ campaign.slug }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-medium"
                                            :class="statusTone(campaign.status)"
                                        >
                                            {{ campaign.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span v-if="campaign.goal_amount">
                                            {{ campaign.goal_amount }}
                                            {{ campaign.goal_currency ?? '' }}
                                        </span>
                                        <span v-else class="text-slate-500"
                                            >-</span
                                        >
                                    </td>
                                    <td
                                        class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300"
                                    >
                                        <div>
                                            Start:
                                            {{ campaign.starts_at ?? '-' }}
                                        </div>
                                        <div>
                                            End: {{ campaign.ends_at ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div
                                            class="flex items-center justify-end gap-2"
                                        >
                                            <Link
                                                class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"
                                                :href="
                                                    route(
                                                        'campaigns.show',
                                                        campaign.id,
                                                    )
                                                "
                                            >
                                                <Eye class="size-4" />
                                                <span>View</span>
                                            </Link>

                                            <Link
                                                v-if="canEdit"
                                                class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-50 dark:text-indigo-200 dark:hover:bg-indigo-500/10"
                                                :href="
                                                    route(
                                                        'campaigns.edit',
                                                        campaign.id,
                                                    )
                                                "
                                            >
                                                <Edit3 class="size-4" />
                                                <span>Edit</span>
                                            </Link>

                                            <button
                                                v-if="canDelete"
                                                type="button"
                                                class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-50 dark:text-red-200 dark:hover:bg-red-500/10"
                                                @click="destroy(campaign)"
                                            >
                                                <Trash2 class="size-4" />
                                                <span>Delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <Pagination :links="paginationLinks" />
                </div>
            </GlassCard>
        </div>
    </AppLayout>
</template>
