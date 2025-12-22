
<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { User, Users, UserCheck, CalendarHeart } from 'lucide-vue-next'; // Import User icon
import { ref, toRefs, watch, onMounted, onUnmounted } from 'vue';
import { route } from 'ziggy-js';
import Pagination from '@/Components/Pagination.vue';

interface WallOfLoveEntry {
    donor_name: string;
    elder_name: string;
    elder_id: number;
    elder_profile_picture: string | null;
    sponsorship_date: string;
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
    heroSlides: {
        title: string;
        description: string;
        image: string;
        cta_text: string;
        cta_link: string;
    }[];
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

// Hero Slider Logic
const heroCurrentSlide = ref(0);
let heroSlideInterval: number;

const nextHeroSlide = () => {
    heroCurrentSlide.value = (heroCurrentSlide.value + 1) % props.heroSlides.length;
};

const goToHeroSlide = (index: number) => {
    heroCurrentSlide.value = index;
};

onMounted(() => {
    heroSlideInterval = setInterval(nextHeroSlide, 7000); // Change slide every 7 seconds
});

onUnmounted(() => {
    clearInterval(heroSlideInterval);
});

// Wall of Love Slider Logic
const currentWallOfLoveSlide = ref(0);
let wallOfLoveSlideInterval: number;

const nextWallOfLoveSlide = () => {
    currentWallOfLoveSlide.value =
        (currentWallOfLoveSlide.value + 1) % props.wallOfLove.length;
};

const prevWallOfLoveSlide = () => {
    currentWallOfLoveSlide.value =
        (currentWallOfLoveSlide.value - 1 + props.wallOfLove.length) % props.wallOfLove.length;
};

onMounted(() => {
    if (props.wallOfLove.length > 1) {
        wallOfLoveSlideInterval = setInterval(nextWallOfLoveSlide, 5000); // Change slide every 5 seconds
    }
});

onUnmounted(() => {
    clearInterval(wallOfLoveSlideInterval);
});

// Live Counters Animation
function useCountUp(target: ref<number>, duration = 2000) {
    const count = ref(0);
    const frameRate = 1000 / 60;
    const totalFrames = Math.round(duration / frameRate);
    let frame = 0;
    let rafId: number;

    const easeOutQuad = (t: number) => t * (2 - t);

    const animate = () => {
        frame++;
        const progress = easeOutQuad(frame / totalFrames);
        count.value = Math.round(target.value * progress);

        if (frame < totalFrames) {
            rafId = requestAnimationFrame(animate);
        } else {
            count.value = target.value;
        }
    };

    const observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                animate();
                observer.disconnect();
            }
        },
        {
            threshold: 0.1,
        }
    );

    const element = ref<HTMLElement | null>(null);

    onMounted(() => {
        if (element.value) {
            observer.observe(element.value);
        }
    });

    onUnmounted(() => {
        if (rafId) {
            cancelAnimationFrame(rafId);
        }
        observer.disconnect();
    });

    watch(target, () => {
        frame = 0;
        count.value = 0;
        if (element.value) {
            observer.observe(element.value);
        }
    });

    return { count, element };
}

const { eldersWaiting, matchedElders, visitsThisMonth } = toRefs(props.liveCounters);

const { count: eldersWaitingCount, element: eldersWaitingRef } = useCountUp(eldersWaiting);
const { count: matchedEldersCount, element: matchedEldersRef } = useCountUp(matchedElders);
const { count: visitsThisMonthCount, element: visitsThisMonthRef } = useCountUp(visitsThisMonth);

