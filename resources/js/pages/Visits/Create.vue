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

interface BranchOption {
    id: number;
    name: string;
}

const props = defineProps<{
    users: UserOption[];
    elders: ElderOption[];
    branches: BranchOption[];
}>();

const translatorOptions = [
    'Amharic',
    'Afaan Oromo',
    'Tigrinya',
    'English',
];

const transportOptions = [
    'Organization van',
    'Private car',
    'Ride-share',
];

const form = useForm({
    user_id: '',
    elder_id: '',
    branch_id: '',
    visit_date: '',
    purpose: '',
    notes: '',
    status: 'pending', // Default to pending
    needs_translator: false,
    translator_language: '',
    needs_transport: false,
    transport_preference: '',
    logistics_notes: '',
});

const submit = () => {
    form.post(route('visits.store'));
};
</script>

<template>
    <Head title="New Visit" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Visits', href: route('visits.index') },
            { title: 'Create', href: route('visits.create') },
        ]"
    >
        <div class="flex flex-col gap-4">
            <div>
                <h1
                    class="text-2xl font-semibold text-slate-900 dark:text-slate-100"
                >
                    Schedule new visit
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    Schedule a new visit for a user to an elder.
                </p>
            </div>

            <GlassCard>
                <form class="space-y-5" @submit.prevent="submit">
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                        >
                            Visitor
                        </label>
                        <select
                            v-model="form.user_id"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option value="" disabled>Select a visitor</option>
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
                            Branch
                        </label>
                        <select
                            v-model="form.branch_id"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option value="" disabled>Select a branch</option>
                            <option
                                v-for="branch in branches"
                                :key="branch.id"
                                :value="branch.id"
                            >
                                {{ branch.name }}
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.branch_id"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                        >
                            Visit Date and Time
                        </label>
                        <input
                            v-model="form.visit_date"
                            type="datetime-local"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError
                            :message="form.errors.visit_date"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                        >
                            Purpose of Visit
                        </label>
                        <input
                            v-model="form.purpose"
                            type="text"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError
                            :message="form.errors.purpose"
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
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="completed">Completed</option>
                        </select>
                        <InputError
                            :message="form.errors.status"
                            class="mt-2"
                        />
                    </div>

                    <div class="space-y-4 rounded-2xl border border-slate-200/70 bg-slate-50/80 px-4 py-4 shadow-sm dark:border-slate-700/60 dark:bg-slate-900/40">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-100">
                                Logistics
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-300">
                                Capture any translator or transport needs plus logistics notes.
                            </p>
                        </div>
                        <div class="grid gap-3 md:grid-cols-2">
                            <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 dark:border-slate-600 dark:bg-slate-900/40 dark:text-slate-200">
                                <input
                                    type="checkbox"
                                    v-model="form.needs_translator"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-500"
                                />
                                Needs translator
                            </label>
                            <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 dark:border-slate-600 dark:bg-slate-900/40 dark:text-slate-200">
                                <input
                                    type="checkbox"
                                    v-model="form.needs_transport"
                                    class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-500"
                                />
                                Needs transport
                            </label>
                        </div>
                        <div v-if="form.needs_translator" class="space-y-2">
                            <label
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                            >
                                Preferred language
                            </label>
                            <select
                                v-model="form.translator_language"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            >
                                <option value="" disabled>
                                    Select language
                                </option>
                                <option
                                    v-for="language in translatorOptions"
                                    :key="language"
                                    :value="language"
                                >
                                    {{ language }}
                                </option>
                            </select>
                            <InputError
                                :message="form.errors.translator_language"
                                class="mt-1"
                            />
                        </div>
                        <div v-if="form.needs_transport" class="space-y-2">
                            <label
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                            >
                                Transport preference
                            </label>
                            <select
                                v-model="form.transport_preference"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            >
                                <option value="" disabled>
                                    Select transport option
                                </option>
                                <option
                                    v-for="transport in transportOptions"
                                    :key="transport"
                                    :value="transport"
                                >
                                    {{ transport }}
                                </option>
                            </select>
                            <InputError
                                :message="form.errors.transport_preference"
                                class="mt-1"
                            />
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                            >
                                Logistics notes
                            </label>
                            <textarea
                                v-model="form.logistics_notes"
                                rows="3"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            ></textarea>
                            <InputError
                                :message="form.errors.logistics_notes"
                                class="mt-1"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <GlassButton size="sm" variant="secondary">
                            <Link
                                :href="route('visits.index')"
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
                            Schedule Visit
                        </GlassButton>
                    </div>
                </form>
            </GlassCard>
        </div>
    </AppLayout>
</template>
