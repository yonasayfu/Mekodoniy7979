<script setup lang="ts">
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

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
    users: UserOption[];
    elders: ElderOption[];
}>();

const form = useForm({
    user_id: '',
    elder_id: '',
    amount: null as number | null,
    frequency: 'once', // Default to once
    start_date: '',
    end_date: '',
    status: 'pending', // Default to pending
    notes: '',
});

const submit = () => {
    form.post(route('sponsorships.store'));
};
</script>

<template>
    <Head title="New Sponsorship" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Sponsorships', href: route('sponsorships.index') },
            { title: 'Create', href: route('sponsorships.create') },
        ]"
    >
        <div class="flex flex-col gap-4">
            <div>
                <h1
                    class="text-2xl font-semibold text-slate-900 dark:text-slate-100"
                >
                    Create new sponsorship
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    Create a new sponsorship for a donor and an elder.
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
                            >
                                Cancel
                            </Link>
                        </GlassButton>
                        <GlassButton
                            size="sm"
                            type="submit"
                            :disabled="form.processing"
                            variant="primary"
                        >
                            Save
                        </GlassButton>
                    </div>
                </form>
            </GlassCard>
        </div>
    </AppLayout>
</template>
