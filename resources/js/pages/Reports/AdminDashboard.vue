<script setup lang="ts">
import GlassCard from '@/components/GlassCard.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { useRoute } from '@/composables/useRoute';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    DollarSign,
    Download,
    Filter,
    Heart,
    TrendingUp,
    Users,
} from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

const route = useRoute();

const props = defineProps<{
    stats: {
        relationship_distribution: Record<string, number>;
        promise_fulfillment_rate: number;
        missed_payments: number;
        featured_matches: any[];
        guest_donations_today: number;
        monthly_expenses_covered: number;
        total_sponsorships: number;
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
const selectedBranch = ref(props.filters.branch_id || 'all');
const selectedRange = ref(props.filters.date_range || '30');
const featuredMatches = computed(() => props.stats.featured_matches ?? []);

const currencyFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'ETB',
    maximumFractionDigits: 0,
});

const summaryCards = computed(() => [
    {
        label: 'Total Sponsorships',
        value: props.stats.total_sponsorships ?? 0,
        caption: 'Active relationships',
        icon: Heart,
        route: route('sponsorships.index'),
    },
    {
        label: 'Registered Elders',
        value: props.stats.total_elders ?? 0,
        caption: 'Across all branches',
        icon: Users,
        route: route('elders.index'),
    },
    {
        label: 'Monthly Expenses Covered',
        value: currencyFormatter.format(props.stats.monthly_expenses_covered ?? 0),
        caption: 'Reported by accounting',
        icon: DollarSign,
        route: route('reports.detailed', { metric: 'monthly_expenses' }),
    },
    {
        label: 'Guest Donations Today',
        value: props.stats.guest_donations_today ?? 0,
        caption: 'Meals funded',
        icon: TrendingUp,
        route: route('reports.detailed', { metric: 'guest_donations' }),
    },
]);

const nextSlide = () => {
    currentSlide.value =
        (currentSlide.value + 1) % (featuredMatches.value.length || 1);
};

