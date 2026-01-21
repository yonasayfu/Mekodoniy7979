<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    CalendarHeart,
    DollarSign,
    Heart,
    HeartHandshake,
    User,
    UserCheck,
    Users,
} from 'lucide-vue-next'; // Import User icon
import { onMounted, onUnmounted, ref, toRefs, watch } from 'vue';
import { route } from 'ziggy-js';
import PreSponsorshipForm from '@/components/PreSponsorshipForm.vue';

const preSponsorshipFormRef = ref<InstanceType<typeof PreSponsorshipForm> | null>(null);

interface WallOfLoveEntry {
    donor_name: string;
    elder_name: string;
    elder_id: number;
    elder_profile_photo_url: string | null;
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
    profile_photo_url: string;
    priority_level: 'low' | 'medium' | 'high';
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

type RelationshipPreset = 'father' | 'mother' | 'brother' | 'sister';

const relationshipCards: Array<{
    title: string;
    label: string;
    description: string;
    highlight: string;
    accent: string;
    relation: RelationshipPreset;
}> = [
    {
        title: 'Become a Father',
        label: 'Father',
        description:
            'Offer protection, mentorship, and monthly essentials to an elder who needs a fatherly hand.',
        highlight: 'Suggested: 2,000 ETB / month',
        accent: 'from-blue-500/10 to-blue-500/5',
        relation: 'father',
    },
    {
        title: 'Become a Mother',
        label: 'Mother',
        description:
            'Provide nourishment, warmth, and companionship much like a caring mother would.',
        highlight: 'Suggested: 1,800 ETB / month',
        accent: 'from-rose-500/10 to-rose-500/5',
        relation: 'mother',
    },
    {
        title: 'Become a Brother',
        label: 'Brother',
        description:
            'Stand beside an elder as a dependable brother—cover visits, medicine, and small joys.',
        highlight: 'Suggested: 1,200 ETB / month',
        accent: 'from-emerald-500/10 to-emerald-500/5',
        relation: 'brother',
    },
    {
        title: 'Become a Sister',
        label: 'Sister',
        description:
            'Share empathy and light as a sister sponsor by funding comfort kits and wellness checks.',
        highlight: 'Suggested: 1,000 ETB / month',
        accent: 'from-purple-500/10 to-purple-500/5',
        relation: 'sister',
    },
];

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
const heroBackgroundVideo = '/images/6096-188704568_small.mp4';

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
    heroCurrentSlide.value =
        (heroCurrentSlide.value + 1) % props.heroSlides.length;
};

const goToHeroSlide = (index: number) => {
    heroCurrentSlide.value = index;
};

const scrollToAnchor = (selector: string) => {
    if (!selector.startsWith('#')) {
        return;
    }

    const section = document.querySelector(selector);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
    }
};

const openRelationshipForm = (type: RelationshipPreset) => {
    preSponsorshipFormRef.value?.openModal(type);
};

const handleHeroCTA = () => {
    const slide = props.heroSlides[heroCurrentSlide.value];
    if (!slide?.cta_link) {
        openRelationshipForm('father');
        return;
    }

    if (slide.cta_link.startsWith('modal:')) {
        const relation = slide.cta_link.replace('modal:', '') as RelationshipPreset;
        openRelationshipForm(relation);
        return;
    }

    if (slide.cta_link.startsWith('#')) {
        scrollToAnchor(slide.cta_link);
        return;
    }

    router.visit(slide.cta_link);
};

onMounted(() => {
    if (props.heroSlides.length > 1) {
        heroSlideInterval = setInterval(nextHeroSlide, 7000); // Change slide every 7 seconds
    }
});

