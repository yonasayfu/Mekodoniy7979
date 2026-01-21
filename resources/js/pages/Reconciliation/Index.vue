<script setup lang="ts">
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

type Summary = {
    count: number;
    total: number;
};

type ImportItem = {
    id: number;
    gateway: string;
    status: string;
    total_rows: number;
    matched_rows: number;
    unmatched_rows: number;
    ignored_rows: number;
    source_filename: string | null;
    branch?: { id: number; name: string } | null;
    uploaded_by?: { id: number; name: string } | null;
    created_at: string | null;
};

const props = defineProps<{
    imports: {
        data: ImportItem[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    summary: {
        matched: Summary;
        unmatched: Summary;
    };
    gateways: Array<{ label: string; value: string }>;
    branches: Array<{ id: number; name: string }>;
}>();

const uploadForm = useForm({
    gateway: props.gateways[0]?.value ?? 'telebirr',
    branch_id: props.branches.length === 1 ? props.branches[0].id : null,
    file: null as File | null,
});

const submitUpload = () => {
    uploadForm.post(route('reconciliation.store'), {
        forceFormData: true,
        onSuccess: () => uploadForm.reset('file'),
    });
};

const formattedTotal = (summary: Summary) =>
    new Intl.NumberFormat(undefined, {
        style: 'currency',
        currency: 'ETB',
        maximumFractionDigits: 2,
    }).format(summary.total ?? 0);

const canChooseBranch = computed(() => props.branches.length > 0);
</script>

<template>
    <Head title="Payment Reconciliation" />

    <AppLayout :breadcrumbs="[{ title: 'Payment Reconciliation', href: route('reconciliation.index') }]">
        <div class="flex flex-1 flex-col gap-6 px-4 py-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-2">
                <GlassCard variant="lite" padding="p-5">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Matched</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                        {{ summary.matched.count }} entries
                    </h2>
                    <p class="text-sm text-slate-500">
                        {{ formattedTotal(summary.matched) }}
                    </p>
                </GlassCard>
                <GlassCard variant="lite" padding="p-5">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Unmatched</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                        {{ summary.unmatched.count }} entries
                    </h2>
                    <p class="text-sm text-slate-500">
                        {{ formattedTotal(summary.unmatched) }}
                    </p>
                </GlassCard>
            </div>

            <GlassCard>
                <div class="flex flex-col gap-4 md:flex-row md:items-end">
                    <div class="flex-1 space-y-3">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Gateway</label
                        >
                        <select
                            v-model="uploadForm.gateway"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                        >
                            <option v-for="gateway in gateways" :key="gateway.value" :value="gateway.value">
                                {{ gateway.label }}
                            </option>
                        </select>
                    </div>

                    <div v-if="canChooseBranch" class="flex-1 space-y-3">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Branch</label
                        >
                        <select
                            v-model="uploadForm.branch_id"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                        >
                            <option :value="null">Auto-detect</option>
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                {{ branch.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex-1 space-y-3">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >CSV File</label
                        >
                        <input
                            type="file"
                            accept=".csv,text/csv"
                            @change="(event) => (uploadForm.file = (event.target as HTMLInputElement).files?.[0] ?? null)"
                            class="w-full rounded-lg border border-dashed border-slate-300 bg-white/70 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"
                        />
                        <InputError v-if="uploadForm.errors.file" :message="uploadForm.errors.file" />
                    </div>

                    <div class="pt-2">
                        <GlassButton
                            variant="primary"
                            class="w-full md:w-auto"
                            :disabled="uploadForm.processing"
                            @click="submitUpload"
                        >
                            <span v-if="uploadForm.processing">Uploading…</span>
                            <span v-else>Upload &amp; reconcile</span>
                        </GlassButton>
                    </div>
                </div>
            </GlassCard>

            <GlassCard variant="lite" padding="p-0">
                <div
                    class="border-b border-slate-200/70 px-5 py-4 text-sm font-semibold text-slate-800 dark:border-slate-800/60 dark:text-slate-100"
                >
                    Recent imports
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/30">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Gateway</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Branch</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Rows</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Matched</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Unmatched</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Uploaded</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-500"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white/70 dark:divide-slate-800/50 dark:bg-slate-900/40">
                            <tr v-for="importItem in imports.data" :key="importItem.id">
                                <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">
                                    {{ importItem.gateway }}
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ importItem.branch?.name ?? 'All branches' }}
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ importItem.total_rows }}</td>
                                <td class="px-4 py-3 text-emerald-600">
                                    {{ importItem.matched_rows }}
                                </td>
                                <td class="px-4 py-3 text-amber-600">
                                    {{ importItem.unmatched_rows }}
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ importItem.created_at ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <Link
                                        :href="route('reconciliation.show', importItem.id)"
                                        class="text-indigo-600 hover:underline"
                                    >
                                        Review
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!imports.data.length">
                                <td colspan="7" class="px-4 py-6 text-center text-slate-500">
                                    No imports yet. Upload a CSV to get started.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination v-if="imports.links.length > 1" :links="imports.links" class="px-5 py-4" />
            </GlassCard>
        </div>
    </AppLayout>
</template>