const applyFilters = () => {
    router.get(
        route('reports.index'),
        {
            branch_id:
                selectedBranch.value === 'all' ? '' : selectedBranch.value,
            date_range: selectedRange.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const exportReport = (format: string) => {
    const url = route('reports.export', {
        format,
        branch_id: selectedBranch.value === 'all' ? null : selectedBranch.value,
        date_range: selectedRange.value,
    });
    window.open(url, '_blank');
};

const printReport = () => {
    window.print();
};

onMounted(() => {
    if (featuredMatches.value.length > 1) {
        setInterval(nextSlide, 5000);
    }
]);

const monthlyTrend = computed(() => props.stats.monthly_trend ?? []);
const trendMaxValue = computed(() => {
    if (!monthlyTrend.value.length) {
        return 1;
    }

    return Math.max(
        ...monthlyTrend.value.map((point) => point.amount ?? 0),
        1,
    );
});

const formattedTrend = (amount: number) =>
    currencyFormatter.format(amount ?? 0);

const promiseFulfillmentLow = computed(
    () => Math.round(props.stats.promise_fulfillment_rate ?? 0),
);
const missedPayments = computed(() => props.stats.missed_payments ?? 0);
</script>

<template>
    <Head title="Admin Dashboard - Mekodonia Reports" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Reports', href: route('reports.index') },
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
                <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                            <Filter class="h-4 w-4" />
                            Filters
                        </div>
                        <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                            <Select v-model="selectedBranch">
                                <SelectTrigger class="w-full min-w-[12rem]">
                                    <SelectValue placeholder="All Branches" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Branches</SelectItem>
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
                                <SelectTrigger class="w-full min-w-[8rem]">
                                    <SelectValue placeholder="30 days" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="7">7 days</SelectItem>
                                    <SelectItem value="30">30 days</SelectItem>
                                    <SelectItem value="90">90 days</SelectItem>
                                    <SelectItem value="365">1 year</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <Button @click="applyFilters" size="sm">
                            Apply Filters
                        </Button>
                        <Button
                            variant="ghost"
                            size="sm"
                            @click="
                                () => {
                                    selectedBranch.value = 'all';
                                    selectedRange.value = '30';
                                }
                            "
                        >
                            Reset
                        </Button>
                    </div>
                </div>
            </GlassCard>

            <!-- Summary Cards -->
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <Link
                    v-for="card in summaryCards"
                    :key="card.label"
                    :href="card.route"
                    class="transition-shadow hover:shadow-lg"
                >
                    <GlassCard class="flex flex-col gap-3 border border-slate-100/60 dark:border-slate-800/50">
                        <div
                            class="inline-flex size-10 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-100"
                        >
                            <component :is="card.icon" class="size-5" />
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-slate-500">
                                {{ card.label }}
                            </p>
                            <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">
                                {{ card.value }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ card.caption }}
                            </p>
                        </div>
                    </GlassCard>
                </Link>
            </div>
                    <div
                        class="inline-flex size-10 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-100"
                    >
                        <component :is="card.icon" class="size-5" />
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-500">
                            {{ card.label }}
                        </p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">
                            {{ card.value }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ card.caption }}
                        </p>
                    </div>
                </GlassCard>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <GlassCard class="lg:col-span-1">
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                Promise health
                            </p>
                            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
                                Promise fulfillment metrics
                            </h2>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-slate-600 dark:text-slate-300">
                                Fulfillment rate measures how many active sponsorships stayed up-to-date this month.
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <Badge variant="outline" class="text-sm">
                                    Fulfillment {{ promiseFulfillmentLow }}%
                                </Badge>
                                <Badge variant="outline" class="text-sm">
                                    Missed payments {{ missedPayments }}
                                </Badge>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Monthly expenses covered
                            </p>
                            <p class="text-lg font-semibold text-slate-900 dark:text-white">
                                {{ currencyFormatter.format(props.stats.monthly_expenses_covered ?? 0) }}
                            </p>
                        </div>
                    </div>
                </GlassCard>

                <GlassCard class="lg:col-span-2">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">
                                    Donations trend
                                </p>
                                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
                                    Last 6 months of guest/pledge receipts
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Updated every 5 minutes
                            </p>
                        </div>
                        <div
                            v-if="monthlyTrend.length"
                            class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6"
                        >
                            <div
                                v-for="point in monthlyTrend"
                                :key="point.label"
                                class="flex flex-col items-center gap-2"
                            >
                                <div class="flex h-28 w-full items-end justify-center">
                                    <span
                                        class="block w-2/3 rounded-full bg-indigo-500 transition-all dark:bg-indigo-400"
                                        :style="{
                                            height: `${Math.max(
                                                8,
                                                ((point.amount ?? 0) / trendMaxValue) * 100,
                                            )}px`,
                                        }"
                                    ></span>
                                </div>
                                <p class="text-xs font-semibold text-slate-900 dark:text-white">
                                    {{ point.label }}
                                </p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    {{ formattedTrend(point.amount ?? 0) }}
                                </p>
                            </div>
                        </div>
                        <div v-else class="rounded-xl border border-dashed border-slate-300 p-4 text-center text-sm text-slate-500 dark:border-slate-600 dark:text-slate-400">
                            No donation trend data available yet.
                        </div>
                    </div>
                </GlassCard>
            </div>

            <!-- Quick Actions -->
            <div class="no-print grid grid-cols-1 gap-6 md:grid-cols-3">
                <Link :href="route('elders.index')">
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

                <Link :href="route('sponsorships.index')">
                    <GlassCard
                        class="cursor-pointer transition-shadow hover:shadow-lg"
                    >
                        <div class="p-6 text-center">
                            <Heart
                                class="mx-auto mb-4 h-12 w-12 text-red-500"
                            />
                            <h3 class="mb-2 font-semibold">
                                Manage Sponsorships
                            </h3>
                            <p
                                class="text-sm text-slate-600 dark:text-slate-400"
                            >
                                Track donor sponsorships and relationship
                                assignments
                            </p>
                        </div>
                    </GlassCard>
                </Link>

                <Link :href="route('reports.donations')">
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

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <Link
                    :href="route('reports.detailed', {
                        metric: 'promise_fulfillment',
                    })"
                >
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

                <Link
                    :href="route('reports.detailed', {
                        metric: 'missed_payments',
                    })"
                >
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

                <Link
                    :href="route('reports.detailed', {
                        metric: 'guest_donations',
                    })"
                >
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

                <Link
                    :href="route('reports.detailed', {
                        metric: 'monthly_expenses',
                    })"
                >
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
                                Total sponsored monthly
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
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                Wall of Love - Success Stories
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Stories your board can share in reports and campaigns.
                            </p>
                        </div>
                        <Badge variant="outline">
                            {{ featuredMatches.length }} featured matches
                        </Badge>
                    </div>
                    <div
                        v-if="featuredMatches.length"
                        class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-rose-50 via-pink-50 to-purple-50 dark:from-rose-900/20 dark:via-pink-900/20 dark:to-purple-900/20"
                    >
                        <transition-group name="fade" tag="div">
                            <div
                                v-for="(match, index) in featuredMatches"
                                :key="match.id ?? index"
                                v-show="currentSlide === index"
                                class="grid grid-cols-1 gap-6 p-8 sm:grid-cols-2"
                            >
                                <div class="space-y-3">
                                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">
                                        {{ match.branch_name ?? 'Branch Story' }}
                                    </p>
                                    <p class="text-2xl font-semibold text-slate-900 dark:text-white">
                                        {{ match.donor_name }} + {{ match.elder_name }}
                                    </p>
                                    <p class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ match.story ?? 'This relationship shows how consistent visits and care change lives.' }}
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        <Badge variant="secondary">
                                            {{ match.relationship ?? 'Sibling' }}
                                        </Badge>
                                        <Badge variant="outline">
                                            {{ match.months_supported ?? 0 }} months
                                        </Badge>
                                    </div>
                                </div>
                                <div class="rounded-2xl bg-white/90 p-6 shadow-lg ring-1 ring-white/40 dark:bg-slate-900/70 dark:ring-slate-800/60">
                                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                        Monthly Support
                                    </p>
                                    <p class="mt-2 text-4xl font-bold text-slate-900 dark:text-white">
                                        {{ match.monthly_support ?? '—' }} ETB
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Started {{ match.started_at ?? 'recently' }}
                                    </p>
                                </div>
                            </div>
                        </transition-group>
                        <div class="absolute inset-x-0 bottom-4 flex justify-center gap-2">
                            <button
                                v-for="(_, index) in featuredMatches"
                                :key="`dot-${index}`"
                                class="h-2 w-2 rounded-full border border-white/60"
                                :class="[
                                    currentSlide === index
                                        ? 'bg-white'
                                        : 'bg-white/40',
                                ]"
                                @click="currentSlide = index"
                            ></button>
                        </div>
                    </div>
                    <div v-else class="py-12 text-center text-slate-500">
                        <Heart class="mx-auto mb-4 h-16 w-16 text-slate-300" />
                        <p>
                            No featured matches yet. Mark some elders as featured to showcase success stories.
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
                            :href="route('reports.activity')"
                            class="text-sm text-blue-600 hover:underline"
                            >View all</Link
                        >
                    </div>
                    <div
                        v-if="props.stats.recent_activity.length > 0"
                        class="space-y-4"
                    >
                        <div
                            v-for="activity in props.stats.recent_activity"
                            :key="activity.id"
                            class="flex items-start gap-4 rounded-lg bg-slate-50 p-4 shadow-sm transition-all hover:shadow-md dark:bg-slate-800"
                        >
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900"
                            >
                                <TrendingUp
                                    class="h-5 w-5 text-blue-600 dark:text-blue-400"
                                />
                            </div>
                            <div class="flex-1">
                                <p
                                    class="font-medium text-slate-800 dark:text-slate-200"
                                >
                                    {{ activity.description }}
                                </p>
                                <p
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
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
