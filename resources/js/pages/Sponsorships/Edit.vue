<script setup lang="ts">
import ActivityTimeline from '@/components/ActivityTimeline.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

type ActivityEntry = {
    id: number | string;
    action: string;
    description?: string | null;
    causer?: {
        id: number | string | null;
        name?: string | null;
    } | null;
    changes?: {
        before?: Record<string, unknown> | null;
        after?: Record<string, unknown> | null;
    } | null;
    created_at?: string | null;
    created_at_for_humans?: string | null;
};

interface UserOption {
    id: number;
    name: string;
}

interface ElderOption {
    id: number;
    first_name: string;
    last_name: string;
}

const props = defineProps<{
    sponsorship: {
        id: number;
        user_id: number;
        elder_id: number;
        amount: number | null;
        frequency: string | null;
        start_date: string | null;
        end_date: string | null;
        status: 'pending' | 'active' | 'completed' | 'cancelled';
        notes: string | null;
        user: UserOption;
        elder: ElderOption;
    };
    activity: ActivityEntry[];
    users: UserOption[];
    elders: ElderOption[];
}>();

const form = useForm({
    user_id: props.sponsorship.user_id,
    elder_id: props.sponsorship.elder_id,
    amount: props.sponsorship.amount,
    frequency: props.sponsorship.frequency ?? 'once',
    start_date: props.sponsorship.start_date ?? '',
    end_date: props.sponsorship.end_date ?? '',
    status: props.sponsorship.status,
    notes: props.sponsorship.notes ?? '',
});

const submit = () => {
    form.put(route('sponsorships.update', props.sponsorship.id));
};
</script>

<template>
    <Head :title="`Edit Sponsorship ${sponsorship.id}`" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Sponsorships', href: route('sponsorships.index') },
            {
                title: `Sponsorship ${sponsorship.id}`,
                href: route('sponsorships.edit', sponsorship.id),
            },
        ]"
    >
        <div class="flex flex-col gap-4">
            <div>
                <h1
                    class="text-2xl font-semibold text-slate-900 dark:text-slate-100"
                >
                    Edit sponsorship
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    Update details for Sponsorship {{ sponsorship.id }} by
                    {{ sponsorship.user.name }} for
                    {{ sponsorship.elder.first_name }}
                    {{ sponsorship.elder.last_name }}.
                </p>
            </div>

            <GlassCard>
                <form class="space-y-5" @submit.prevent="submit">
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                        >
                            Donor
                        </label>
                        <select
                            v-model="form.user_id"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option value="" disabled>Select a donor</option>
                            <option
                                v-for="user in users"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }}
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.user_id"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                        >
                            Elder
                        </label>
                        <select
                            v-model="form.elder_id"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option value="" disabled>Select an elder</option>
                            <option
                                v-for="elder in elders"
                                :key="elder.id"
                                :value="elder.id"
                            >
                                {{ elder.first_name }} {{ elder.last_name }}
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.elder_id"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                        >
                            Amount (ETB)
                        </label>
                        <input
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError
                            :message="form.errors.amount"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                        >
                            Frequency
                        </label>
                        <select
                            v-model="form.frequency"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option value="once">Once</option>
                            <option value="monthly">Monthly</option>
                        </select>
                        <InputError
                            :message="form.errors.frequency"
                            class="mt-2"
                        />
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >
                                Start Date
                            </label>
                            <input
                                v-model="form.start_date"
                                type="date"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="form.errors.start_date"
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >
                                End Date
                            </label>
                            <input
                                v-model="form.end_date"
                                type="date"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="form.errors.end_date"
                                class="mt-2"
                            />
                        </div>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                        >
                            Status
                        </label>
                        <select
                            v-model="form.status"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <InputError
                            :message="form.errors.status"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                        >
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        ></textarea>
                        <InputError :message="form.errors.notes" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <GlassButton size="sm" variant="secondary">
                            <Link
                                :href="route('sponsorships.index')"
                                class="flex items-center gap-2"
                                >Cancel</Link
                            >
                        </GlassButton>
                        <GlassButton
                            size="sm"
                            type="submit"
                            :disabled="form.processing"
                            variant="primary"
                        >
                            Save changes
                        </GlassButton>
                    </div>
                </form>
            </GlassCard>

            <GlassCard
                variant="lite"
                content-class="space-y-4"
                :disable-shine="true"
            >
                <div>
                    <h2
                        class="text-sm font-semibold text-slate-900 dark:text-slate-100"
                    >
                        Recent activity
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Track changes applied to this sponsorship.
                    </p>
                </div>
                <ActivityTimeline :entries="activity" />
            </GlassCard>
        </div>
    </AppLayout>
</template>
