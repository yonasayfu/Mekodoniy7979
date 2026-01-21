<script setup lang="ts">
import GlassButton from '@/components/GlassButton.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps<{
    campaign: {
        id: number;
        title: string;
        slug: string;
        description: string | null;
        short_description: string | null;
        cta_label: string;
        cta_url: string;
        accent_color: string;
        hero_image_url: string;
        goal_amount: number | null;
        goal_currency: string | null;
        raised_amount: number;
        donor_count: number;
        progress_percent: number | null;
        featured_video_url: string | null;
    };
    urgentElders: Array<{
        id: number;
        name: string;
        priority_level: string;
        profile_photo_url: string;
        branch?: string | null;
    }>;
    share: {
        url: string;
    };
}>();

const donateUrl = computed(() => {
    if (props.campaign.cta_url) {
        return props.campaign.cta_url;
    }

    return route('guest.donation', { campaign_id: props.campaign.id }, false);
});

const progressPercent = computed(() => props.campaign.progress_percent ?? 0);
</script>

<template>
    <Head :title="campaign.title" />

    <GuestLayout>
        <div class="flex flex-col">
            <section
                class="relative overflow-hidden bg-slate-900 text-white"
                :style="{ '--accent': campaign.accent_color ?? '#2563eb' }"
            >
                <img
                    :src="campaign.hero_image_url"
                    alt=""
                    class="absolute inset-0 h-full w-full object-cover opacity-70"
                />
                <div class="absolute inset-0 bg-slate-900/70"></div>
                <div class="relative mx-auto flex w-full max-w-5xl flex-col gap-8 px-4 py-16 sm:px-6 lg:px-8">
                    <div class="space-y-4">
                        <p class="text-xs uppercase tracking-[0.35em] text-white/60">
                            Mekodonia Home Connect
                        </p>
                        <h1 class="text-4xl font-bold leading-tight sm:text-5xl">
                            {{ campaign.title }}
                        </h1>
                        <p class="max-w-3xl text-lg text-white/90">
                            {{ campaign.short_description ?? campaign.description }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <GlassButton
                            as="span"
                            size="lg"
                            variant="primary"
                            class="bg-[var(--accent)] text-white hover:opacity-90"
                        >
                            <Link :href="donateUrl" class="flex items-center gap-2">
                                {{ campaign.cta_label }}
                            </Link>
                        </GlassButton>
                        <a
                            :href="share.url"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center text-sm font-semibold text-white/80 hover:text-white"
                        >
                            Share campaign →
                        </a>
                    </div>
                    <div class="w-full rounded-2xl bg-white/15 p-4 backdrop-blur">
                        <p class="text-sm font-medium text-white/80">
                            Raised {{ campaign.raised_amount.toLocaleString(undefined, { maximumFractionDigits: 0 }) }}
                            {{ campaign.goal_currency ?? 'ETB' }}
                            <span v-if="campaign.goal_amount"> of {{ campaign.goal_amount.toLocaleString(undefined, { maximumFractionDigits: 0 }) }}</span>
                        </p>
                        <div class="mt-2 h-3 rounded-full bg-white/20">
                            <div
                                class="h-full rounded-full bg-white transition-all"
                                :style="{ width: `${progressPercent}%` }"
                            ></div>
                        </div>
                        <p class="mt-2 text-xs uppercase tracking-[0.35em] text-white/70">
                            {{ progressPercent }}% funded · {{ campaign.donor_count }} supporters
                        </p>
                    </div>
                </div>
            </section>

            <section class="bg-white py-12 px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-5xl space-y-6 text-slate-700">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">About this drive</h2>
                            <p class="mt-2 whitespace-pre-line text-sm text-slate-600">
                                {{ campaign.description }}
                            </p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5 text-sm text-slate-600">
                            <p class="font-semibold text-slate-900">Fast facts</p>
                            <ul class="mt-3 space-y-2">
                                <li>
                                    <span class="font-medium text-slate-800">Supporters:</span>
                                    {{ campaign.donor_count }}
                                </li>
                                <li>
                                    <span class="font-medium text-slate-800">Raised:</span>
                                    {{ campaign.raised_amount.toLocaleString(undefined, { maximumFractionDigits: 0 }) }}
                                    {{ campaign.goal_currency ?? 'ETB' }}
                                </li>
                                <li v-if="campaign.featured_video_url" class="mt-2">
                                    <div class="aspect-video overflow-hidden rounded-xl bg-black/5">
                                        <iframe
                                            :src="campaign.featured_video_url"
                                            title="Campaign video"
                                            class="h-full w-full"
                                            allowfullscreen
                                        ></iframe>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div v-if="urgentElders.length" class="mt-10">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-slate-900">
                                Elders waiting in this campaign
                            </h3>
                            <Link
                                :href="route('home')"
                                class="text-sm font-semibold text-[var(--accent)]"
                            >
                                Browse all →
                            </Link>
                        </div>
                        <div class="mt-4 grid gap-4 md:grid-cols-3">
                            <article
                                v-for="elder in urgentElders"
                                :key="elder.id"
                                class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm"
                            >
                                <img
                                    :src="elder.profile_photo_url"
                                    :alt="elder.name"
                                    class="h-40 w-full rounded-xl object-cover"
                                />
                                <div class="mt-3">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ elder.name }}
                                    </p>
                                    <p class="text-xs uppercase tracking-wider text-slate-400">
                                        {{ elder.priority_level }} priority
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ elder.branch ?? 'Any branch' }}
                                    </p>
                                </div>
                                <GlassButton
                                    as="span"
                                    size="sm"
                                    variant="secondary"
                                    class="mt-3 w-full"
                                >
                                    <Link
                                        :href="route('elders.public.show', elder.id, false)"
                                        class="flex items-center justify-center gap-2 text-sm"
                                    >
                                        Meet {{ elder.name.split(' ')[0] }}
                                    </Link>
                                </GlassButton>
                            </article>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </GuestLayout>
</template>
