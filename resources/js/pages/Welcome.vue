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
import { onMounted, onUnmounted, ref, toRefs, watch, computed } from 'vue';
import { route } from 'ziggy-js';

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
    relationship_type?: RelationshipPreset | null;
    branch_name?: string | null;
    location?: string | null;
    funding_goal?: number;
    funding_received?: number;
    funding_progress?: number;
    funding_needed?: number;
    is_funded?: boolean;
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

const heroQuickActions = [
    { label: 'Find a Father', relation: 'father' as RelationshipPreset },
    { label: 'Find a Mother', relation: 'mother' as RelationshipPreset },
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
        relationship?: string;
        include_funded?: boolean;
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
const currentRelationship = ref(props.filters.relationship || '');
const includeFunded = ref(!!props.filters.include_funded);
const heroBackgroundVideo = '/images/6096-188704568_small.mp4';

const heroSlidesFallback = [
    {
        title: 'Mekodonia Home Connect',
        description:
            'Support elders with a simple click—sponsor an elder or donate a meal today.',
        image: '/images/hero-father.svg',
        cta_text: 'See How It Works',
        cta_link: '#how-it-works',
    },
];

const currencyFormatter = new Intl.NumberFormat('en-ET', {
    style: 'currency',
    currency: 'ETB',
    maximumFractionDigits: 0,
});

const formatCurrency = (value?: number | null) =>
    currencyFormatter.format(value ?? 0);

const progressPercent = (value?: number) => {
    const percent = Math.round((value ?? 0) * 100);
    return `${Math.min(100, Math.max(0, percent))}%`;
};

const heroSlidesToShow = computed(() =>
    props.heroSlides.length ? props.heroSlides : heroSlidesFallback,
);

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

const guestMealDonationHref = route(
    'guest.donation',
    { mode: 'one_time' },
    false,
);

const relationshipPresets: RelationshipPreset[] = [
    'father',
    'mother',
    'brother',
    'sister',
];

const normalizeRelationshipParam = (
    value?: string | null,
): string | undefined => {
    if (!value) {
        return undefined;
    }

    return relationshipPresets.includes(value as RelationshipPreset)
        ? (value as RelationshipPreset)
        : undefined;
};

const buildGuestDonationParams = (
    relationship?: string,
    elderId?: number,
) => {
    const params: Record<string, string> = {
        mode: 'sponsorship',
    };

    if (relationship) {
        params.relationship = relationship;
    }

    if (elderId !== undefined) {
        params.elder_id = elderId.toString();
    }

    return params;
};

const openGuestDonation = (relationship?: string, elderId?: number) => {
    router.visit(
        route(
            'guest.donation',
            buildGuestDonationParams(relationship, elderId),
            false,
        ),
    );
};

const openSponsorThisElder = (elder: ElderSummary) => {
    if (elder.is_funded) {
        return;
    }

    const candidateRelation =
        elder.relationship_type ?? currentRelationship.value;
    const relationship = normalizeRelationshipParam(candidateRelation);
    openGuestDonation(relationship, elder.id);
};

const heroHighlights = [
    {
        title: 'Verified Elders',
        description: 'Every profile passes through our caregivers so donors trust the stories.',
        icon: User,
    },
    {
        title: 'Transparent Support',
        description: 'Monthly gifts map to living essentials, therapy, and visits we track for you.',
        icon: Heart,
    },
    {
        title: 'Responsive Team',
        description: 'Our staff follow through on questions, delivery, and thank-you updates.',
        icon: Users,
    },
];

const donorJourneySteps = [
    {
        title: 'Discover a family role',
        description:
            'Filter elders by relationship, priority, and location so you can sponsor someone aligned with your heart.',
        icon: UserCheck,
    },
    {
        title: 'Choose a story',
        description:
            'Review elder stories, needs, and priority levels—each card links to the public profile before you commit.',
        icon: HeartHandshake,
    },
    {
        title: 'Seal the commitment',
        description:
            'Click “Sponsor this elder” to open the guest form pre-filled with your elder and relationship.',
        icon: DollarSign,
    },
];

const formatRelationshipLabel = (relation?: RelationshipPreset) => {
    if (!relation) {
        return 'All relationships';
    }

    const card = relationshipCards.find((item) => item.relation === relation);
    return card?.label ?? relation.charAt(0).toUpperCase() + relation.slice(1);
};

const formatPriorityLabel = (value: string) =>
    priorityOptions.find((option) => option.value === value)?.label ?? value;

const formatGenderLabel = (value: string) =>
    genderOptions.find((option) => option.value === value)?.label ?? value;

const activeFilterBadges = computed(() => {
    const filters: string[] = [];
    if (currentRelationship.value) {
        filters.push(`Relationship: ${formatRelationshipLabel(currentRelationship.value)}`);
    }
    if (currentPriority.value) {
        filters.push(`Priority: ${formatPriorityLabel(currentPriority.value)}`);
    }
    if (currentGender.value) {
        filters.push(`Gender: ${formatGenderLabel(currentGender.value)}`);
    }
    return filters;
});

const hasActiveFilters = computed(() => activeFilterBadges.value.length > 0);

const clearFilters = () => {
    currentRelationship.value = '';
    currentPriority.value = '';
    currentGender.value = '';
    includeFunded.value = false;
    applyFilters();
};

const applyFilters = () => {
    // Keep filters in the query string so guests can bookmark/share filtered galleries.
    router.get(
        route('home'),
        {
            priority: currentPriority.value,
            gender: currentGender.value,
            relationship: currentRelationship.value,
            include_funded: includeFunded.value ? 1 : undefined,
        },
        {
            replace: true,
            preserveState: true,
            onSuccess: () => {
                scrollToAnchor('#elders-gallery');
            },
        },
    );
};

const toggleIncludeFunded = () => {
    includeFunded.value = !includeFunded.value;
    applyFilters();
};

watch(
    () => props.filters.relationship,
    (value) => {
        currentRelationship.value = value ?? '';
    },
);

watch(
    () => props.filters.priority,
    (value) => {
        currentPriority.value = value ?? '';
    },
);

watch(
    () => props.filters.gender,
    (value) => {
        currentGender.value = value ?? '';
    },
);

watch(
    () => props.filters.include_funded,
    (value) => {
        includeFunded.value = Boolean(value);
    },
);

const isRelationshipActive = (relation: RelationshipPreset) =>
    currentRelationship.value === relation;

// Hero Slider Logic
const heroCurrentSlide = ref(0);
let heroSlideInterval: number;

const nextHeroSlide = () => {
    const length = heroSlidesToShow.value.length;
    if (!length) {
        return;
    }

    heroCurrentSlide.value = (heroCurrentSlide.value + 1) % length;
};

const goToHeroSlide = (index: number) => {
    const length = heroSlidesToShow.value.length || 1;
    heroCurrentSlide.value = index % length;
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

const openRelationshipGallery = (relation: RelationshipPreset = 'father') => {
    currentRelationship.value = relation;
    router.get(
        route('home'),
        {
            relationship: relation,
            include_funded: includeFunded.value ? 1 : undefined,
        },
        {
            replace: true,
            preserveScroll: false,
            onSuccess: () => {
                scrollToAnchor('#elders-gallery');
            },
        },
    );
};

watch(
    heroSlidesToShow,
    (slides) => {
        if (heroCurrentSlide.value >= slides.length) {
            heroCurrentSlide.value = 0;
        }
    },
);

const handleHeroCTA = () => {
    const slide = heroSlidesToShow.value[heroCurrentSlide.value];
    if (!slide?.cta_link) {
        openRelationshipGallery('father');
        return;
    }

    if (slide.cta_link.startsWith('modal:')) {
        const relation = slide.cta_link.replace('modal:', '') as RelationshipPreset;
        openRelationshipGallery(relation);
        return;
    }

    if (slide.cta_link.startsWith('#')) {
        scrollToAnchor(slide.cta_link);
        return;
    }

    router.visit(slide.cta_link);
};

onMounted(() => {
    if (heroSlidesToShow.value.length > 1) {
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
            <template v-if="heroSlidesToShow.length">
                <section
                    class="relative flex min-h-[90vh] items-end justify-center overflow-hidden bg-cover bg-center text-white pt-16 pb-16"
                    :style="{
                        backgroundImage: `url(${heroSlidesToShow[heroCurrentSlide].image})`,
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
                <div class="relative z-10 w-full px-4 pb-10">
                    <div
                        class="mx-auto max-w-4xl rounded-3xl bg-black/60 p-4 text-white shadow-2xl backdrop-blur-sm md:p-6"
                    >
                        <div
                            class="grid gap-6 md:grid-cols-[minmax(0,1fr)_minmax(auto,24rem)] md:items-center md:text-left"
                        >
                            <div>
                                <p class="text-[10px] uppercase tracking-[0.4em] text-indigo-200">
                                    Mekodonia Home Connect
                                </p>
                                <h1 class="mt-2 text-3xl font-extrabold leading-snug text-white md:text-4xl">
                                    {{ heroSlidesToShow[heroCurrentSlide].title }}
                                </h1>
                                <p class="mt-2 text-sm leading-relaxed text-slate-100 md:text-base">
                                    {{ heroSlidesToShow[heroCurrentSlide].description }}
                                </p>
                                <p class="mt-4 text-sm text-indigo-100">
                                    Expert caregivers and donors unite here—every pledge is tracked, acknowledged, and reported.
                                </p>
                            </div>
                            <div class="flex flex-col gap-3 md:items-end">
                                <button
                                    @click="handleHeroCTA"
                                    class="w-full rounded-full bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-3 text-sm font-semibold uppercase tracking-[0.35em] text-white shadow-lg transition hover:scale-[1.01] hover:from-indigo-600 hover:to-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
                                >
                                    {{ heroSlidesToShow[heroCurrentSlide].cta_text }}
                                </button>
                                <button
                                    type="button"
                                    class="w-full rounded-full border border-white/30 px-6 py-3 text-sm font-semibold uppercase tracking-[0.35em] text-white/90 shadow-lg transition hover:bg-white/10 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
                                    @click="scrollToAnchor('#how-it-works')"
                                >
                                    Learn More
                                </button>
                            </div>
                        </div>
                    </div>
                    <div
                        v-if="heroQuickActions.length"
                        class="relative z-10 mt-4 flex flex-wrap items-center justify-center gap-3 text-[10px] font-semibold uppercase tracking-[0.5em] text-white"
                    >
                            <div class="flex gap-2">
                                <button
                                    v-for="action in heroQuickActions"
                                    :key="action.label"
                                    type="button"
                                    class="rounded-full border border-white/60 bg-white/90 px-3 py-1 text-[10px] font-bold text-slate-900 transition hover:border-white"
                                    @click="openRelationshipGallery(action.relation)"
                                >
                                    {{ action.label }}
                                </button>
                            </div>
                        <span class="hidden text-white/60 sm:inline">|</span>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="rounded-full border border-white/60 px-3 py-1 text-[10px] uppercase text-white transition hover:bg-white/20"
                                @click="scrollToAnchor('#elders-gallery')"
                            >
                                Browse Elders
                            </button>
                            <Link
                                :href="guestMealDonationHref"
                                class="rounded-full border border-white/60 px-3 py-1 text-[10px] uppercase text-white transition hover:bg-white/20"
                            >
                                Donate a Meal
                            </Link>
                        </div>
                    </div>
                    <div
                        v-if="hasActiveFilters"
                        class="relative z-10 mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold uppercase tracking-[0.3em] text-white"
                    >
                        <span class="text-white/70">Filtered by</span>
                        <span
                            v-for="filter in activeFilterBadges"
                            :key="filter"
                            class="rounded-full border border-white/40 bg-white/10 px-3 py-1 text-[9px] text-white/90"
                        >
                            {{ filter }}
                        </span>
                        <button
                            type="button"
                            @click="clearFilters"
                            class="rounded-full border border-white/60 px-3 py-1 text-[10px] uppercase text-white transition hover:bg-white/20"
                        >
                            Clear
                        </button>
                    </div>
                </div>
                <!-- Slider Indicators -->
                <div
                    class="absolute bottom-6 left-0 right-0 z-10 flex justify-center gap-2"
                >
                            <button
                                v-for="(_, index) in heroSlidesToShow"
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

                <section class="bg-slate-900 py-10">
                    <div class="container mx-auto px-4">
                        <div class="flex flex-col gap-4 rounded-2xl bg-gradient-to-r from-indigo-900/90 via-slate-900 to-slate-900/80 p-6 text-center text-white shadow-2xl sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.5em] text-indigo-300">Returning donors</p>
                                <p class="mt-1 text-xl font-semibold">Log in to access your dashboard & giving history</p>
                                <p class="text-sm text-indigo-200">
                                    Use the phone number and password you received on your thank-you screen to view receipts, manage pledges, or email proofs.
                                </p>
                            </div>
                            <div class="flex flex-wrap items-center justify-center gap-3">
                                <Link
                                    class="rounded-full border border-white/60 px-5 py-2 text-xs font-semibold uppercase tracking-[0.4em] text-white transition hover:border-indigo-200 hover:text-indigo-100"
                                    :href="route('login', undefined, false)"
                                >
                                    Log in
                                </Link>
                                <Link
                                    class="rounded-full border border-white/60 bg-white/10 px-5 py-2 text-xs font-semibold uppercase tracking-[0.4em] text-white transition hover:border-white hover:bg-white/20"
                                    :href="route('donors.donations.index', undefined, false)"
                                >
                                    View my donations
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-indigo-50 py-10 dark:bg-slate-900/80">
                    <div class="container mx-auto px-4">
                        <div class="flex flex-col gap-4 rounded-2xl border border-indigo-100 bg-white/90 p-6 shadow-lg dark:border-slate-700 dark:bg-slate-900/70 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.5em] text-indigo-600 dark:text-indigo-300">
                                    Ready to join the circle?
                                </p>
                                <p class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">
                                    Create a donor account and unlock your personalized dashboard.
                                </p>
                                <p class="text-sm text-slate-500 dark:text-slate-300">
                                    We’ll assign the External/Donor role, sync your phone/name, and keep every pledge organized in one place.
                                </p>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <Link
                                    :href="`${route('register', undefined, false)}?role=donor`"
                                    class="rounded-full border border-indigo-500/70 bg-indigo-500/10 px-5 py-2 text-xs font-semibold uppercase tracking-[0.4em] text-indigo-700 transition hover:bg-indigo-500/20 dark:border-indigo-400/60 dark:bg-indigo-500/10 dark:text-indigo-200"
                                >
                                    Register as donor
                                </Link>
                                <Link
                                    :href="route('login', undefined, false)"
                                    class="rounded-full border border-slate-200 px-5 py-2 text-xs font-semibold uppercase tracking-[0.4em] text-slate-700 transition hover:border-indigo-500 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-300 dark:hover:text-indigo-300"
                                >
                                    Already have creds?
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>

                <section
                    id="trust-signals"
                    class="bg-white py-16 dark:bg-gray-900"
                >
                <div class="container mx-auto px-4 text-center">
                    <p class="text-sm uppercase tracking-[0.5em] text-indigo-600 dark:text-indigo-300">
                        Trust Signals
                    </p>
                    <h2 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white md:text-4xl">
                        How we safeguard every sponsorship
                    </h2>
                    <p class="mx-auto mt-3 max-w-3xl text-lg text-gray-600 dark:text-gray-300">
                        Every donor and elder benefit from a structured care pathway—the cards below explain how we verify, track, and support each relationship.
                    </p>
                    <div class="mt-10 grid gap-6 md:grid-cols-3">
                        <article
                            v-for="highlight in heroHighlights"
                            :key="highlight.title"
                            class="flex flex-col gap-3 rounded-3xl border border-gray-100/70 bg-gray-50 p-6 text-left shadow-sm transition hover:border-indigo-400 hover:bg-white dark:border-gray-800 dark:bg-gray-800/60"
                        >
                            <div
                                class="flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-200"
                            >
                                <component :is="highlight.icon" class="size-6" />
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ highlight.title }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ highlight.description }}
                            </p>
                            <p class="text-xs uppercase tracking-[0.4em] text-gray-500 dark:text-gray-400">
                                Verified quality signal
                            </p>
                        </article>
                    </div>
                </div>
            </section>
            </template>
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

            <!-- How Support Works Section -->
            <section id="how-it-works" class="bg-white py-20 dark:bg-gray-900">
                <div class="container mx-auto px-4 text-center">
                    <h2 class="text-4xl font-bold text-gray-800 dark:text-white">
                        How Support Happens
                    </h2>
                    <p class="mx-auto mt-4 max-w-3xl text-xl text-gray-600 dark:text-gray-300">
                        Every gift passes through a caring workflow so donors can see impact, staff can coordinate care, and elders receive essentials on time.
                    </p>
                    <div class="mt-12 grid grid-cols-1 gap-10 md:grid-cols-3">
                        <div class="flex flex-col items-center rounded-xl bg-gray-50 p-6 shadow-lg dark:bg-gray-800">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                                <User class="size-10" />
                            </div>
                                <h3 class="mt-6 text-xl font-semibold text-gray-800 dark:text-white">
                                    1. Discover the need
                                </h3>
                                <p class="mt-2 text-gray-600 dark:text-gray-300">
                                    Explore elder stories curated by the care team, with priority levels, photos, and needs transparent at a glance.
                                </p>
                        </div>
                        <div class="flex flex-col items-center rounded-xl bg-gray-50 p-6 shadow-lg dark:bg-gray-800">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300">
                                <Heart class="size-10" />
                            </div>
                                <h3 class="mt-6 text-xl font-semibold text-gray-800 dark:text-white">
                                    2. Match by relationship
                                </h3>
                                <p class="mt-2 text-gray-600 dark:text-gray-300">
                                    Pick the role that resonates—father, mother, brother, or sister—and we frame the needs list around that commitment.
                                </p>
                        </div>
                        <div class="flex flex-col items-center rounded-xl bg-gray-50 p-6 shadow-lg dark:bg-gray-800">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300">
                                <DollarSign class="size-10" />
                            </div>
                                <h3 class="mt-6 text-xl font-semibold text-gray-800 dark:text-white">
                                    3. Confirm the commitment
                                </h3>
                                <p class="mt-2 text-gray-600 dark:text-gray-300">
                                    Complete the guest donation form with the elder, relationship, and payment details—you’ll receive a receipt plus thank-you updates.
                                </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Donor Journey Section -->
            <section
                id="guest-journey"
                class="bg-gradient-to-br from-indigo-50 to-purple-50 py-16 text-gray-900 dark:bg-gray-900/50 dark:text-white"
            >
            <div class="container mx-auto px-4 text-center">
                    <p class="text-sm uppercase tracking-[0.6em] text-indigo-600 dark:text-indigo-300">
                        Donor Journey
                    </p>
                    <h2 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white md:text-4xl">
                        From curiosity to commitment
                    </h2>
                    <p class="mx-auto mt-3 max-w-3xl text-lg text-gray-600 dark:text-gray-300">
                        Narrow the search, read the stories, and finalize your pledge with confidence—our team handles the rest.
                    </p>
                    <div class="mt-10 grid gap-6 md:grid-cols-3">
                        <article
                            v-for="step in donorJourneySteps"
                            :key="step.title"
                            class="flex flex-col gap-3 rounded-3xl border border-indigo-100/50 bg-white/70 p-6 shadow-sm transition hover:border-indigo-400 hover:shadow-lg dark:border-indigo-900/60 dark:bg-gray-900/60"
                        >
                            <div
                                class="flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-200"
                            >
                                <component :is="step.icon" class="size-6" />
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ step.title }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ step.description }}
                            </p>
                        </article>
                    </div>
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        <button
                            type="button"
                            class="rounded-full bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
                            @click="scrollToAnchor('#elders-gallery')"
                        >
                            Browse Elders
                        </button>
                        <button
                            type="button"
                            class="rounded-full border border-indigo-600 px-6 py-3 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50 dark:border-indigo-300 dark:text-indigo-200 dark:hover:bg-indigo-500/20"
                            @click="openRelationshipGallery('father')"
                        >
                            Find a Father
                        </button>
                    </div>
                </div>
            </section>

            <!-- Relationship CTA Section -->
            <section
                id="relationship-cta"
                class="bg-white py-16 dark:bg-gray-900 shadow-inner"
            >
                <div class="container mx-auto px-4 text-center">
                    <h2 class="text-4xl font-bold text-gray-800 dark:text-white">
                        Choose Your Connection
                    </h2>
                    <p class="mt-3 text-lg text-gray-600 dark:text-gray-300">
                        Select the relationship that resonates with you and start supporting an elder instantly.
                    </p>
                    <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                        <div
                            v-for="card in relationshipCards"
                            :key="card.relation"
                            class="flex flex-col rounded-2xl border border-gray-100 bg-gradient-to-br p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-gray-700/50 dark:bg-slate-900/70"
                            :class="[card.accent, isRelationshipActive(card.relation) ? 'ring-2 ring-indigo-400/70 dark:ring-indigo-300/50 shadow-lg' : '']"
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
                                    @click="openGuestDonation(card.relation)"
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
            <section class="bg-gradient-to-br from-indigo-100 via-white to-purple-100 py-24 dark:bg-gradient-to-br dark:from-gray-900 dark:via-gray-950 dark:to-gray-900">
                <div class="container mx-auto px-4 text-center">
                    <p class="text-sm uppercase tracking-[0.4em] text-indigo-600 dark:text-indigo-300">
                        Impact Metrics
                    </p>
                    <h2 class="mt-2 text-4xl font-extrabold text-gray-900 dark:text-white">
                        Stories told through numbers
                    </h2>
                    <p class="mx-auto mt-4 max-w-3xl text-lg text-gray-600 dark:text-gray-300">
                        Every stat below represents elders supported, visits completed, and the hearts that keep Mekodonia Home Connect alive.
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
                            <button
                                type="button"
                                @click="toggleIncludeFunded"
                                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-indigo-500 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-300 dark:hover:text-indigo-300"
                            >
                                {{
                                    includeFunded
                                        ? 'Hide fully funded elders'
                                        : 'Show fully funded elders'
                                }}
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
                                <div class="relative h-56 w-full">
                                    <Link
                                        :href="
                                            route('elders.public.show', elder.id, false)
                                        "
                                        class="relative block h-full w-full"
                                    >
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
                                    </Link>
                                </div>
                                <div class="p-6">
                                    <Link
                                        :href="
                                            route('elders.public.show', elder.id, false)
                                        "
                                        class="inline-block"
                                    >
                                        <h3
                                            class="text-2xl font-bold text-gray-900 dark:text-white"
                                        >
                                            {{ elder.full_name }}
                                        </h3>
                                    </Link>
                                    <p
                                        class="mt-2 text-gray-600 dark:text-gray-300"
                                    >
                                        Awaiting your compassionate support.
                                    </p>
                                    <div
                                        v-if="
                                            elder.relationship_type ||
                                            elder.branch_name ||
                                            elder.location
                                        "
                                        class="mt-3 flex flex-wrap gap-2 text-[11px] text-slate-500 dark:text-slate-400"
                                    >
                                        <span
                                            v-if="elder.relationship_type"
                                            class="font-semibold text-slate-700 dark:text-white"
                                        >
                                            Relationship:
                                            {{
                                                formatRelationshipLabel(
                                                    elder.relationship_type,
                                                )
                                            }}
                                        </span>
                                        <span
                                            v-if="elder.branch_name"
                                            class="font-semibold text-slate-700 dark:text-white"
                                        >
                                            Branch: {{ elder.branch_name }}
                                        </span>
                                        <span
                                            v-if="elder.location"
                                            class="font-semibold text-slate-700 dark:text-white"
                                            >
                                            Location: {{ elder.location }}
                                        </span>
                                    </div>
                                    <div
                                        v-if="elder.funding_goal && elder.funding_goal > 0"
                                        class="mt-3 space-y-2 text-[11px] text-slate-500 dark:text-slate-400"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-slate-700 dark:text-white">
                                                Goal: {{ formatCurrency(elder.funding_goal) }}
                                            </span>
                                            <span
                                                :class="elder.is_funded
                                                    ? 'text-emerald-600 dark:text-emerald-300'
                                                    : 'text-amber-600 dark:text-amber-300'"
                                            >
                                                {{
                                                    elder.is_funded
                                                        ? 'Fully funded'
                                                        : `Remaining ${formatCurrency(
                                                              elder.funding_needed,
                                                          )}`
                                                }}
                                            </span>
                                        </div>
                                        <div class="relative h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                                            <div
                                                class="h-full rounded-full transition-all"
                                                :class="elder.is_funded
                                                    ? 'bg-emerald-500 dark:bg-emerald-400'
                                                    : 'bg-indigo-500 dark:bg-indigo-400'"
                                                :style="{ width: progressPercent(elder.funding_progress) }"
                                            ></div>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex flex-wrap items-center gap-3">
                                        <Link
                                            :href="
                                                route('elders.public.show', elder.id, false)
                                            "
                                            class="inline-flex items-center text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200"
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
                                        </Link>
                                        <button
                                            type="button"
                                            @click="openSponsorThisElder(elder)"
                                            :disabled="elder.is_funded"
                                            class="rounded-full border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-100 dark:border-indigo-500/30 dark:bg-indigo-500/10 dark:text-indigo-300 dark:hover:bg-indigo-500/20 disabled:border-slate-200 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed"
                                        >
                                            {{ elder.is_funded ? 'Campaign closed' : 'Sponsor this elder' }}
                                        </button>
                                        <p
                                            v-if="elder.is_funded"
                                            class="text-[11px] font-semibold text-emerald-600"
                                        >
                                            Campaign goal achieved—thank you!
                                        </p>
                                    </div>
                                </div>
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
                            :href="route('guest.donation', { mode: 'one_time' }, false)"
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
</template>
