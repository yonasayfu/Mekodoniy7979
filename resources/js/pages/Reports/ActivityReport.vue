<script setup lang="ts">
import GlassCard from '@/components/GlassCard.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Activity, ArrowLeft, Calendar, Download } from 'lucide-vue-next';

const props = defineProps<{
    activities: any[];
    filters: {
        branch_id?: string;
        date_range?: string;
    };
}>();

const exportActivities = () => {
    const csv =
        'Description,Event Type,User,Elder,Date,Time Ago\n' +
        props.activities
            .map(
                (activity) =>
                    `"${activity.description}","${activity.event_type}","${activity.user_name || ''}","${activity.elder_name || ''}","${activity.created_at}","${activity.time_ago}"`,
            )
            .join('\n');

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'activity_report.csv';
    a.click();
    window.URL.revokeObjectURL(url);
};

const getEventTypeColor = (type: string) => {
    switch (type?.toLowerCase()) {
        case 'pledge_created':
        case 'donation':
            return 'bg-green-100 text-green-800';
        case 'visit':
        case 'check_in':
            return 'bg-blue-100 text-blue-800';
        case 'pledge_updated':
        case 'profile_updated':
            return 'bg-yellow-100 text-yellow-800';
        case 'pledge_cancelled':
        case 'issue':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-slate-100 text-slate-800';
    }
};
</script>

<template>
    <Head title="Activity Report - Mekodonia Reports" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Reports', href: '/reports' },
            { title: 'Admin Dashboard', href: '/reports' },
            { title: 'Activity Report' },
        ]"
    >
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button
                        variant="outline"
                        size="sm"
                        @click="$inertia.visit('/reports')"
                    >
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Dashboard
                    </Button>
                    <div>
                        <h1
                            class="text-3xl font-bold text-slate-900 dark:text-slate-100"
                        >
                            Activity Report
                        </h1>
                        <p class="mt-1 text-slate-600 dark:text-slate-400">
                            Recent system activities and timeline events
                        </p>
                    </div>
                </div>
                <Button @click="exportActivities" variant="outline" size="sm">
                    <Download class="mr-2 h-4 w-4" />
                    Export CSV
                </Button>
            </div>

            <!-- Summary -->
            <GlassCard>
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <Activity class="h-8 w-8 text-blue-500" />
                        <div>
                            <h3
                                class="text-lg font-semibold text-slate-900 dark:text-slate-100"
                            >
                                {{ activities.length }} Activities
                            </h3>
                            <p class="text-slate-600 dark:text-slate-400">
                                Recent system activities and events
                            </p>
                        </div>
                    </div>
                </div>
            </GlassCard>

            <!-- Activity Timeline -->
            <GlassCard>
                <div class="p-6">
                    <h3
                        class="mb-6 text-lg font-semibold text-slate-900 dark:text-slate-100"
                    >
                        Activity Timeline
                    </h3>

                    <div v-if="activities.length > 0" class="space-y-4">
                        <div
                            v-for="activity in activities"
                            :key="activity.id"
                            class="flex gap-4 rounded-lg bg-slate-50 p-4 dark:bg-slate-800"
                        >
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/50"
                                >
                                    <Activity
                                        class="h-5 w-5 text-blue-600 dark:text-blue-400"
                                    />
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="mb-1 flex items-center gap-2">
                                    <Badge
                                        :class="
                                            getEventTypeColor(
                                                activity.event_type,
                                            )
                                        "
                                    >
                                        {{ activity.event_type || 'Activity' }}
                                    </Badge>
                                    <span
                                        class="flex items-center gap-1 text-sm text-slate-500"
                                    >
                                        <Calendar class="h-3 w-3" />
                                        {{ activity.time_ago }}
                                    </span>
                                </div>
                                <p
                                    class="mb-1 font-medium text-slate-900 dark:text-slate-100"
                                >
                                    {{ activity.description }}
                                </p>
                                <div
                                    class="flex gap-4 text-sm text-slate-600 dark:text-slate-400"
                                >
                                    <span v-if="activity.user_name">
                                        <strong>User:</strong>
                                        {{ activity.user_name }}
                                    </span>
                                    <span v-if="activity.elder_name">
                                        <strong>Elder:</strong>
                                        {{ activity.elder_name }}
                                    </span>
                                    <span>
                                        <strong>Date:</strong>
                                        {{ activity.created_at }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-12 text-center text-slate-500">
                        <Activity
                            class="mx-auto mb-4 h-16 w-16 text-slate-300"
                        />
                        <p>No activities found for the selected period.</p>
                    </div>
                </div>
            </GlassCard>
        </div>
    </AppLayout>
</template>
