<script setup lang="ts">
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    campaign: {
        id: number;
        title: string;
        slug: string;
        description: string | null;
        short_description: string | null;
        starts_at: string | null;
        ends_at: string | null;
        goal_amount: number | null;
        goal_currency: string | null;
        status: 'draft' | 'active' | 'ended';
        cta_label: string | null;
        cta_url: string | null;
        accent_color: string | null;
        featured_video_url: string | null;
        hero_image_url: string;
    };
    breadcrumbs?: { title: string; href: string }[];
}>();

const form = useForm({
    title: props.campaign.title,
    slug: props.campaign.slug ?? '',
    description: props.campaign.description ?? '',
    short_description: props.campaign.short_description ?? '',
    starts_at: props.campaign.starts_at ?? '',
    ends_at: props.campaign.ends_at ?? '',
    goal_amount: props.campaign.goal_amount,
    goal_currency: props.campaign.goal_currency ?? 'ETB',
    status: props.campaign.status,
    cta_label: props.campaign.cta_label ?? 'Support this campaign',
    cta_url: props.campaign.cta_url ?? '',
    accent_color: props.campaign.accent_color ?? '#2563eb',
    featured_video_url: props.campaign.featured_video_url ?? '',
    hero_image: null as File | null,
});

const submit = () => {
    form.put(route('campaigns.update', props.campaign.id), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="`Edit ${campaign.title}`" />

    <AppLayout
        :breadcrumbs="
            props.breadcrumbs ?? [
                { title: 'Campaigns', href: route('campaigns.index') },
                {
                    title: campaign.title,
                    href: route('campaigns.edit', campaign.id),
                },
            ]
        "
    >
        <div class="flex flex-col gap-4">
            <div>
                <h1
                    class="text-2xl font-semibold text-slate-900 dark:text-slate-100"
                >
                    Edit campaign
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    Update campaign details for {{ campaign.title }}.
                </p>
            </div>

            <GlassCard>
                <form class="space-y-5" @submit.prevent="submit">
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Title</label
                        >
                        <input
                            v-model="form.title"
                            type="text"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.title" class="mt-2" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Slug</label
                        >
                        <input
                            v-model="form.slug"
                            type="text"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.slug" class="mt-2" />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Description</label
                        >
                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        ></textarea>
                        <InputError
                            :message="form.errors.description"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Short spotlight blurb</label
                        >
                        <textarea
                            v-model="form.short_description"
                            rows="3"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        ></textarea>
                        <p class="mt-1 text-xs text-slate-500">
                            Appears on the microsite hero area.
                        </p>
                        <InputError
                            :message="form.errors.short_description"
                            class="mt-2"
                        />
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >Starts At</label
                            >
                            <input
                                v-model="form.starts_at"
                                type="datetime-local"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="form.errors.starts_at"
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >Ends At</label
                            >
                            <input
                                v-model="form.ends_at"
                                type="datetime-local"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="form.errors.ends_at"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >Goal Amount</label
                            >
                            <input
                                v-model="form.goal_amount"
                                type="number"
                                step="0.01"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="form.errors.goal_amount"
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >Goal Currency</label
                            >
                            <input
                                v-model="form.goal_currency"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="form.errors.goal_currency"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Status</label
                        >
                        <select
                            v-model="form.status"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="ended">Ended</option>
                        </select>
                        <InputError
                            :message="form.errors.status"
                            class="mt-2"
                        />
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >CTA Label</label
                            >
                            <input
                                v-model="form.cta_label"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="form.errors.cta_label"
                                class="mt-2"
                            />
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >CTA URL</label
                            >
                            <input
                                v-model="form.cta_url"
                                type="url"
                                placeholder="https://"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="form.errors.cta_url"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >Accent Color</label
                            >
                            <input
                                v-model="form.accent_color"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="form.errors.accent_color"
                                class="mt-2"
                            />
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >Featured video URL</label
                            >
                            <input
                                v-model="form.featured_video_url"
                                type="url"
                                placeholder="https://www.youtube.com/embed/..."
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="form.errors.featured_video_url"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >Hero image</label
                        >
                        <div class="mt-2 flex flex-col gap-4 md:flex-row md:items-center">
                            <div class="flex-1">
                                <input
                                    type="file"
                                    accept="image/*"
                                    class="w-full rounded-lg border border-dashed border-slate-300 bg-white px-3 py-2 text-sm text-slate-600 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                    @change="
                                        (event) =>
                                            (form.hero_image =
                                                (event.target as HTMLInputElement).files?.[0] ??
                                                null)
                                    "
                                />
                                <InputError
                                    :message="form.errors.hero_image"
                                    class="mt-2"
                                />
                            </div>
                            <img
                                v-if="campaign.hero_image_url"
                                :src="campaign.hero_image_url"
                                alt="Current hero"
                                class="h-24 w-40 rounded-lg object-cover"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <GlassButton size="sm" variant="secondary">
                            <Link
                                :href="route('campaigns.index')"
                                class="flex items-center gap-2"
                                >Cancel</Link
                            >
                        </GlassButton>
                        <GlassButton
                            size="sm"
                            type="submit"
                            :disabled="form.processing"
                            variant="primary"
                            >Save changes</GlassButton
                        >
                    </div>
                </form>
            </GlassCard>
        </div>
    </AppLayout>
</template>
