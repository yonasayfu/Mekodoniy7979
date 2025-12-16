<script setup lang="ts">
import Pagination from '@/components/Pagination.vue'; // Import Pagination component
import { useRoute } from '@/composables/useRoute';
import { dashboard, login, register } from '@/routes';
import { Head, Link, router } from '@inertiajs/vue3';
import { User } from 'lucide-vue-next'; // Import User icon
import { ref } from 'vue';

const route = useRoute();

interface WallOfLoveEntry {
    donor_name: string;
    elder_name: string;
    elder_id: number;
    elder_profile_picture: string | null;
    pledge_date: string;
}

interface LiveCounters {
    eldersWaiting: number;
    matchedElders: number;
    visitsThisMonth: number;
}

interface ElderSummary {
    id: number;
    full_name: string;
    profile_picture_path: string | null;
    priority_level: 'low' | 'medium' | 'high';
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    wallOfLove: WallOfLoveEntry[];
    liveCounters: LiveCounters;
    eldersGallery: {
        data: ElderSummary[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
    };
    filters: {
        priority?: string;
        gender?: string;
    };
}>();

const currentPriority = ref(props.filters.priority || '');
const currentGender = ref(props.filters.gender || '');

const priorityOptions = [
    { value: '', label: 'All Priorities' },
    { value: 'low', label: 'Low' },
    { value: 'medium', label: 'Medium' },
    { value: 'high', label: 'High' },
];

const genderOptions = [
    { value: '', label: 'All Genders' },
    { value: 'Male', label: 'Male' },
    { value: 'Female', label: 'Female' },
    { value: 'Other', label: 'Other' },
];

const applyFilters = () => {
    router.get(
        route('home'),
        {
            priority: currentPriority.value,
            gender: currentGender.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-slate-100 p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-slate-900"
    >
        <header
            class="mb-6 w-full max-w-7xl text-sm lg:flex lg:justify-end lg:p-8"
        >
            <nav class="flex items-center justify-end gap-4 p-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-500"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="register()"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-500"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <main
            class="flex w-full flex-col items-center justify-center p-6 text-slate-800 dark:text-slate-200"
        >
            <div class="space-y-8 text-center">
                <h1
                    class="text-5xl font-extrabold tracking-tight sm:text-6xl lg:text-7xl"
                >
                    Mekodonia Home Connect
                </h1>
                <p class="mt-4 text-xl text-slate-600 dark:text-slate-400">
                    A donor-elder matchmaking and stewardship platform.
                </p>
                <div
                    class="flex flex-col items-center justify-center gap-4 sm:flex-row"
                >
                    <Link
                        href="#"
                        class="rounded-md bg-emerald-600 px-6 py-3 text-lg font-semibold text-white shadow-lg hover:bg-emerald-500"
                    >
                        Long-term support
                    </Link>
                    <Link
                        :href="route('guest.donation')"
                        class="rounded-md bg-indigo-600 px-6 py-3 text-lg font-semibold text-white shadow-lg hover:bg-indigo-500"
                    >
                        Donate a Meal
                    </Link>
                </div>
            </div>

            <div class="mt-12 w-full max-w-4xl space-y-12">
                <section
                    class="rounded-lg bg-white/70 p-8 shadow-xl dark:bg-slate-800/70"
                >
                    <h2 class="text-3xl font-bold">
                        Hero Slider: "Become a Father Today"
                    </h2>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">
                        (Implementation for dynamic hero images and call to
                        action)
                    </p>
                </section>

                <section
                    class="rounded-lg bg-white/70 p-8 shadow-xl dark:bg-slate-800/70"
                >
                    <h2 class="text-3xl font-bold">Live Counters</h2>
                    <div class="mt-4 grid grid-cols-3 gap-4">
                        <div
                            class="rounded-md bg-blue-100 p-4 text-blue-800 dark:bg-blue-900 dark:text-blue-100"
                        >
                            Elders Waiting:
                            <span class="text-2xl font-bold">{{
                                liveCounters.eldersWaiting
                            }}</span>
                        </div>
                        <div
                            class="rounded-md bg-green-100 p-4 text-green-800 dark:bg-green-900 dark:text-green-100"
                        >
                            Matched:
                            <span class="text-2xl font-bold">{{
                                liveCounters.matchedElders
                            }}</span>
                        </div>
                        <div
                            class="rounded-md bg-purple-100 p-4 text-purple-800 dark:bg-purple-900 dark:text-purple-100"
                        >
                            Visited This Month:
                            <span class="text-2xl font-bold">{{
                                liveCounters.visitsThisMonth
                            }}</span>
                        </div>
                    </div>
                </section>

                <section
                    class="rounded-lg bg-white/70 p-8 shadow-xl dark:bg-slate-800/70"
                >
                    <h2 class="text-3xl font-bold">Elder Gallery</h2>
                    <p class="mt-2 text-slate-600 dark:text-slate-400">
                        Browse elders in need of support.
                    </p>
                    <div
                        class="mt-6 flex flex-col gap-4 md:flex-row md:items-center"
                    >
                        <div class="flex flex-1 gap-2">
                            <select
                                v-model="currentPriority"
                                @change="applyFilters"
                                class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            >
                                <option
                                    v-for="option in priorityOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                            <select
                                v-model="currentGender"
                                @change="applyFilters"
                                class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            >
                                <option
                                    v-for="option in genderOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                            <button
                                @click="applyFilters"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-500"
                            >
                                Apply Filters
                            </button>
                        </div>
                    </div>
                    <div
                        class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <template v-if="eldersGallery.data.length">
                            <div
                                v-for="elder in eldersGallery.data"
                                :key="elder.id"
                                class="rounded-lg bg-slate-100/80 p-4 shadow-md dark:bg-slate-700/80"
                            >
                                <Link :href="route('elders.show', elder.id)">
                                    <div class="flex items-center gap-4">
                                        <img
                                            v-if="elder.profile_picture_path"
                                            :src="`/storage/${elder.profile_picture_path}`"
                                            class="h-16 w-16 rounded-full object-cover"
                                            alt="Elder"
                                        />
                                        <div
                                            v-else
                                            class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-300 text-slate-600 dark:bg-slate-600 dark:text-slate-300"
                                        >
                                            <User class="size-8" />
                                        </div>
                                        <div>
                                            <h3
                                                class="text-lg font-semibold text-slate-800 dark:text-slate-100"
                                            >
                                                {{ elder.full_name }}
                                            </h3>
                                            <p
                                                class="text-sm text-slate-600 dark:text-slate-300"
                                            >
                                                Priority:
                                                {{ elder.priority_level }}
                                            </p>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </template>
                        <p
                            v-else
                            class="col-span-full text-center text-slate-500 dark:text-slate-400"
                        >
                            No elders found matching your criteria.
                        </p>
                    </div>
                    <div
                        v-if="eldersGallery.last_page > 1"
                        class="mt-8 flex justify-center"
                    >
                        <Pagination :links="eldersGallery.links" />
                    </div>
                </section>

                <section
                    class="rounded-lg bg-white/70 p-8 shadow-xl dark:bg-slate-800/70"
                >
                    <h2 class="text-3xl font-bold">FAQ & Trust Badges</h2>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">
                        (Content for frequently asked questions and trust
                        certifications)
                    </p>
                </section>
            </div>
        </main>
    </div>
</template>