onUnmounted(() => {
    if (heroSlideInterval) {
        clearInterval(heroSlideInterval);
    }
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
        (currentWallOfLoveSlide.value - 1 + props.wallOfLove.length) %
        props.wallOfLove.length;
};

onMounted(() => {
    if (props.wallOfLove.length > 1) {
        wallOfLoveSlideInterval = setInterval(nextWallOfLoveSlide, 5000); // Change slide every 5 seconds
    }
});

onUnmounted(() => {
    if (wallOfLoveSlideInterval) {
        clearInterval(wallOfLoveSlideInterval);
    }
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
        },
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

const { eldersWaiting, matchedElders, visitsThisMonth } = toRefs(
    props.liveCounters,
);

const { count: eldersWaitingCount, element: eldersWaitingRef } =
    useCountUp(eldersWaiting);
const { count: matchedEldersCount, element: matchedEldersRef } =
    useCountUp(matchedElders);
const { count: visitsThisMonthCount, element: visitsThisMonthRef } =
    useCountUp(visitsThisMonth);
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
        class="min-h-screen bg-background font-sans text-foreground antialiased"
    >
        <!-- Navbar -->
        <nav
            class="fixed top-0 right-0 left-0 z-50 bg-white/80 shadow-sm backdrop-blur-md dark:bg-gray-900/80"
        >
            <div
                class="container mx-auto flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8"
            >
                <Link
                    :href="route('home', undefined, false)"
                    class="flex items-center space-x-2"
                >
                    <img
                        src="/images/mekodonia-logo.svg"
                        alt="Mekodonia Logo"
                        class="h-8 w-auto"
                    />
                    <span
                        class="text-xl font-bold text-gray-900 dark:text-white"
                        >Mekodonia</span
                    >
                </Link>
                <div class="flex items-center space-x-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard', undefined, false)"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-gray-900"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login', undefined, false)"
                            class="text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="route('register', undefined, false)"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-gray-900"
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
                v-if="heroSlides.length"
                class="relative flex min-h-[90vh] items-center justify-center overflow-hidden bg-cover bg-center text-white"
                :style="{
                    backgroundImage: `url(${heroSlides[heroCurrentSlide].image})`,
                }"
            >
                <video
                    v-if="heroBackgroundVideo"
                    :src="heroBackgroundVideo"
                    autoplay
                    muted
                    loop
                    playsinline
                    class="absolute inset-0 h-full w-full object-cover"
                ></video>
                <div class="absolute inset-0 bg-black/75"></div>
                <div
                    class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-black/70 via-black/20 to-transparent"
                ></div>
                <div class="relative z-10 w-full px-4">
                    <div
                        class="mx-auto max-w-3xl rounded-3xl bg-black/60 p-6 text-center shadow-2xl backdrop-blur-sm md:p-10"
                    >
                        <p
                            class="text-xs uppercase tracking-[0.35em] text-indigo-200"
                        >
                            Mekodonia Home Connect
                        </p>
                        <h1
                            class="mt-4 text-4xl font-extrabold leading-snug text-white md:text-6xl"
                        >
                            {{ heroSlides[heroCurrentSlide].title }}
                        </h1>
                        <p
                            class="mt-4 text-base leading-relaxed text-slate-100 md:text-2xl"
                        >
                            {{ heroSlides[heroCurrentSlide].description }}
                        </p>
                        <div
                            class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-center"
                        >
                            <button
                                @click="handleHeroCTA"
                                class="w-full rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 px-8 py-3 text-lg font-semibold text-white shadow-lg transition hover:scale-[1.01] hover:from-indigo-600 hover:to-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none sm:w-auto"
                            >
                                {{ heroSlides[heroCurrentSlide].cta_text }}
                            </button>
                            <Link
                                href="#how-it-works"
                                class="w-full rounded-xl border border-white/30 px-8 py-3 text-lg font-semibold text-white/90 shadow-lg transition hover:bg-white/10 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none sm:w-auto"
                            >
                                Learn More
                            </Link>
                        </div>
                    </div>
                </div>
                <!-- Slider Indicators -->
                <div
                    class="absolute bottom-6 left-0 right-0 z-10 flex justify-center gap-2"
                >
                    <button
                        v-for="(_, index) in heroSlides"
                        :key="`dot-${index}`"
                        @click="goToHeroSlide(index)"
                        :class="[
                            'h-3 w-3 rounded-full border border-white/40 transition-colors duration-300',
                            heroCurrentSlide === index
                                ? 'bg-indigo-400'
                                : 'bg-white/30 hover:bg-white/60',
                        ]"
                    ></button>
                </div>
            </section>
            <section
                v-else
                class="flex h-[60vh] items-center justify-center bg-gradient-to-br from-indigo-600 to-purple-600 px-6 text-center text-white"
            >
                <div>
                    <h1 class="text-4xl font-bold">Mekodonia Home Connect</h1>
                    <p class="mt-4 text-lg">
                        Add hero slides from the dashboard to welcome donors with live stories.
                    </p>
                </div>
            </section>

            <!-- Relationship CTA Section -->
            <section
                id="relationship-cta"
                class="bg-white py-16 dark:bg-gray-900 shadow-inner"
            >
                <div class="container mx-auto px-4">
                    <div class="text-center">
                        <h2 class="text-4xl font-bold text-gray-800 dark:text-white">
                            Choose Your Connection
                        </h2>
                        <p class="mt-3 text-lg text-gray-600 dark:text-gray-300">
                            Select the relationship that resonates with you and start supporting an elder instantly.
                        </p>
                    </div>
                    <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                        <div
                            v-for="card in relationshipCards"
                            :key="card.relation"
                            class="flex flex-col rounded-2xl border border-gray-100 bg-gradient-to-br p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-gray-700/50 dark:bg-slate-900/70"
                            :class="card.accent"
                        >
                            <div
                                class="flex size-12 items-center justify-center rounded-xl bg-white/70 text-indigo-600 shadow-inner dark:bg-white/10 dark:text-indigo-300"
                            >
                                <HeartHandshake class="size-6" />
                            </div>
                            <h3 class="mt-4 text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ card.title }}
                            </h3>
                            <p class="mt-2 flex-1 text-sm text-gray-600 dark:text-gray-300">
                                {{ card.description }}
                            </p>
                            <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-indigo-600 dark:text-indigo-300">
                                {{ card.highlight }}
                            </p>
                            <div class="mt-6 flex flex-col gap-3">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
                                    @click="openRelationshipForm(card.relation)"
                                >
                                    Start as a {{ card.label }}
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-lg border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-50 dark:border-indigo-500/30 dark:text-indigo-300 dark:hover:bg-indigo-500/10"
                                    @click="scrollToAnchor('#elders-gallery')"
                                >
                                    Browse Elders
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Impact Section (Live Counters) -->
            <section class="bg-gray-50 py-20 dark:bg-gray-800">
                <div class="container mx-auto px-4 text-center">
                    <h2
                        class="text-4xl font-bold text-gray-800 dark:text-white"
                    >
                        Our Impact
                    </h2>
                    <p class="mt-4 text-xl text-gray-600 dark:text-gray-300">
                        Numbers that speak volumes about our mission.
                    </p>
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
                            <p
                                class="mt-6 text-5xl font-extrabold text-gray-800 dark:text-white"
                            >
                                {{ eldersWaitingCount }}
                            </p>
                            <p
                                class="mt-2 text-lg font-semibold text-gray-600 dark:text-gray-300"
                            >
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
                            <p
                                class="mt-6 text-5xl font-extrabold text-gray-800 dark:text-white"
                            >
                                {{ matchedEldersCount }}
                            </p>
                            <p
                                class="mt-2 text-lg font-semibold text-gray-600 dark:text-gray-300"
                            >
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
                            <p
                                class="mt-6 text-5xl font-extrabold text-gray-800 dark:text-white"
                            >
                                {{ visitsThisMonthCount }}
                            </p>
                            <p
                                class="mt-2 text-lg font-semibold text-gray-600 dark:text-gray-300"
                            >
                                Visits This Month
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- How It Works Section -->
            <section id="how-it-works" class="bg-white py-20 dark:bg-gray-900">
                <div class="container mx-auto px-4 text-center">
                    <h2
                        class="text-4xl font-bold text-gray-800 dark:text-white"
                    >
                        How It Works
                    </h2>
                    <p
                        class="mx-auto mt-4 max-w-3xl text-xl text-gray-600 dark:text-gray-300"
                    >
                        Connecting hearts, changing lives. Simple steps to make
                        a profound difference.
                    </p>
                    <div class="mt-12 grid grid-cols-1 gap-10 md:grid-cols-3">
                        <div
                            class="flex flex-col items-center rounded-xl bg-gray-50 p-6 shadow-lg dark:bg-gray-800"
                        >
                            <div
                                class="flex h-20 w-20 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300"
                            >
                                <User class="size-10" />
                            </div>
                            <h3
                                class="mt-6 text-xl font-semibold text-gray-800 dark:text-white"
                            >
                                1. Browse Elders
                            </h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Discover compelling stories of elders needing
                                support. Filter by location, needs, and more.
                            </p>
                        </div>
                        <div
                            class="flex flex-col items-center rounded-xl bg-gray-50 p-6 shadow-lg dark:bg-gray-800"
                        >
                            <div
                                class="flex h-20 w-20 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300"
                            >
                                <Heart class="size-10" />
                            </div>
                            <h3
                                class="mt-6 text-xl font-semibold text-gray-800 dark:text-white"
                            >
                                2. Choose Your Relationship
                            </h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Decide how you want to support – as a Father,
                                Mother, Brother, or Sister.
                            </p>
                        </div>
                        <div
                            class="flex flex-col items-center rounded-xl bg-gray-50 p-6 shadow-lg dark:bg-gray-800"
                        >
                            <div
                                class="flex h-20 w-20 items-center justify-center rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300"
                            >
                                <DollarSign class="size-10" />
                            </div>
                            <h3
                                class="mt-6 text-xl font-semibold text-gray-800 dark:text-white"
                            >
                                3. Make an Impact
                            </h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Set up monthly contributions or make a one-time
                                donation easily and securely.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Elder Gallery Section -->
            <section
                id="elders-gallery"
                class="bg-gray-50 py-20 dark:bg-gray-800"
            >
                <div class="container mx-auto px-4">
                    <h2
                        class="text-center text-4xl font-bold text-gray-800 dark:text-white"
                    >
                        Our Elders
                    </h2>
                    <p
                        class="mt-4 text-center text-xl text-gray-600 dark:text-gray-300"
                    >
                        Meet the elders whose lives you can transform.
                    </p>
                    <div
                        class="mt-10 flex flex-col items-center justify-between gap-4 md:flex-row"
                    >
                        <div
                            class="flex flex-1 items-center justify-center gap-4"
                        >
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
                                class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
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
                                <Link
                                    :href="
                                        route('elders.public.show', elder.id, false)
                                    "
                                >
                                    <div class="relative h-56 w-full">
                                        <img
                                            :src="elder.profile_photo_url"
                                            class="h-full w-full object-cover"
                                            alt="Elder profile"
                                        />
                                        <div
                                            class="absolute top-4 right-4 rounded-full bg-indigo-500 px-3 py-1 text-xs font-semibold text-white uppercase"
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
                                    'relative inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium',
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
            <section class="bg-white py-20 dark:bg-gray-900">
                <div class="container mx-auto px-4 text-center">
                    <h2
                        class="text-4xl font-bold text-gray-800 dark:text-white"
                    >
                        Wall of Love
                    </h2>
                    <p
                        class="mx-auto mt-4 max-w-3xl text-xl text-gray-600 dark:text-gray-300"
                    >
                        Heartwarming connections made possible by our community.
                    </p>
                    <div
                        v-if="wallOfLove.length > 0"
                        class="relative mt-12 overflow-hidden rounded-xl bg-gray-50 p-8 shadow-lg dark:bg-gray-800"
                    >
                        <div
                            class="flex transition-transform duration-700 ease-in-out"
                            :style="{
                                transform: `translateX(-${currentWallOfLoveSlide * 100}%)`,
                            }"
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
                                        :src="
                                            match.elder_profile_photo_url ??
                                            '/images/monk-mekodoniya.jpg'
                                        "
                                        alt="Elder Profile"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                                <p
                                    class="mt-6 text-xl font-semibold text-gray-800 dark:text-white"
                                >
                                    {{ match.donor_name }} is supporting
                                    {{ match.elder_name }}
                                </p>
                                <p
                                    class="mt-2 text-gray-600 dark:text-gray-300"
                                >
                                    Joined {{ match.sponsorship_date }}
                                </p>
                            </div>
                        </div>

                        <!-- Wall of Love Slider Controls -->
                        <button
                            @click="prevWallOfLoveSlide"
                            class="absolute top-1/2 left-4 -translate-y-1/2 rounded-full bg-white/50 p-2 text-gray-800 hover:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
                        >
                            &#10094;
                        </button>
                        <button
                            @click="nextWallOfLoveSlide"
                            class="absolute top-1/2 right-4 -translate-y-1/2 rounded-full bg-white/50 p-2 text-gray-800 hover:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
                        >
                            &#10095;
                        </button>
                        <!-- Wall of Love Slider Indicators -->
                        <div
                            class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 space-x-2"
                        >
                            <button
                                v-for="(_, index) in wallOfLove"
                                :key="`wall-dot-${index}`"
                                @click="currentWallOfLoveSlide = index"
                                :class="[
                                    'h-3 w-3 rounded-full transition-colors duration-300',
                                    currentWallOfLoveSlide === index
                                        ? 'bg-indigo-500'
                                        : 'bg-white/50 hover:bg-white',
                                ]"
                            ></button>
                        </div>
                    </div>
                    <div
                        v-else
                        class="mt-12 text-center text-gray-500 dark:text-gray-400"
                    >
                        <p class="text-xl">No success stories to share yet.</p>
                        <p class="mt-2">
                            Be the first to create a lasting bond!
                        </p>
                    </div>
                </div>
            </section>

            <!-- Call to Action Section -->
            <section class="bg-indigo-600 py-20 text-center text-white">
                <div class="container mx-auto px-4">
                    <h2 class="text-4xl font-bold">
                        Ready to Make a Difference?
                    </h2>
                    <p class="mx-auto mt-4 max-w-3xl text-xl">
                        Your support can provide comfort, dignity, and care for
                        our elders. Join our mission today.
                    </p>
                    <div class="mt-8 space-x-4">
                        <Link
                            :href="route('register', undefined, false)"
                            class="rounded-lg bg-white px-8 py-3 text-lg font-semibold text-indigo-600 shadow-lg transition-all hover:bg-gray-100 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:outline-none"
                        >
                            Sponsor an Elder
                        </Link>
                        <Link
                            :href="route('guest.donation', undefined, false)"
                            class="rounded-lg border border-white px-8 py-3 text-lg font-semibold text-white shadow-lg transition-all hover:bg-white/20 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:outline-none"
                        >
                            Make a One-Time Donation
                        </Link>
                    </div>
                </div>
            </section>

            <!-- FAQ & Trust Badges Section -->
            <section class="bg-gray-50 py-20 dark:bg-gray-800">
                <div class="container mx-auto px-4">
                    <h2
                        class="text-center text-4xl font-bold text-gray-800 dark:text-white"
                    >
                        Frequently Asked Questions
                    </h2>
                    <div class="mx-auto mt-12 max-w-4xl space-y-8">
                        <!-- FAQ Item 1 -->
                        <div>
                            <h3
                                class="text-xl font-semibold text-gray-800 dark:text-white"
                            >
                                How can I sponsor an elder?
                            </h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                You can browse our Elder Gallery, select an
                                elder you wish to support, and choose a
                                sponsorship relationship (Father, Mother,
                                Brother, Sister). You can then set up a monthly
                                contribution.
                            </p>
                        </div>
                        <!-- FAQ Item 2 -->
                        <div>
                            <h3
                                class="text-xl font-semibold text-gray-800 dark:text-white"
                            >
                                Can I make a one-time donation?
                            </h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Yes, you can make a one-time donation to provide
                                essential items like meals or medication without
                                committing to a long-term sponsorship. Look for
                                the "Make a One-Time Donation" button.
                            </p>
                        </div>
                        <!-- FAQ Item 3 -->
                        <div>
                            <h3
                                class="text-xl font-semibold text-gray-800 dark:text-white"
                            >
                                Is my donation secure?
                            </h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                We utilize secure payment gateways and protocols
                                to ensure your donations are processed safely.
                                Your financial information is always protected.
                            </p>
                        </div>
                        <!-- FAQ Item 4 -->
                        <div>
                            <h3
                                class="text-xl font-semibold text-gray-800 dark:text-white"
                            >
                                How do I track my impact?
                            </h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                Registered donors can access a personalized
                                dashboard to view their payment history, details
                                of their sponsored elder, and receive annual
                                thank-you summaries.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 py-12 text-white dark:bg-gray-950">
            <div class="container mx-auto px-4 text-center">
                <p>
                    &copy; {{ new Date().getFullYear() }} Mekodonia Home
                    Connect. All rights reserved.
                </p>
                <div class="mt-4 space-x-4">
                    <a href="#" class="hover:text-gray-300">Privacy Policy</a>
                    <a href="#" class="hover:text-gray-300">Terms of Service</a>
                    <a href="#" class="hover:text-gray-300">Contact Us</a>
                </div>
            </div>
        </footer>
    </div>
    <PreSponsorshipForm ref="preSponsorshipFormRef" />
</template>
