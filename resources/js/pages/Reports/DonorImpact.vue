<script setup lang="ts">
import GlassCard from '@/components/GlassCard.vue';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps<{
    impact: {
        total_donations: number;
        supported_elders_count: number;
        timeline_events: any[];
    };
    filters: {
        range: string;
    };
    annual_reports: any[];
    sponsorships: any[];
}>();

const form = useForm({
    range: props.filters.range,
});

watch(
    () => form.range,
    (newValue) => {
        form.get(route('reports.index'), {
            preserveState: true,
            replace: true,
        });
    },
);

const formattedTotalDonations = computed(() => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'ETB',
    }).format(props.impact.total_donations);
});

const printPage = () => {
    window.print();
};

const downloadReport = (pdfPath: string) => {
    window.open(pdfPath, '_blank');
};
</script>

<template>
    <Head title="My Impact" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Reports', href: '/reports' },
            { title: 'My Impact' },
        ]"
    >
        <div class="space-y-6">
            <div class="no-print">
                <div class="flex items-center justify-between">
                    <div>
                        <h1
                            class="text-2xl font-semibold text-slate-900 dark:text-slate-100"
                        >
                            My Impact
                        </h1>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            Here's a summary of your contributions.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Select v-model="form.range">
                            <SelectTrigger class="w-[180px]">
                                <SelectValue placeholder="Select a range" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="30">Last 30 days</SelectItem>
                                <SelectItem value="90">Last 90 days</SelectItem>
                                <SelectItem value="all">All Time</SelectItem>
                            </SelectContent>
                        </Select>
                        <Button @click="printPage">Print</Button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <GlassCard>
                    <div class="p-6">
                        <h3
                            class="text-sm font-semibold text-slate-600 dark:text-slate-300"
                        >
                            Total Donations
                        </h3>
                        <p
                            class="mt-2 text-3xl font-bold text-slate-900 dark:text-slate-100"
                        >
                            {{ formattedTotalDonations }}
                        </p>
                    </div>
                </GlassCard>
                <GlassCard>
                    <div class="p-6">
                        <h3
                            class="text-sm font-semibold text-slate-600 dark:text-slate-300"
                        >
                            Supported Elders
                        </h3>
                        <p
                            class="mt-2 text-3xl font-bold text-slate-900 dark:text-slate-100"
                        >
                            {{ impact.supported_elders_count }}
                        </p>
                    </div>
                </GlassCard>
            </div>

            <!-- Promise Status -->
            <GlassCard v-if="sponsorships && sponsorships.length > 0">
                <div class="p-6">
                    <h3
                        class="text-lg font-semibold text-slate-900 dark:text-slate-100"
                    >
                        Your Promise Status
                    </h3>
                    <div class="mt-4 space-y-4">
                        <div
                            v-for="sponsorship in sponsorships"
                            :key="sponsorship.id"
                            class="flex items-center justify-between rounded-lg border p-4"
                        >
                            <div>
                                <p class="font-medium capitalize">
                                    {{ sponsorship.elder.first_name }}
                                    {{ sponsorship.elder.last_name }} ({{
                                        sponsorship.relationship_type
                                    }})
                                </p>
                                <p
                                    class="text-sm text-slate-600 dark:text-slate-400"
                                >
                                    Monthly commitment: ${{
                                        sponsorship.amount
                                    }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm">
                                    <span
                                        :class="
                                            sponsorship.promise_kept_last_month
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                    >
                                        {{
                                            sponsorship.promise_kept_last_month
                                                ? '✓ Promise Kept'
                                                : '✗ Payment Missed'
                                        }}
                                    </span>
                                </div>
                                <div
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
                                    {{ sponsorship.consecutive_months_kept }}
                                    consecutive months
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </GlassCard>

            <!-- Annual Impact -->
            <GlassCard v-if="annual_reports && annual_reports.length > 0">
                <div class="p-6">
                    <h3
                        class="text-lg font-semibold text-slate-900 dark:text-slate-100"
                    >
                        Your Annual Impact
                    </h3>
                    <div class="mt-4 space-y-2">
                        <div
                            v-for="report in annual_reports"
                            :key="report.id"
                            class="flex items-center justify-between rounded bg-slate-50 p-3 dark:bg-slate-800"
                        >
                            <span
                                >{{ report.report_year }} Thank You Report</span
                            >
                            <Button
                                @click="downloadReport(report.pdf_path)"
                                size="sm"
                            >
                                Download PDF
                            </Button>
                        </div>
                    </div>
                </div>
            </GlassCard>

            <GlassCard>
                <div class="p-6">
                    <h3
                        class="text-lg font-semibold text-slate-900 dark:text-slate-100"
                    >
                        Recent Activity
                    </h3>
                    <div
                        v-if="impact.timeline_events.length === 0"
                        class="mt-4 text-center text-sm text-slate-500 dark:text-slate-300"
                    >
                        No recent activity to show.
                    </div>
                    <ul v-else class="mt-4 space-y-4">
                        <li
                            v-for="event in impact.timeline_events"
                            :key="event.id"
                            class="flex items-start space-x-4"
                        >
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-500/10 text-indigo-500 dark:bg-indigo-500/20"
                                >
                                    <!-- Icon can be dynamic based on event type -->
                                    <span>🎁</span>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-slate-800 dark:text-slate-100"
                                >
                                    {{ event.type }}
                                </p>
                                <p
                                    class="text-sm text-slate-600 dark:text-slate-400"
                                >
                                    For {{ event.elder.name }} on
                                    {{
                                        new Date(
                                            event.occurred_at,
                                        ).toLocaleDateString()
                                    }}
                                </p>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ event.description }}
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </GlassCard>
        </div>
    </AppLayout>
</template>

<style>
@media print {
    .no-print {
        display: none;
    }
}
</style>
