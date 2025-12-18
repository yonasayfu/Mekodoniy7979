<script setup lang="ts">
import GlassCard from '@/components/GlassCard.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    ArrowLeft,
    DollarSign,
    Download,
    Heart,
    TrendingUp,
    Users,
} from 'lucide-vue-next';

const props = defineProps<{
    metric: string;
    data: any;
    filters: {
        branch_id?: string;
        date_range?: string;
    };
}>();

const getMetricTitle = () => {
    switch (props.metric) {
        case 'promise_fulfillment':
            return 'Promise Fulfillment Details';
        case 'missed_payments':
            return 'Missed Payments Details';
        case 'guest_donations':
            return 'Guest Donations Details';
        case 'monthly_expenses':
            return 'Monthly Expenses Details';
        default:
            return 'Report Details';
    }
};

const getMetricIcon = () => {
    switch (props.metric) {
        case 'promise_fulfillment':
            return TrendingUp;
        case 'missed_payments':
            return Heart;
        case 'guest_donations':
            return DollarSign;
        case 'monthly_expenses':
            return Users;
        default:
            return TrendingUp;
    }
};

const exportData = () => {
    // Simple CSV export for now
    const data = props.data;
    let csv = '';

    if (props.metric === 'promise_fulfillment') {
        csv = 'Donor Name,Elder Name,Amount,Relationship,Months Supported\n';
        data.fulfilled_sponsorships.forEach((sponsorship: any) => {
            csv += `${sponsorship.donor_name},${sponsorship.elder_name},${sponsorship.amount},${sponsorship.relationship},${sponsorship.months_supported}\n`;
        });
    } else if (props.metric === 'missed_payments') {
        csv =
            'Donor Name,Elder Name,Amount,Relationship,Last Payment,Days Overdue\n';
        data.missed_sponsorships.forEach((sponsorship: any) => {
            csv += `${sponsorship.donor_name},${sponsorship.elder_name},${sponsorship.amount},${sponsorship.relationship},${sponsorship.last_payment || 'N/A'},${sponsorship.days_overdue || 'N/A'}\n`;
        });
    } else if (props.metric === 'guest_donations') {
        csv = 'Amount,Type,Date,Notes\n';
        data.donations.forEach((donation: any) => {
            csv += `${donation.amount},${donation.donation_type},${donation.created_at},${donation.notes || ''}\n`;
        });
    }

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${props.metric}_report.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
};
</script>

