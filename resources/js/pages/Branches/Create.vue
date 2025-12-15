<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    location: '',
    contact_person: '',
    contact_email: '',
    contact_phone: '',
    notes: '',
});

const submit = () => {
    form.post(route('branches.store'));
};
</script>

<template>
    <Head title="New Branch" />

    <AppLayout :breadcrumbs="[{ title: 'Branches', href: '/branches' }, { title: 'Create', href: route('branches.create') }]">
    <div class="flex flex-col gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                Add new branch
            </h1>
            <p class="text-sm text-slate-600 dark:text-slate-300">
                Create a new organizational branch.
            </p>
        </div>

        <GlassCard>
            <form class="space-y-5" @submit.prevent="submit">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Branch Name
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Location
                    </label>
                    <input
                        v-model="form.location"
                        type="text"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    />
                    <InputError :message="form.errors.location" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Contact Person
                    </label>
                    <input
                        v-model="form.contact_person"
                        type="text"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    />
                    <InputError :message="form.errors.contact_person" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Contact Email
                    </label>
                    <input
                        v-model="form.contact_email"
                        type="email"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    />
                    <InputError :message="form.errors.contact_email" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Contact Phone
                    </label>
                    <input
                        v-model="form.contact_phone"
                        type="text"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    />
                    <InputError :message="form.errors.contact_phone" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
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
                    <GlassButton
                        size="sm"
                        variant="secondary"
                    >
                        <Link :href="route('branches.index')" class="flex items-center gap-2">
                            Cancel
                        </Link>
                    </GlassButton>
                    <GlassButton size="sm" type="submit" :disabled="form.processing" variant="primary">
                        Save
                    </GlassButton>
                </div>
            </form>
        </GlassCard>
    </div>
    </AppLayout>
</template>
