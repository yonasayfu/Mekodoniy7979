<script setup lang="ts">
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    slug: '',
    description: '',
    short_description: '',
    starts_at: '',
    ends_at: '',
    goal_amount: null as number | null,
    goal_currency: 'ETB',
    status: 'draft',
    cta_label: 'Support this campaign',
    cta_url: '',
    accent_color: '#2563eb',
    featured_video_url: '',
    hero_image: null as File | null,
});

const submit = () => {
    form.post(route('campaigns.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="New Campaign" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Campaigns', href: route('campaigns.index') },
            { title: 'Create', href: route('campaigns.create') },
        ]"
    >
        <div class="flex flex-col gap-4">
            <div>
                <h1
                    class="text-2xl font-semibold text-slate-900 dark:text-slate-100"
                >
                    Create campaign
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    Create a new fundraising campaign.
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
                            >Slug (optional)</label
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
                            Appears on the public microsite hero.
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
                                >CTA URL (optional)</label
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
                                >Featured video URL (optional)</label
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
                        <input
                            type="file"
                            class="mt-1 w-full rounded-lg border border-dashed border-slate-300 bg-white px-3 py-2 text-sm text-slate-600 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            accept="image/*"
                            @change="
                                (event) =>
                                    (form.hero_image = (event.target as HTMLInputElement).files?.[0] ??
                                        null)
                            "
                        />
                        <InputError
                            :message="form.errors.hero_image"
                            class="mt-2"
                        />
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
                            >Save</GlassButton
                        >
                    </div>
                </form>
            </GlassCard>
        </div>
    </AppLayout>
</template>
