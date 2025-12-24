<script setup lang="ts">
import GlassCard from '@/components/GlassCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { FileText } from 'lucide-vue-next';
import { computed } from 'vue';

interface ReportSummary {
    name: string;
    description: string;
    route?: string; // Optional route to view the report details
}

const props = defineProps<{
    reports: ReportSummary[];
}>();

const hasReports = computed(() => props.reports.length > 0);
</script>

<template>
    <Head title="Reports" />

    <AppLayout :breadcrumbs="[{ title: 'Reports', href: '/reports' }]">
        <div class="space-y-6">
            <div>
                <h1
                    class="text-2xl font-semibold text-slate-900 dark:text-slate-100"
                >
                    Reports
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    View various reports and insights.
                </p>
            </div>

            <GlassCard>
                <div
                    v-if="!hasReports"
                    class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-300"
                >
                    No reports available yet.
                </div>
                <div
                    v-else
                    class="divide-y divide-slate-200/70 dark:divide-slate-800/60"
                >
                    <div
                        v-for="report in reports"
                        :key="report.name"
                        class="flex items-center gap-3 px-5 py-4 text-sm"
                    >
                        <div
                            class="rounded-lg bg-indigo-500/10 p-2 text-indigo-500 dark:bg-indigo-500/20"
                        >
                            <FileText class="size-5" />
                        </div>
                        <div class="flex-1">
                            <p
                                class="font-semibold text-slate-800 dark:text-slate-100"
                            >
                                {{ report.name }}
                            </p>
                            <p
                                class="text-xs text-slate-500 dark:text-slate-400"
                            >
                                {{ report.description }}
                            </p>
                        </div>
                        <Link
                            v-if="report.route"
                            :href="report.route"
                            class="inline-flex items-center rounded-md p-2 text-indigo-600 transition hover:bg-indigo-50 dark:text-indigo-300 dark:hover:bg-indigo-900/50"
                            :target="
                                report.name === 'Impact Book (Donor)'
                                    ? '_blank'
                                    : '_self'
                            "
                        >
                            View Report
                        </Link>
                    </div>
                </div>
            </GlassCard>
        </div>
    </AppLayout>
</template>
