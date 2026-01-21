<script setup lang="ts">
import GlassCard from '@/components/GlassCard.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, HeartHandshake, MapPin, Shield } from 'lucide-vue-next';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps<{
    elder: {
        id: number;
        first_name: string;
        last_name: string;
        profile_photo_url: string;
        priority_level: string;
        bio: string | null;
        special_needs: string | null;
        branch: {
            name: string;
        };
    };
}>();

const donationHref = route('guest.donation', undefined, false);
const homeHref = route('home', undefined, false);

const priorityChip = computed(() => {
    const level = props.elder.priority_level?.toLowerCase() ?? 'medium';
    const map: Record<string, { label: string; tone: string }> = {
        high: {
            label: 'High Priority',
            tone: 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-100',
        },
        medium: {
            label: 'Medium Priority',
            tone: 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-100',
        },
        low: {
            label: 'Low Priority',
            tone: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-100',
        },
    };

    return (
        map[level] ?? map.medium
    );
});
</script>

<template>
    <Head :title="`${props.elder.first_name} ${props.elder.last_name}`" />

    <GuestLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <GlassCard>
                    <div class="mb-6 flex items-center justify-between gap-4">
                        <Link
                            :href="homeHref"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800"
                        >
                            <ArrowLeft class="size-4" />
                            Back to Welcome
                        </Link>
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase"
                            :class="priorityChip.tone"
                        >
                            {{ priorityChip.label }}
                        </span>
                    </div>
                    <div class="flex flex-col gap-10 lg:flex-row">
                        <div class="lg:w-2/5">
                            <div class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-slate-900/60">
                                <img
                                    :src="props.elder.profile_photo_url"
                                    class="h-80 w-full object-cover"
                                    :alt="props.elder.first_name"
                                />
                                <div class="space-y-3 px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <HeartHandshake class="size-5 text-indigo-500" />
                                        <div>
                                            <p class="text-xs uppercase tracking-[0.35em] text-slate-500">
                                                Elder Profile
                                            </p>
                                            <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">
                                                {{ props.elder.first_name }} {{ props.elder.last_name }}
                                            </h1>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex items-start gap-3 text-sm text-slate-600 dark:text-slate-300">
                                            <MapPin class="mt-0.5 size-4 text-indigo-400" />
                                            <div>
                                                <p class="text-xs uppercase tracking-wide text-slate-500">
                                                    Branch
                                                </p>
                                                <p class="font-semibold">
                                                    {{ props.elder.branch.name }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-3 text-sm text-slate-600 dark:text-slate-300">
                                            <Shield class="mt-0.5 size-4 text-indigo-400" />
                                            <div>
                                                <p class="text-xs uppercase tracking-wide text-slate-500">
                                                    Priority Need
                                                </p>
                                                <p class="font-semibold capitalize">
                                                    {{ props.elder.priority_level }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 space-y-8">
                            <section>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                                    Story
                                </h3>
                                <p class="mt-3 text-base leading-relaxed text-slate-600 dark:text-slate-300">
                                    {{
                                        props.elder.bio ||
                                        'This elder is waiting for a family connection. Their full biography will be added by branch staff soon.'
                                    }}
                                </p>
                            </section>

                            <section v-if="props.elder.special_needs">
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                                    Current Needs
                                </h3>
                                <div class="mt-3 rounded-2xl border border-amber-100 bg-amber-50/80 p-4 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">
                                    {{ props.elder.special_needs }}
                                </div>
                            </section>

                            <section>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                                    Choose How to Help
                                </h3>
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                    Start a recurring relationship or cover an immediate need.
                                </p>
                                <div class="mt-4 flex flex-wrap gap-3">
                                    <Link
                                        :href="`${donationHref}?elder_id=${props.elder.id}&mode=sponsorship`"
                                        class="inline-flex items-center rounded-full bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    >
                                        Sponsor {{ props.elder.first_name }}
                                    </Link>
                                    <Link
                                        :href="`${donationHref}?elder_id=${props.elder.id}&mode=one_time`"
                                        class="inline-flex items-center rounded-full border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800/50"
                                    >
                                        Fund a Meal Today
                                    </Link>
                                </div>
                            </section>
                        </div>
                    </div>
                </GlassCard>
            </div>
        </div>
    </GuestLayout>
</template>
