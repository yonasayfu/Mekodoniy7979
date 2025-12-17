<script setup lang="ts">
import GlassCard from '@/components/GlassCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps<{
    stats: {
        relationship_distribution: Record<string, number>;
        promise_fulfillment_rate: number;
        missed_payments: number;
        featured_matches: any[];
        guest_donations_today: number;
        monthly_expenses_covered: number;
        total_pledges: number;
        total_elders: number;
        total_donors: number;
        recent_activity: any[];
    };
    filters: {
        branch_id?: string;
        date_range?: string;
    };
    branches: any[];
}>();

const currentSlide = ref(0);
const selectedBranch = ref(props.filters.branch_id || '');
const selectedRange = ref(props.filters.date_range || '30');

const nextSlide = () => {
    currentSlide.value =
        (currentSlide.value + 1) % (props.stats.featured_matches.length || 1);
};

const applyFilters = () => {
    router.get(
        '/reports',
        {
            branch_id: selectedBranch.value,
            date_range: selectedRange.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const exportReport = (format: string) => {
    window.open(
        `/reports/export?format=${format}&branch_id=${selectedBranch.value}&date_range=${selectedRange.value}`,
        '_blank',
    );
};

const printReport = () => {
    window.print();
};

onMounted(() => {
    if (
        props.stats.featured_matches &&
        props.stats.featured_matches.length > 1
    ) {
        setInterval(nextSlide, 5000);
    }
});
</script>

<template>
    <Head title="Admin Dashboard - Mekodonia Reports" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Reports', href: '/reports' },
            { title: 'Admin Dashboard' },
        ]"
    >
        <div class="space-y-6">
            <!-- Header with Filters and Actions -->
            <div
                class="no-print flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
            >
                <div>
                    <h1
                        class="text-3xl font-bold text-slate-900 dark:text-slate-100"
                    >
                        Mekodonia Impact Dashboard
                    </h1>
                    <p class="mt-1 text-slate-600 dark:text-slate-400">
                        Comprehensive overview of our charity operations and
                        donor relationships
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Button @click="printReport" variant="outline" size="sm">
                        <Download class="mr-2 h-4 w-4" />
                        Print Report
                    </Button>
                    <Button
                        @click="exportReport('pdf')"
                        variant="outline"
                        size="sm"
                    >
                        <Download class="mr-2 h-4 w-4" />
                        Export PDF
                    </Button>
                    <Button
                        @click="exportReport('excel')"
                        variant="outline"
                        size="sm"
                    >
                        <Download class="mr-2 h-4 w-4" />
                        Export Excel
                    </Button>
                </div>
            </div>

            <!-- Filters -->
            <GlassCard class="no-print">
                <div class="p-4">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <Filter class="h-4 w-4" />
                            <span class="font-medium">Filters:</span>
                        </div>
                        <Select v-model="selectedBranch">
                            <SelectTrigger class="w-48">
                                <SelectValue placeholder="All Branches" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="">All Branches</SelectItem>
                                <SelectItem
                                    v-for="branch in branches"
                                    :key="branch.id"
                                    :value="branch.id"
                                >
                                    {{ branch.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <Select v-model="selectedRange">
                            <SelectTrigger class="w-32">
                                <SelectValue placeholder="30 days" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="7">7 days</SelectItem>
                                <SelectItem value="30">30 days</SelectItem>
                                <SelectItem value="90">90 days</SelectItem>
                                <SelectItem value="365">1 year</SelectItem>
                            </SelectContent>
                        </Select>
                        <Button @click="applyFilters" size="sm">
                            Apply Filters
                        </Button>
                    </div>
                </div>
            </GlassCard>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <Link href="/reports/detailed?metric=promise_fulfillment">
                    <GlassCard
                        class="cursor-pointer transition-shadow hover:shadow-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="text-sm font-medium text-slate-600 dark:text-slate-400"
                                    >
                                        Promise Fulfillment
                                    </p>
                                    <p
                                        class="mt-1 text-3xl font-bold text-green-600"
                                    >
                                        {{
                                            Math.round(
                                                props.stats
                                                    .promise_fulfillment_rate,
                                            )
                                        }}%
                                    </p>
                                </div>
                                <TrendingUp class="h-8 w-8 text-green-500" />
                            </div>
                            <p class="mt-2 text-xs text-slate-500">
                                Click for detailed breakdown
                            </p>
                        </div>
                    </GlassCard>
                </Link>

                <Link href="/reports/detailed?metric=missed_payments">
                    <GlassCard
                        class="cursor-pointer transition-shadow hover:shadow-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="text-sm font-medium text-slate-600 dark:text-slate-400"
                                    >
                                        Missed Payments
                                    </p>
                                    <p
                                        class="mt-1 text-3xl font-bold text-red-600"
                                    >
                                        {{ props.stats.missed_payments }}
                                    </p>
                                </div>
                                <Heart class="h-8 w-8 text-red-500" />
                            </div>
                            <p class="mt-2 text-xs text-slate-500">
                                Click for follow-up actions
                            </p>
                        </div>
                    </GlassCard>
                </Link>

                <Link href="/reports/detailed?metric=guest_donations">
                    <GlassCard
                        class="cursor-pointer transition-shadow hover:shadow-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="text-sm font-medium text-slate-600 dark:text-slate-400"
                                    >
                                        Guest Donations
                                    </p>
                                    <p
                                        class="mt-1 text-3xl font-bold text-blue-600"
                                    >
                                        ${{ props.stats.guest_donations_today }}
                                    </p>
                                </div>
                                <DollarSign class="h-8 w-8 text-blue-500" />
                            </div>
                            <p class="mt-2 text-xs text-slate-500">
                                Today's anonymous contributions
                            </p>
                        </div>
                    </GlassCard>
                </Link>

                <Link href="/reports/detailed?metric=monthly_expenses">
                    <GlassCard
                        class="cursor-pointer transition-shadow hover:shadow-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="text-sm font-medium text-slate-600 dark:text-slate-400"
                                    >
                                        Monthly Support
                                    </p>
                                    <p
                                        class="mt-1 text-3xl font-bold text-purple-600"
                                    >
                                        ${{
                                            props.stats.monthly_expenses_covered
                                        }}
                                    </p>
                                </div>
                                <Users class="h-8 w-8 text-purple-500" />
                            </div>
                            <p class="mt-2 text-xs text-slate-500">
                                Total pledged monthly
                            </p>
                        </div>
                    </GlassCard>
                </Link>
            </div>

            <!-- Relationship Distribution -->
            <GlassCard>
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3
                            class="text-lg font-semibold text-slate-900 dark:text-slate-100"
                        >
                            Relationship Distribution
                        </h3>
                        <Badge variant="secondary"
                            >{{
                                Object.keys(
                                    props.stats.relationship_distribution,
                                ).length
                            }}
                            types</Badge
                        >
                    </div>
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                        <div
                            v-for="(count, type) in props.stats
                                .relationship_distribution"
                            :key="type"
                            class="rounded-lg bg-gradient-to-br from-blue-50 to-indigo-50 p-4 text-center dark:from-blue-900/20 dark:to-indigo-900/20"
                        >
                            <div
                                class="text-2xl font-bold text-slate-900 capitalize dark:text-slate-100"
                            >
                                {{ type }}
                            </div>
                            <div
                                class="text-sm text-slate-600 dark:text-slate-400"
                            >
                                {{ count }} relationships
                            </div>
                            <div
                                class="mt-2 h-2 w-full rounded-full bg-slate-200 dark:bg-slate-700"
                            >
                                <div
                                    class="h-2 rounded-full bg-blue-500"
                                    :style="{
                                        width: `${(count / Math.max(...Object.values(props.stats.relationship_distribution))) * 100}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div
                        v-if="
                            Object.keys(props.stats.relationship_distribution)
                                .length === 0
                        "
                        class="py-8 text-center text-slate-500"
                    >
                        No relationship data available yet. Elders need to be
                        assigned relationship types.
                    </div>
                </div>
            </GlassCard>

            <!-- Wall of Love - Featured Matches -->
            <GlassCard>
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3
                            class="text-lg font-semibold text-slate-900 dark:text-slate-100"
                        >
                            Wall of Love - Success Stories
                        </h3>
                        <Badge variant="outline"
                            >{{ props.stats.featured_matches.length }} featured
                            matches</Badge
                        >
                    </div>
                    <div
                        v-if="props.stats.featured_matches.length > 0"
                        class="relative overflow-hidden rounded-lg"
                    >
                        <div
                            class="flex transition-transform duration-500"
                            :style="{
                                transform: `translateX(-${currentSlide * 100}%)`,
                            }"
                        >
                            <div
                                v-for="match in props.stats.featured_matches"
                                :key="match.id"
                                class="w-full flex-shrink-0 p-6"
                            >
                                <div
                                    class="rounded-xl bg-gradient-to-r from-rose-50 via-pink-50 to-purple-50 p-8 text-center dark:from-rose-900/20 dark:via-pink-900/20 dark:to-purple-900/20"
                                >
                                    <div class="mb-4 text-6xl">💝</div>
                                    <p
                                        class="mb-2 text-xl font-medium text-slate-900 dark:text-slate-100"
                                    >
                                        {{ match.story }}
                                    </p>
                                    <p
                                        class="text-sm text-slate-600 dark:text-slate-400"
                                    >
                                        {{ match.donor_name }} &
                                        {{ match.elder_name }}
                                    </p>
                                    <div class="mt-4 flex justify-center gap-2">
                                        <Badge variant="secondary">{{
                                            match.relationship
                                        }}</Badge>
                                        <Badge variant="outline"
                                            >{{
                                                match.months_supported
                                            }}
                                            months</Badge
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Slide indicators -->
                        <div class="mt-4 flex justify-center gap-2">
                            <button
                                v-for="(match, index) in props.stats
                                    .featured_matches"
                                :key="index"
                                @click="currentSlide = index"
                                class="h-2 w-2 rounded-full transition-colors"
                                :class="
                                    currentSlide === index
                                        ? 'bg-blue-500'
                                        : 'bg-slate-300 dark:bg-slate-600'
                                "
                            ></button>
                        </div>
                    </div>
                    <div v-else class="py-12 text-center text-slate-500">
                        <Heart class="mx-auto mb-4 h-16 w-16 text-slate-300" />
                        <p>
                            No featured matches yet. Mark some elders as
                            featured to showcase success stories.
                        </p>
                    </div>
                </div>
            </GlassCard>

            <!-- Recent Activity -->
            <GlassCard>
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3
                            class="text-lg font-semibold text-slate-900 dark:text-slate-100"
                        >
                            Recent Activity
                        </h3>
                        <Link
                            href="/reports/activity"
                            class="text-sm text-blue-600 hover:underline"
                            >View all</Link
                        >
                    </div>
                    <div
                        v-if="props.stats.recent_activity.length > 0"
                        class="space-y-3"
                    >
                        <div
                            v-for="activity in props.stats.recent_activity"
                            :key="activity.id"
                            class="flex items-center gap-3 rounded-lg bg-slate-50 p-3 dark:bg-slate-800"
                        >
                            <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">
                                    {{ activity.description }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ activity.time_ago }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-8 text-center text-slate-500">
                        No recent activity to display.
                    </div>
                </div>
            </GlassCard>

            <!-- Quick Actions -->
            <div class="no-print grid grid-cols-1 gap-6 md:grid-cols-3">
                <Link href="/elders">
                    <GlassCard
                        class="cursor-pointer transition-shadow hover:shadow-lg"
                    >
                        <div class="p-6 text-center">
                            <Users
                                class="mx-auto mb-4 h-12 w-12 text-blue-500"
                            />
                            <h3 class="mb-2 font-semibold">Manage Elders</h3>
                            <p
                                class="text-sm text-slate-600 dark:text-slate-400"
                            >
                                Add new elders, update profiles, assign
                                relationships
                            </p>
                        </div>
                    </GlassCard>
                </Link>

                <Link href="/pledges">
                    <GlassCard
                        class="cursor-pointer transition-shadow hover:shadow-lg"
                    >
                        <div class="p-6 text-center">
                            <Heart
                                class="mx-auto mb-4 h-12 w-12 text-red-500"
                            />
                            <h3 class="mb-2 font-semibold">Manage Pledges</h3>
                            <p
                                class="text-sm text-slate-600 dark:text-slate-400"
                            >
                                Track donor commitments and relationship
                                assignments
                            </p>
                        </div>
                    </GlassCard>
                </Link>

                <Link href="/reports/donations">
                    <GlassCard
                        class="cursor-pointer transition-shadow hover:shadow-lg"
                    >
                        <div class="p-6 text-center">
                            <DollarSign
                                class="mx-auto mb-4 h-12 w-12 text-green-500"
                            />
                            <h3 class="mb-2 font-semibold">Donation Reports</h3>
                            <p
                                class="text-sm text-slate-600 dark:text-slate-400"
                            >
                                Detailed financial reports and transaction
                                history
                            </p>
                        </div>
                    </GlassCard>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
