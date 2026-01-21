<script setup lang="ts">
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit3 } from 'lucide-vue-next';

const props = defineProps<{
    campaign: {
        id: number;
        title: string;
        slug: string;
        description: string | null;
        starts_at: string | null;
        ends_at: string | null;
        goal_amount: number | null;
        goal_currency: string | null;
        status: 'draft' | 'active' | 'ended';
        created_at?: string | null;
        updated_at?: string | null;
    };
    breadcrumbs?: { title: string; href: string }[];
}>();
</script>

<template>
    <Head :title="`Campaign - ${campaign.title}`" />

    <AppLayout
        :breadcrumbs="
            props.breadcrumbs ?? [
                { title: 'Campaigns', href: route('campaigns.index') },
                {
                    title: campaign.title,
                    href: route('campaigns.show', campaign.id),
                },
            ]
        "
    >
        <div class="space-y-6">
            <div class="liquidGlass-wrapper">
                <span class="liquidGlass-inner-shine" aria-hidden="true" />
                <div
                    class="liquidGlass-content flex flex-col gap-4 px-5 py-5 md:flex-row md:items-center md:justify-between"
                >
                    <div>
                        <h1
                            class="text-2xl font-semibold text-slate-900 dark:text-slate-100"
                        >
                            Campaign
                        </h1>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            {{ campaign.title }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <GlassButton as="span" size="sm" variant="secondary">
                            <Link
                                :href="route('campaigns.index')"
                                class="flex items-center gap-2"
                            >
                                <ArrowLeft class="size-4" />
                                <span>Back to list</span>
                            </Link>
                        </GlassButton>

                        <GlassButton as="span" size="sm" variant="primary">
                            <Link
                                :href="route('campaigns.edit', campaign.id)"
                                class="flex items-center gap-2"
                            >
                                <Edit3 class="size-4" />
                                <span>Edit</span>
                            </Link>
                        </GlassButton>
                        <GlassButton as="span" size="sm" variant="ghost">
                            <Link
                                :href="route('campaigns.landing', { campaign: campaign.slug }, false)"
                                class="flex items-center gap-2"
                                target="_blank"
                            >
                                <span>View microsite</span>
                            </Link>
                        </GlassButton>
                    </div>
                </div>
            </div>

            <GlassCard padding="p-0">
                <div
                    class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 p-6 dark:border-slate-800/60 dark:bg-slate-900/60"
                >
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <p
                                class="text-xs font-semibold tracking-wide text-slate-400 uppercase dark:text-slate-500"
                            >
                                Status
                            </p>
                            <p
                                class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100"
                            >
                                {{ campaign.status }}
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold tracking-wide text-slate-400 uppercase dark:text-slate-500"
                            >
                                Slug
                            </p>
                            <p
                                class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100"
                            >
                                {{ campaign.slug }}
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold tracking-wide text-slate-400 uppercase dark:text-slate-500"
                            >
                                Goal
                            </p>
                            <p
                                class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100"
                            >
                                <span v-if="campaign.goal_amount"
                                    >{{ campaign.goal_amount }}
                                    {{ campaign.goal_currency ?? '' }}</span
                                >
                                <span v-else>-</span>
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold tracking-wide text-slate-400 uppercase dark:text-slate-500"
                            >
                                Schedule
                            </p>
                            <p
                                class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100"
                            >
                                Start: {{ campaign.starts_at ?? '-' }}
                                <br />
                                End: {{ campaign.ends_at ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <p
                            class="text-xs font-semibold tracking-wide text-slate-400 uppercase dark:text-slate-500"
                        >
                            Description
                        </p>
                        <p
                            class="mt-1 text-sm whitespace-pre-line text-slate-700 dark:text-slate-200"
                        >
                            {{ campaign.description ?? '-' }}
                        </p>
                    </div>
                    <div class="mt-6">
                        <p
                            class="text-xs font-semibold tracking-wide text-slate-400 uppercase dark:text-slate-500"
                        >
                            Microsite teaser
                        </p>
                        <p
                            class="mt-1 text-sm whitespace-pre-line text-slate-700 dark:text-slate-200"
                        >
                            {{ campaign.short_description ?? '–' }}
                        </p>
                    </div>
                </div>
            </GlassCard>
        </div>
    </AppLayout>
</template>
