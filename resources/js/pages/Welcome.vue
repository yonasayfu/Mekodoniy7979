
<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { User, Users, UserCheck, CalendarHeart } from 'lucide-vue-next'; // Import User icon
import { ref, toRefs, watch, onMounted, onUnmounted } from 'vue';
import { route } from 'ziggy-js';

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
const currentSlide = ref(0);
let slideInterval: number;

const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % props.heroSlides.length;
};

const prevSlide = () => {
    currentSlide.value = (currentSlide.value - 1 + props.heroSlides.length) % props.heroSlides.length;
};

const goToSlide = (index: number) => {
    currentSlide.value = index;
};

onMounted(() => {
    slideInterval = setInterval(nextSlide, 7000); // Change slide every 7 seconds
});

onUnmounted(() => {
    clearInterval(slideInterval);
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
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col bg-slate-100 text-[#1b1b18] dark:bg-slate-900"
    >
        <header
            class="w-full text-sm lg:p-8"
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
            class="flex w-full flex-col justify-center px-4 py-6 lg:px-8 text-slate-800 dark:text-slate-200"
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
                    <a
                        href="#"
                        class="rounded-md bg-indigo-600 px-6 py-3 text-lg font-semibold text-white shadow-lg hover:bg-indigo-500"
                    >
                        Donate a Meal
                    </a>
                </div>
            </div>

            <div class="mt-12 w-full space-y-12">
                <section class="relative w-full overflow-hidden py-12">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="relative h-64 md:h-80">
                            <template v-for="(slide, index) in heroSlides" :key="index">
                                <transition
                                    enter-active-class="transition-opacity ease-in-out duration-1000"
                                    enter-from-class="opacity-0"
                                    enter-to-class="opacity-100"
                                    leave-active-class="transition-opacity ease-in-out duration-1000"
                                    leave-from-class="opacity-100"
                                    leave-to-class="opacity-0"
                                >
                                    <div v-show="currentSlide === index" class="absolute inset-0 h-full w-full">
                                        <img :src="slide.image" :alt="slide.title" class="h-full w-full object-cover" />
                                        <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center text-center text-white p-4">
                                            <h2 class="text-3xl md:text-5xl font-extrabold tracking-tight">
                                                {{ slide.title }}
                                            </h2>
                                            <p class="mt-2 md:mt-4 text-lg md:text-xl max-w-2xl">
                                                {{ slide.description }}
                                            </p>
                                            <a :href="slide.cta_link" class="mt-4 md:mt-6 rounded-md bg-indigo-600 px-6 py-3 text-lg font-semibold text-white shadow-lg hover:bg-indigo-500">
                                                {{ slide.cta_text }}
                                            </a>
                                        </div>
                                    </div>
                                </transition>
                            </template>
                        </div>

                        <!-- Slider Controls -->
                        <button @click="prevSlide" class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full bg-white/50 p-2 text-slate-800 hover:bg-white">
                            &#10094;
                        </button>
                        <button @click="nextSlide" class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full bg-white/50 p-2 text-slate-800 hover:bg-white">
                            &#10095;
                        </button>

                        <!-- Slider Indicators -->
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
                            <button
                                v-for="(_, index) in heroSlides"
                                :key="`dot-${index}`"
                                @click="goToSlide(index)"
                                :class="[
                                    'h-3 w-3 rounded-full',
                                    currentSlide === index ? 'bg-indigo-600' : 'bg-white/50',
                                ]"
                            ></button>
                        </div>
                    </div>
                </section>

                <section
                    class="py-12 bg-white/70 dark:bg-slate-800/70"
                >
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 class="text-3xl font-bold text-center">Live Counters</h2>
                        <div
                            class="mt-8 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3"
                        >
                            <div
                                ref="eldersWaitingRef"
                                class="flex flex-col items-center justify-center rounded-lg bg-white p-6 text-center shadow-lg dark:bg-slate-800"
                            >
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-blue-500 dark:bg-blue-900 dark:text-blue-300"
                                >
                                    <Users class="size-8" />
                                </div>
                                <p
                                    class="mt-4 text-4xl font-extrabold text-slate-800 dark:text-white"
                                >
                                    {{ eldersWaitingCount }}
                                </p>
                                <p
                                    class="mt-2 text-base font-medium text-slate-600 dark:text-slate-400"
                                >
                                    Elders Waiting
                                </p>
                            </div>
                            <div
                                ref="matchedEldersRef"
                                class="flex flex-col items-center justify-center rounded-lg bg-white p-6 text-center shadow-lg dark:bg-slate-800"
                            >
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-500 dark:bg-green-900 dark:text-green-300"
                                >
                                    <UserCheck class="size-8" />
                                </div>
                                <p
                                    class="mt-4 text-4xl font-extrabold text-slate-800 dark:text-white"
                                >
                                    {{ matchedEldersCount }}
                                </p>
                                <p
                                    class="mt-2 text-base font-medium text-slate-600 dark:text-slate-400"
                                >
                                    Matched
                                </p>
                            </div>
                            <div
                                ref="visitsThisMonthRef"
                                class="flex flex-col items-center justify-center rounded-lg bg-white p-6 text-center shadow-lg dark:bg-slate-800"
                            >
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-full bg-purple-100 text-purple-500 dark:bg-purple-900 dark:text-purple-300"
                                >
                                    <CalendarHeart class="size-8" />
                                </div>
                                <p
                                    class="mt-4 text-4xl font-extrabold text-slate-800 dark:text-white"
                                >
                                    {{ visitsThisMonthCount }}
                                </p>
                                <p
                                    class="mt-2 text-base font-medium text-slate-600 dark:text-slate-400"
                                >
                                    Visited This Month
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
                
                <section
                    id="elders-gallery"
                    class="py-12 bg-white/70 dark:bg-slate-800/70"
                >
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
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
                    </div>
                </section>

                <section
                    class="py-12"
                >
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-3xl font-bold">FAQ & Trust Badges</h2>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">
                        (Content for frequently asked questions and trust
                        certifications)
                    </p>
                    </div>
                </section>
            </div>
        </main>
    </div>
</template>
