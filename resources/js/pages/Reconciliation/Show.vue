<script setup lang="ts">
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import { route } from 'ziggy-js';

type ImportDetails = {
    id: number;
    gateway: string;
    status: string;
    total_rows: number;
    matched_rows: number;
    unmatched_rows: number;
    ignored_rows: number;
    source_filename: string | null;
    branch: { id: number; name: string } | null;
    uploaded_by?: { id: number; name: string } | null;
    created_at: string | null;
};

type ReconciliationItem = {
    id: number;
    status: string;
    reference: string | null;
    payer_name: string | null;
    payer_phone: string | null;
    amount: number | null;
    currency: string | null;
    paid_at: string | null;
    donation: {
        id: number;
        receipt_uuid: string;
        amount: number;
    } | null;
    elder: { id: number; name: string } | null;
    match_strategy: string | null;
    notes: string | null;
};

const props = defineProps<{
    reconciliationImport: ImportDetails;
    items: {
        data: ReconciliationItem[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: {
        status?: string | null;
    };
    statusCounts: Record<string, number>;
}>();

const statusOptions = computed(() => [
    { key: null, label: 'All' },
    { key: 'unmatched', label: `Unmatched (${props.statusCounts.unmatched ?? 0})` },
    { key: 'matched', label: `Matched (${props.statusCounts.matched ?? 0})` },
    { key: 'ignored', label: `Ignored (${props.statusCounts.ignored ?? 0})` },
]);

const filterByStatus = (status: string | null) => {
    router.get(
        route('reconciliation.show', props.reconciliationImport.id),
        { status },
        { preserveScroll: true, preserveState: true },
    );
};

const matchForms = reactive<Record<number, ReturnType<typeof useForm>>>({});

const formFor = (itemId: number) => {
    if (!matchForms[itemId]) {
        matchForms[itemId] = useForm({
            donation_reference: '',
        });
    }

    return matchForms[itemId];
};

const submitMatch = (itemId: number) => {
    const form = formFor(itemId);
    form.post(route('reconciliation.items.match', { import: props.reconciliationImport.id, item: itemId }), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const ignoreItem = (itemId: number) => {
    router.post(
        route('reconciliation.items.ignore', { import: props.reconciliationImport.id, item: itemId }),
        {},
        { preserveScroll: true },
    );
};

const statusPillClass = (status: string) => {
    switch (status) {
        case 'matched':
            return 'bg-emerald-100 text-emerald-700';
        case 'unmatched':
            return 'bg-amber-100 text-amber-700';
        case 'ignored':
            return 'bg-slate-200 text-slate-600';
        default:
            return 'bg-slate-200 text-slate-600';
    }
};
</script>

<template>
    <Head :title="`Reconciliation – Import #${reconciliationImport.id}`" />

            <AppLayout
                :breadcrumbs="[
                    { title: 'Reconciliation', href: route('reconciliation.index') },
                    { title: `Import ${reconciliationImport.id}`, href: route('reconciliation.show', reconciliationImport.id) },
                ]"
            >
        <div class="flex flex-1 flex-col gap-6 px-4 py-6 lg:px-8">
            <GlassCard>
                <div class="grid gap-4 md:grid-cols-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Gateway</p>
                        <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            {{ reconciliationImport.gateway }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Branch</p>
                        <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            {{ reconciliationImport.branch?.name ?? 'All' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Uploaded by</p>
                        <p class="text-sm text-slate-700 dark:text-slate-200">
                            {{ reconciliationImport.uploaded_by?.name ?? 'Unknown' }}
                        </p>
                        <p class="text-xs text-slate-500">
                                    {{ reconciliationImport.created_at ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Rows</p>
                        <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            {{ reconciliationImport.matched_rows }} matched / {{ reconciliationImport.unmatched_rows }} unmatched
                        </p>
                    </div>
                </div>
            </GlassCard>

            <div class="flex flex-wrap gap-2">
                <button
                    v-for="option in statusOptions"
                    :key="option.key ?? 'all'"
                    type="button"
                    class="rounded-full border px-4 py-1 text-sm"
                    :class="
                        props.filters.status === option.key
                            ? 'border-indigo-500 bg-indigo-50 text-indigo-600 dark:border-indigo-400 dark:bg-indigo-500/10 dark:text-indigo-200'
                            : 'border-slate-200 text-slate-600 dark:border-slate-700 dark:text-slate-300'
                    "
                    @click="filterByStatus(option.key)"
                >
                    {{ option.label }}
                </button>
            </div>

            <GlassCard variant="lite" padding="p-0">
                <div
                    class="border-b border-slate-200/70 px-5 py-4 text-sm font-semibold text-slate-800 dark:border-slate-800/60 dark:text-slate-100"
                >
                    Entries
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/20">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Status</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Reference</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Amount</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Paid at</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Donation</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white/70 dark:divide-slate-800/50 dark:bg-slate-900/40">
                            <tr v-for="item in items.data" :key="item.id">
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="statusPillClass(item.status)"
                                    >
                                        {{ item.status }}
                                    </span>
                                    <p v-if="item.match_strategy" class="mt-1 text-xs text-slate-500">
                                        via {{ item.match_strategy }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-900 dark:text-slate-100">
                                        {{ item.reference ?? '—' }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ item.payer_name ?? 'Unknown payer' }}
                                    </p>
                                    <p v-if="item.payer_phone" class="text-xs text-slate-500">
                                        {{ item.payer_phone }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-slate-900 dark:text-slate-100">
                                    {{ item.amount ?? 0 }} {{ item.currency ?? 'ETB' }}
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ item.paid_at ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div v-if="item.donation" class="space-y-1">
                                        <p class="font-semibold text-slate-900 dark:text-slate-100">
                                            Donation #{{ item.donation.id }}
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            Receipt: {{ item.donation.receipt_uuid }}
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            Amount: {{ item.donation.amount }} ETB
                                        </p>
                                    </div>
                                    <p v-else class="text-sm text-slate-500">Not linked</p>
                                </td>
                                <td class="px-4 py-3">
                                    <div v-if="item.status === 'unmatched'" class="space-y-2">
                                        <input
                                            v-model="formFor(item.id).donation_reference"
                                            type="text"
                                            placeholder="Donation ID or receipt"
                                            class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-900"
                                        />
                                        <InputError
                                            v-if="formFor(item.id).errors.donation_reference"
                                            :message="formFor(item.id).errors.donation_reference"
                                        />
                                        <div class="flex gap-2">
                                            <GlassButton
                                                size="sm"
                                                variant="primary"
                                                :disabled="formFor(item.id).processing"
                                                @click="submitMatch(item.id)"
                                            >
                                                <span v-if="formFor(item.id).processing">Linking…</span>
                                                <span v-else>Link donation</span>
                                            </GlassButton>
                                            <GlassButton size="sm" variant="secondary" @click="ignoreItem(item.id)">
                                                Ignore
                                            </GlassButton>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <p class="text-xs text-slate-500">
                                            {{ item.elder?.name ?? '' }}
                                        </p>
                                        <p v-if="item.notes" class="text-xs text-slate-500">
                                            {{ item.notes }}
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!items.data.length">
                                <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                                    No reconciliation entries for this import.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination v-if="items.links.length > 1" :links="items.links" class="px-5 py-4" />
            </GlassCard>
        </div>
    </AppLayout>
</template>
