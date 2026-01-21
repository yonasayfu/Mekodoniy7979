<script setup lang="ts">
import GlassCard from '@/components/GlassCard.vue';
import Pagination from '@/components/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    messages: {
        data: Array<{
            id: number;
            channel: string;
            to: string;
            subject?: string | null;
            status: string;
            attempts: number;
            sent_at?: string | null;
            failed_at?: string | null;
            error_message?: string | null;
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: {
        channel?: string | null;
        status?: string | null;
    };
    channels: string[];
    statuses: string[];
}>();

const filterBy = (key: 'channel' | 'status', value: string | null) => {
    router.get(
        route('outbound.index'),
        {
            ...props.filters,
            [key]: value ?? undefined,
        },
        { preserveState: true, preserveScroll: true },
    );
};

const statusColor = (status: string) => {
    switch (status) {
        case 'sent':
        case 'delivered':
            return 'text-emerald-600';
        case 'failed':
            return 'text-red-600';
        case 'pending':
            return 'text-slate-500';
        default:
            return 'text-amber-600';
    }
};
</script>

<template>
    <Head title="Outbound Messages" />

    <AppLayout :breadcrumbs="[{ title: 'Outbound Messages', href: route('outbound.index') }]">
        <div class="flex flex-1 flex-col gap-6 px-4 py-6 lg:px-8">
            <GlassCard variant="lite" padding="p-5">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Channel</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button
                                v-for="channel in ['all', ...channels]"
                                :key="channel"
                                type="button"
                                class="rounded-full border px-3 py-1 text-sm"
                                :class="
                                    (filters.channel ?? 'all') === (channel === 'all' ? 'all' : channel)
                                        ? 'border-indigo-500 bg-indigo-50 text-indigo-600'
                                        : 'border-slate-200 text-slate-600'
                                "
                                @click="filterBy('channel', channel === 'all' ? null : channel)"
                            >
                                {{ channel === 'all' ? 'All' : channel }}
                            </button>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Status</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button
                                v-for="status in ['all', ...statuses]"
                                :key="status"
                                type="button"
                                class="rounded-full border px-3 py-1 text-sm"
                                :class="
                                    (filters.status ?? 'all') === (status === 'all' ? 'all' : status)
                                        ? 'border-indigo-500 bg-indigo-50 text-indigo-600'
                                        : 'border-slate-200 text-slate-600'
                                "
                                @click="filterBy('status', status === 'all' ? null : status)"
                            >
                                {{ status === 'all' ? 'All' : status }}
                            </button>
                        </div>
                    </div>
                </div>
            </GlassCard>

            <GlassCard variant="lite" padding="p-0">
                <div
                    class="border-b border-slate-200/70 px-5 py-4 text-sm font-semibold text-slate-800 dark:border-slate-800/60 dark:text-slate-100"
                >
                    Message Log
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
                        <thead class="bg-slate-50/80 dark:bg-slate-900/30">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Channel</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Recipient</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Subject / Preview</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Status</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Attempts</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Sent At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white/70 dark:divide-slate-800/40 dark:bg-slate-900/40">
                            <tr v-for="message in messages.data" :key="message.id">
                                <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-100">
                                    {{ message.channel }}
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ message.to }}
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-900 dark:text-slate-100">
                                        {{ message.subject ?? '—' }}
                                    </p>
                                    <p v-if="message.error_message" class="text-xs text-red-500">
                                        {{ message.error_message }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <span :class="['font-semibold', statusColor(message.status)]">
                                        {{ message.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ message.attempts }}
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ message.sent_at ?? '—' }}
                                </td>
                            </tr>
                            <tr v-if="!messages.data.length">
                                <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                                    No outbound messages found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination v-if="messages.links.length > 1" :links="messages.links" class="px-5 py-4" />
            </GlassCard>
        </div>
    </AppLayout>
</template>
