<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';

const form = useForm({
    amount: 70,
    name: '',
    email: '',
    phone: '',
    elder_id: null as number | null,
});

const submit = () => {
    form.post(route('donations.guest.store'));
};
</script>

<template>
    <Head title="Donate a Meal" />

    <AppLayout>
        <div class="flex flex-col items-center justify-center py-12">
            <div class="w-full max-w-2xl">
                <GlassCard>
                    <h1 class="text-3xl font-bold text-center">Donate a Meal</h1>
                    <p class="mt-2 text-center text-slate-600 dark:text-slate-400">
                        Your contribution can make a huge difference in an elder's life.
                    </p>

                    <form @submit.prevent="submit" class="mt-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Donation Amount (ETB)
                            </label>
                            <div class="mt-2 flex items-center space-x-4">
                                <input type="range" min="70" max="250" step="10" v-model="form.amount" class="w-full">
                                <span class="text-2xl font-bold">{{ form.amount }} ETB</span>
                            </div>
                            <div class="mt-2 flex justify-between text-xs text-slate-500 dark:text-slate-400">
                                <span>70 (Breakfast)</span>
                                <span>150 (Lunch)</span>
                                <span>250 (Dinner)</span>
                            </div>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Your Name (Optional)
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Your Email (Optional)
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Your Phone (Optional)
                            </label>
                            <input
                                id="phone"
                                v-model="form.phone"
                                type="text"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError :message="form.errors.phone" class="mt-2" />
                        </div>

                        <div class="flex justify-end">
                            <GlassButton type="submit" :disabled="form.processing">
                                <span v-if="form.processing">Processing...</span>
                                <span v-else>Proceed to Payment</span>
                            </GlassButton>
                        </div>
                    </form>
                </GlassCard>
            </div>
        </div>
    </AppLayout>
</template>