</script>
<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
            rel="stylesheet"
        />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div
        class="min-h-screen antialiased bg-background font-sans text-foreground"
    >
        <!-- Navbar -->
        <nav
            class="fixed left-0 right-0 top-0 z-50 bg-white/80 backdrop-blur-md shadow-sm dark:bg-gray-900/80"
        >
            <div
                class="container mx-auto flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8"
            >
                <Link :href="route('home')" class="flex items-center space-x-2">
                    <img
                        src="/images/mekodonia-logo.png"
                        alt="Mekodonia Logo"
                        class="h-8 w-auto"
                    />
                    <span class="text-xl font-bold text-gray-900 dark:text-white"
                        >Mekodonia</span
                    >
                </Link>
                <div class="flex items-center space-x-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="route('register')"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                        >
                            Register
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <main>
            <!-- Hero Section -->
            <section
                class="relative flex h-screen items-center justify-center bg-cover bg-center text-white"
                :style="{ backgroundImage: `url(${heroSlides[heroCurrentSlide].image})` }"
            >
                <div class="absolute inset-0 bg-black/60"></div>
                <div class="relative z-10 mx-auto max-w-4xl p-8 text-center">
                    <h1 class="text-5xl font-extrabold leading-tight md:text-6xl">
                        {{ heroSlides[heroCurrentSlide].title }}
                    </h1>
                    <p class="mt-4 text-xl md:text-2xl">
                        {{ heroSlides[heroCurrentSlide].description }}
                    </p>
                    <div class="mt-8 flex justify-center space-x-4">
                        <Link
                            :href="heroSlides[heroCurrentSlide].cta_link"
                            class="rounded-lg bg-indigo-600 px-8 py-3 text-lg font-semibold shadow-lg transition-all hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            {{ heroSlides[heroCurrentSlide].cta_text }}
                        </Link>
                        <Link
                            href="#how-it-works"
                            class="rounded-lg bg-white px-8 py-3 text-lg font-semibold text-gray-800 shadow-lg transition-all hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Learn More
                        </Link>
                    </div>
                </div>
                <!-- Slider Indicators -->
                <div class="absolute bottom-8 left-1/2 z-10 flex -translate-x-1/2 space-x-2">
                    <button
                        v-for="(_, index) in heroSlides"
                        :key="`dot-${index}`"
                        @click="goToHeroSlide(index)"
                        :class="[
                            'h-3 w-3 rounded-full transition-colors duration-300',
                            heroCurrentSlide === index ? 'bg-indigo-500' : 'bg-white/50 hover:bg-white',
                        ]"
                    ></button>
                </div>
            </section>

            <!-- Impact Section (Live Counters) -->
            <section class="py-20 bg-gray-50 dark:bg-gray-800">
                <div class="container mx-auto px-4 text-center">
                    <h2 class="text-4xl font-bold text-gray-800 dark:text-white">Our Impact</h2>
                    <p class="mt-4 text-xl text-gray-600 dark:text-gray-300">Numbers that speak volumes about our mission.</p>
                    <div class="mt-12 grid grid-cols-1 gap-10 md:grid-cols-3">
                        <div
                            ref="eldersWaitingRef"
                            class="flex flex-col items-center justify-center rounded-xl bg-white p-8 shadow-lg dark:bg-gray-700"
                        >
                            <div
                                class="flex h-20 w-20 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300"
                            >
                                <Users class="size-10" />
                            </div>
                            <p class="mt-6 text-5xl font-extrabold text-gray-800 dark:text-white">
                                {{ eldersWaitingCount }}
                            </p>
                            <p class="mt-2 text-lg font-semibold text-gray-600 dark:text-gray-300">
                                Elders Awaiting Sponsorship
                            </p>
                        </div>
                        <div
                            ref="matchedEldersRef"
                            class="flex flex-col items-center justify-center rounded-xl bg-white p-8 shadow-lg dark:bg-gray-700"
                        >
                            <div
                                class="flex h-20 w-20 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300"
                            >
                                <UserCheck class="size-10" />
                            </div>
                            <p class="mt-6 text-5xl font-extrabold text-gray-800 dark:text-white">
                                {{ matchedEldersCount }}
                            </p>
                            <p class="mt-2 text-lg font-semibold text-gray-600 dark:text-gray-300">
                                Elders Matched
                            </p>
                        </div>
                        <div
                            ref="visitsThisMonthRef"
                            class="flex flex-col items-center justify-center rounded-xl bg-white p-8 shadow-lg dark:bg-gray-700"
                        >
                            <div
                                class="flex h-20 w-20 items-center justify-center rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300"
                            >
                                <CalendarHeart class="size-10" />
                            </div>
                            <p class="mt-6 text-5xl font-extrabold text-gray-800 dark:text-white">
                                {{ visitsThisMonthCount }}
                            </p>
                            <p class="mt-2 text-lg font-semibold text-gray-600 dark:text-gray-300">
                                Visits This Month
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- How It Works Section -->
            <section id="how-it-works" class="py-20 bg-white dark:bg-gray-900">
                <div class="container mx-auto px-4 text-center">
                    <h2 class="text-4xl font-bold text-gray-800 dark:text-white">How It Works</h2>
                    <p class="mt-4 text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Connecting hearts, changing lives. Simple steps to make a profound difference.
                    </p>
                    <div class="mt-12 grid grid-cols-1 gap-10 md:grid-cols-3">
                        <div class="flex flex-col items-center p-6 rounded-xl shadow-lg bg-gray-50 dark:bg-gray-800">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                                <User class="size-10" />
                            </div>
                            <h3 class="mt-6 text-xl font-semibold text-gray-800 dark:text-white">1. Browse Elders</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Discover compelling stories of elders needing support. Filter by location, needs, and more.
                            </p>
                        </div>
                        <div class="flex flex-col items-center p-6 rounded-xl shadow-lg bg-gray-50 dark:bg-gray-800">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300">
                                <Heart class="size-10" />
                            </div>
                            <h3 class="mt-6 text-xl font-semibold text-gray-800 dark:text-white">2. Choose Your Relationship</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Decide how you want to support – as a Father, Mother, Brother, or Sister.
                            </p>
                        </div>
                        <div class="flex flex-col items-center p-6 rounded-xl shadow-lg bg-gray-50 dark:bg-gray-800">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300">
                                <DollarSign class="size-10" />
                            </div>
                            <h3 class="mt-6 text-xl font-semibold text-gray-800 dark:text-white">3. Make an Impact</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Set up monthly contributions or make a one-time donation easily and securely.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Elder Gallery Section -->
            <section
                id="elders-gallery"
                class="py-20 bg-gray-50 dark:bg-gray-800"
            >
                <div class="container mx-auto px-4">
                    <h2 class="text-4xl font-bold text-center text-gray-800 dark:text-white">Our Elders</h2>
                    <p class="mt-4 text-xl text-center text-gray-600 dark:text-gray-300">
                        Meet the elders whose lives you can transform.
                    </p>
                    <div class="mt-10 flex flex-col items-center justify-between gap-4 md:flex-row">
                        <div class="flex flex-1 items-center justify-center gap-4">
                            <select
                                v-model="currentPriority"
                                @change="applyFilters"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
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
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
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
                                class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Filter
                            </button>
                        </div>
                    </div>
                    <div
                        class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                    >
                        <template v-if="eldersGallery.data.length">
                            <div
                                v-for="elder in eldersGallery.data"
                                :key="elder.id"
                                class="overflow-hidden rounded-xl bg-white shadow-lg transition-all hover:shadow-xl dark:bg-gray-700"
                            >
                                <Link :href="route('elders.show', elder.id)">
                                    <div class="relative h-56 w-full">
                                        <img
                                            v-if="elder.profile_picture_path"
                                            :src="`/storage/${elder.profile_picture_path}`"
                                            class="h-full w-full object-cover"
                                            alt="Elder"
                                        />
                                        <div
                                            v-else
                                            class="flex h-full w-full items-center justify-center bg-gray-200 text-gray-500 dark:bg-gray-600 dark:text-gray-400"
                                        >
                                            <User class="size-20" />
                                        </div>
                                        <div
                                            class="absolute right-4 top-4 rounded-full bg-indigo-500 px-3 py-1 text-xs font-semibold uppercase text-white"
                                        >
                                            {{ elder.priority_level }}
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <h3
                                            class="text-2xl font-bold text-gray-900 dark:text-white"
                                        >
                                            {{ elder.full_name }}
                                        </h3>
                                        <p
                                            class="mt-2 text-gray-600 dark:text-gray-300"
                                        >
                                            Awaiting your compassionate support.
                                        </p>
                                        <div
                                            class="mt-4 inline-flex items-center text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200"
                                        >
                                            Learn More
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="ml-1 size-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 5l7 7-7 7"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </template>
                        <p
                            v-else
                            class="col-span-full text-center text-gray-500 dark:text-gray-400"
                        >
                            No elders found matching your criteria.
                        </p>
                    </div>
                    <div
                        v-if="eldersGallery.last_page > 1"
                        class="mt-12 flex justify-center"
                    >
                        <!-- Pagination Component -->
                        <nav
                            class="flex items-center space-x-2"
                            aria-label="Pagination"
                        >
                            <Link
                                v-for="(link, index) in eldersGallery.links"
                                :key="index"
                                :href="link.url || '#'"
                                :class="[
                                    'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg',
                                    link.active
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600',
                                    !link.url &&
                                        'cursor-not-allowed opacity-50',
                                ]"
                                v-html="link.label"
                            />
                        </nav>
                    </div>
                </div>
            </section>

            <!-- Wall of Love - Success Stories -->
            <section class="py-20 bg-white dark:bg-gray-900">
                <div class="container mx-auto px-4 text-center">
                    <h2 class="text-4xl font-bold text-gray-800 dark:text-white">Wall of Love</h2>
                    <p class="mt-4 text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Heartwarming connections made possible by our community.
                    </p>
                    <div
                        v-if="wallOfLove.length > 0"
                        class="relative mt-12 overflow-hidden rounded-xl bg-gray-50 p-8 shadow-lg dark:bg-gray-800"
                    >
                        <div
                            class="flex transition-transform duration-700 ease-in-out"
                            :style="{ transform: `translateX(-${currentWallOfLoveSlide * 100}%)` }"
                        >
                            <div
                                v-for="(match, index) in wallOfLove"
                                :key="index"
                                class="w-full flex-shrink-0 text-center"
                            >
                                <div
                                    class="relative mx-auto h-32 w-32 overflow-hidden rounded-full border-4 border-indigo-500 shadow-md"
                                >
                                    <img
                                        v-if="match.elder_profile_picture"
                                        :src="`/storage/${match.elder_profile_picture}`"
                                        alt="Elder Profile"
                                        class="h-full w-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="flex h-full w-full items-center justify-center bg-gray-200 text-gray-500 dark:bg-gray-600 dark:text-gray-400"
                                    >
                                        <User class="size-16" />
                                    </div>
                                </div>
                                <p class="mt-6 text-xl font-semibold text-gray-800 dark:text-white">
                                    {{ match.donor_name }} is supporting
                                    {{ match.elder_name }}
                                </p>
                                <p class="mt-2 text-gray-600 dark:text-gray-300">
                                    Joined {{ match.sponsorship_date }}
                                </p>
                            </div>
                        </div>

                        <!-- Wall of Love Slider Controls -->
                        <button
                            @click="prevWallOfLoveSlide"
                            class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full bg-white/50 p-2 text-gray-800 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            &#10094;
                        </button>
                        <button
                            @click="nextWallOfLoveSlide"
                            class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full bg-white/50 p-2 text-gray-800 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            &#10095;
                        </button>
                        <!-- Wall of Love Slider Indicators -->
                        <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 space-x-2">
                            <button
                                v-for="(_, index) in wallOfLove"
                                :key="`wall-dot-${index}`"
                                @click="currentWallOfLoveSlide = index"
                                :class="[
                                    'h-3 w-3 rounded-full transition-colors duration-300',
                                    currentWallOfLoveSlide === index ? 'bg-indigo-500' : 'bg-white/50 hover:bg-white',
                                ]"
                            ></button>
                        </div>
                    </div>
                    <div v-else class="mt-12 text-center text-gray-500 dark:text-gray-400">
                        <p class="text-xl">No success stories to share yet.</p>
                        <p class="mt-2">Be the first to create a lasting bond!</p>
                    </div>
                </div>
            </section>

            <!-- Call to Action Section -->
            <section class="py-20 bg-indigo-600 text-white text-center">
                <div class="container mx-auto px-4">
                    <h2 class="text-4xl font-bold">Ready to Make a Difference?</h2>
                    <p class="mt-4 text-xl max-w-3xl mx-auto">
                        Your support can provide comfort, dignity, and care for our elders. Join our mission today.
                    </p>
                    <div class="mt-8 space-x-4">
                        <Link
                            :href="route('register')"
                            class="rounded-lg bg-white px-8 py-3 text-lg font-semibold text-indigo-600 shadow-lg transition-all hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2"
                        >
                            Sponsor an Elder
                        </Link>
                        <Link
                            href="#guest-donation-form"
                            class="rounded-lg border border-white px-8 py-3 text-lg font-semibold text-white shadow-lg transition-all hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2"
                        >
                            Make a One-Time Donation
                        </Link>
                    </div>
                </div>
            </section>

            <!-- FAQ & Trust Badges Section -->
            <section class="py-20 bg-gray-50 dark:bg-gray-800">
                <div class="container mx-auto px-4">
                    <h2 class="text-4xl font-bold text-center text-gray-800 dark:text-white">
                        Frequently Asked Questions
                    </h2>
                    <div class="mt-12 max-w-4xl mx-auto space-y-8">
                        <!-- FAQ Item 1 -->
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white">
                                How can I sponsor an elder?
                            </h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                You can browse our Elder Gallery, select an elder you wish to support, and choose a sponsorship relationship (Father, Mother, Brother, Sister). You can then set up a monthly contribution.
                            </p>
                        </div>
                        <!-- FAQ Item 2 -->
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white">
                                Can I make a one-time donation?
                            </h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Yes, you can make a one-time donation to provide essential items like meals or medication without committing to a long-term sponsorship. Look for the "Make a One-Time Donation" button.
                            </p>
                        </div>
                        <!-- FAQ Item 3 -->
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white">
                                Is my donation secure?
                            </h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                We utilize secure payment gateways and protocols to ensure your donations are processed safely. Your financial information is always protected.
                            </p>
                        </div>
                        <!-- FAQ Item 4 -->
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white">
                                How do I track my impact?
                            </h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Registered donors can access a personalized dashboard to view their payment history, details of their sponsored elder, and receive annual thank-you summaries.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 py-12 text-white dark:bg-gray-950">
            <div class="container mx-auto px-4 text-center">
                <p>&copy; {{ new Date().getFullYear() }} Mekodonia Home Connect. All rights reserved.</p>
                <div class="mt-4 space-x-4">
                    <a href="#" class="hover:text-gray-300">Privacy Policy</a>
                    <a href="#" class="hover:text-gray-300">Terms of Service</a>
                    <a href="#" class="hover:text-gray-300">Contact Us</a>
                </div>
            </div>
        </footer>
    </div>
</template>
