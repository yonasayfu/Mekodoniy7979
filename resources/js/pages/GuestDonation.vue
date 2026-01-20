<script setup lang="ts">
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const getQueryParam = (param: string) => {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
};

const mode = getQueryParam('mode') ?? 'one_time';

const form = useForm({
    amount: 70,
    name: '',
    email: '',
    phone: '',
    elder_id: getQueryParam('elder_id')
        ? parseInt(getQueryParam('elder_id')!)
        : (null as number | null),
});

const selectedRelationship = getQueryParam('relationship');

const submit = () => {
    form.post(route('donations.guest.store'));
};
</script>

<template>
    <Head title="Donate a Meal" />

    <GuestLayout>
        <div class="flex flex-col items-center justify-center py-12">
            <div class="w-full max-w-2xl">
                <GlassCard>
                    <h1 class="text-center text-3xl font-bold">
                        Donate a Meal
                    </h1>
                    <p
                        class="mt-2 text-center text-slate-600 dark:text-slate-400"
                    >
                        Your contribution can make a huge difference in an
                        elder's life.
                    </p>

                    <form @submit.prevent="submit" class="mt-8 space-y-6">
                        <div
                            v-if="selectedRelationship || mode === 'sponsorship'"
                            class="rounded-lg border border-indigo-200 bg-indigo-50/60 px-4 py-3 text-sm font-medium text-indigo-700 dark:border-indigo-500/40 dark:bg-indigo-500/10 dark:text-indigo-200"
                        >
                            You’re starting a
                            <span class="font-semibold">
                                {{
                                    selectedRelationship
                                        ? selectedRelationship
                                              .charAt(0)
                                              .toUpperCase() +
                                          selectedRelationship.slice(1)
                                        : 'monthly'
                                }}
                            </span>
                            sponsorship. Complete the form below to continue.
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >
                                Donation Amount (ETB)
                            </label>
                            <div class="mt-2 flex items-center space-x-4">
                                <input
                                    type="range"
                                    min="70"
                                    max="250"
                                    step="10"
                                    v-model="form.amount"
                                    class="w-full"
                                />
                                <span class="text-2xl font-bold"
                                    >{{ form.amount }} ETB</span
                                >
                            </div>
                            <div
                                class="mt-2 flex justify-between text-xs text-slate-500 dark:text-slate-400"
                            >
                                <span>70 (Breakfast)</span>
                                <span>150 (Lunch)</span>
                                <span>250 (Dinner)</span>
                            </div>
                        </div>

                        <div>
                            <label
                                for="name"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >
                                Your Name (Optional)
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="form.errors.name"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <label
                                for="email"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >
                                Your Email (Optional)
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="form.errors.email"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <label
                                for="phone"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >
                                Your Phone (Optional)
                            </label>
                            <input
                                id="phone"
                                v-model="form.phone"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="form.errors.phone"
                                class="mt-2"
                            />
                        </div>

                        <div class="flex justify-end">
                            <GlassButton
                                type="submit"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing"
                                    >Processing...</span
                                >
                                <span v-else>Proceed to Payment</span>
                            </GlassButton>
                        </div>
                    </form>
                </GlassCard>
            </div>
        </div>
    </GuestLayout>
</template>