<template>
    <Head :title="getMetricTitle() + ' - Mekodonia Reports'" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Reports', href: '/reports' },
            { title: 'Admin Dashboard', href: '/reports' },
            { title: getMetricTitle() },
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
                            {{ getMetricTitle() }}
                        </h1>
                        <p class="mt-1 text-slate-600 dark:text-slate-400">
                            Detailed breakdown and analysis
                        </p>
                    </div>
                </div>
                <Button @click="exportData" variant="outline" size="sm">
                    <Download class="mr-2 h-4 w-4" />
                    Export CSV
                </Button>
            </div>

            <!-- Summary Cards -->
            <div
                v-if="metric === 'promise_fulfillment'"
                class="grid grid-cols-1 gap-6 md:grid-cols-4"
            >
                <GlassCard>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium text-slate-600 dark:text-slate-400"
                                >
                                    Total Sponsorships
                                </p>
                                <p
                                    class="mt-1 text-3xl font-bold text-slate-900 dark:text-slate-100"
                                >
                                    {{ data.total_sponsorships }}
                                </p>
                            </div>
                            <Users class="h-8 w-8 text-blue-500" />
                        </div>
                    </div>
                </GlassCard>

                <GlassCard>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium text-slate-600 dark:text-slate-400"
                                >
                                    Fulfilled
                                </p>
                                <p
                                    class="mt-1 text-3xl font-bold text-green-600"
                                >
                                    {{ data.fulfilled_count }}
                                </p>
                            </div>
                            <TrendingUp class="h-8 w-8 text-green-500" />
                        </div>
                    </div>
                </GlassCard>

                <GlassCard>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium text-slate-600 dark:text-slate-400"
                                >
                                    Missed
                                </p>
                                <p class="mt-1 text-3xl font-bold text-red-600">
                                    {{ data.missed_count }}
                                </p>
                            </div>
                            <Heart class="h-8 w-8 text-red-500" />
                        </div>
                    </div>
                </GlassCard>

                <GlassCard>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium text-slate-600 dark:text-slate-400"
                                >
                                    Fulfillment Rate
                                </p>
                                <p
                                    class="mt-1 text-3xl font-bold text-blue-600"
                                >
                                    {{ Math.round(data.fulfillment_rate) }}%
                                </p>
                            </div>
                            <TrendingUp class="h-8 w-8 text-blue-500" />
                        </div>
                    </div>
                </GlassCard>
            </div>

            <!-- Detailed Data -->
            <GlassCard v-if="metric === 'promise_fulfillment'">
                <div class="p-6">
                    <h3
                        class="mb-4 text-lg font-semibold text-slate-900 dark:text-slate-100"
                    >
                        Fulfilled Sponsorships
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="sponsorship in data.fulfilled_sponsorships"
                            :key="sponsorship.id"
                            class="flex items-center justify-between rounded-lg bg-green-50 p-4 dark:bg-green-900/20"
                        >
                            <div>
                                <p class="font-medium">
                                    {{ sponsorship.donor_name }} →
                                    {{ sponsorship.elder_name }}
                                </p>
                                <p
                                    class="text-sm text-slate-600 dark:text-slate-400"
                                >
                                    {{ sponsorship.relationship }} • ${{
                                        sponsorship.amount
                                    }}/month •
                                    {{ sponsorship.months_supported }} months
                                </p>
                            </div>
                            <Badge
                                variant="secondary"
                                class="bg-green-100 text-green-800"
                                >Fulfilled</Badge
                            >
                        </div>
                    </div>
                </div>
            </GlassCard>

            <GlassCard v-if="metric === 'missed_payments'">
                <div class="p-6">
                    <h3
                        class="mb-4 text-lg font-semibold text-slate-900 dark:text-slate-100"
                    >
                        Missed Payments
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="sponsorship in data.missed_sponsorships"
                            :key="sponsorship.id"
                            class="flex items-center justify-between rounded-lg bg-red-50 p-4 dark:bg-red-900/20"
                        >
                            <div>
                                <p class="font-medium">
                                    {{ sponsorship.donor_name }} →
                                    {{ sponsorship.elder_name }}
                                </p>
                                <p
                                    class="text-sm text-slate-600 dark:text-slate-400"
                                >
                                    ${{ sponsorship.amount }}/month • Last payment:
                                    {{ sponsorship.last_payment || 'Never' }}
                                    <span
                                        v-if="sponsorship.days_overdue"
                                        class="font-medium text-red-600"
                                    >
                                        • {{ sponsorship.days_overdue }} days overdue
                                    </span>
                                </p>
                            </div>
                            <Badge variant="destructive">Missed</Badge>
                        </div>
                    </div>
                </div>
            </GlassCard>

            <GlassCard v-if="metric === 'guest_donations'">
                <div class="p-6">
                    <h3
                        class="mb-4 text-lg font-semibold text-slate-900 dark:text-slate-100"
                    >
                        Guest Donations
                    </h3>
                    <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div class="text-center">
                            <p
                                class="text-2xl font-bold text-slate-900 dark:text-slate-100"
                            >
                                {{ data.total_donations }}
                            </p>
                            <p
                                class="text-sm text-slate-600 dark:text-slate-400"
                            >
                                Total Donations
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">
                                ${{ data.total_amount }}
                            </p>
                            <p
                                class="text-sm text-slate-600 dark:text-slate-400"
                            >
                                Total Amount
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">
                                ${{ Math.round(data.average_donation) }}
                            </p>
                            <p
                                class="text-sm text-slate-600 dark:text-slate-400"
                            >
                                Average Donation
                            </p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div
                            v-for="donation in data.donations"
                            :key="donation.id"
                            class="flex items-center justify-between rounded-lg bg-slate-50 p-4 dark:bg-slate-800"
                        >
                            <div>
                                <p class="font-medium">
                                    ${{ donation.amount }}
                                </p>
                                <p
                                    class="text-sm text-slate-600 dark:text-slate-400"
                                >
                                    {{ donation.donation_type }} •
                                    {{ donation.created_at }}
                                </p>
                                <p
                                    v-if="donation.notes"
                                    class="text-sm text-slate-500"
                                >
                                    {{ donation.notes }}
                                </p>
                            </div>
                            <Badge variant="outline">{{
                                donation.donation_type
                            }}</Badge>
                        </div>
                    </div>
                </div>
            </GlassCard>

            <GlassCard v-if="metric === 'monthly_expenses'">
                <div class="p-6">
                    <h3
                        class="mb-4 text-lg font-semibold text-slate-900 dark:text-slate-100"
                    >
                        Monthly Expenses Overview
                    </h3>
                    <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div class="text-center">
                            <p
                                class="text-2xl font-bold text-slate-900 dark:text-slate-100"
                            >
                                {{ data.total_sponsorships }}
                            </p>
                            <p
                                class="text-sm text-slate-600 dark:text-slate-400"
                            >
                                Active Sponsorships
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">
                                ${{ data.total_monthly_amount }}
                            </p>
                            <p
                                class="text-sm text-slate-600 dark:text-slate-400"
                            >
                                Monthly Total
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">
                                ${{ Math.round(data.average_sponsorship_amount) }}
                            </p>
                            <p
                                class="text-sm text-slate-600 dark:text-slate-400"
                            >
                                Average Sponsorship
                            </p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="mb-3 font-medium">By Relationship Type</h4>
                        <div class="space-y-2">
                            <div
                                v-for="(stats, type) in data.by_relationship"
                                :key="type"
                                class="flex items-center justify-between rounded bg-slate-50 p-3 dark:bg-slate-800"
                            >
                                <span class="capitalize">{{ type }}</span>
                                <div class="text-right">
                                    <span class="font-medium"
                                        >{{ stats.count }} sponsorships</span
                                    >
                                    <span
                                        class="ml-2 text-sm text-slate-600 dark:text-slate-400"
                                    >
                                        ${{ stats.total_amount }}/month
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="mb-3 font-medium">Top Donors</h4>
                        <div class="space-y-2">
                            <div
                                v-for="(donor, index) in data.top_donors"
                                :key="index"
                                class="flex items-center justify-between rounded bg-slate-50 p-3 dark:bg-slate-800"
                            >
                                <span>{{ donor.name }}</span>
                                <div class="text-right">
                                    <span class="font-medium"
                                        >${{ donor.total_amount }}/month</span
                                    >
                                    <span
                                        class="ml-2 text-sm text-slate-600 dark:text-slate-400"
                                    >
                                        {{ donor.sponsorship_count }} sponsorships
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </GlassCard>
        </div>
    </AppLayout>
</template>
