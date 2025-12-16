<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import FileUploadField from '@/components/FileUploadField.vue'; // For profile picture upload
import { Head, Link, useForm } from '@inertiajs/vue3';

interface BranchOption {
    id: number;
    name: string;
}

const props = defineProps<{
    branches: BranchOption[];
}>();

const form = useForm({
    branch_id: '',
    first_name: '',
    last_name: '',
    date_of_birth: '',
    gender: 'male', // Default to male
    address: '',
    city: '',
    country: '',
    phone: '',
    bio: '',
    profile_picture: null as File | null,
    priority_level: 'medium', // Default to medium
    health_status: '',
    special_needs: '',
    monthly_expenses: null as number | null,
    video: null as File | null, // Changed from video_url to video (File | null)
});

const submit = () => {
    form.post(route('elders.store'), { forceFormData: true });
};

const updateProfilePicture = (file: File | null) => {
    form.profile_picture = file;
};

const updateVideo = (file: File | null) => {
    form.video = file;
};
</script>

<template>
    <Head title="New Elder" />

    <AppLayout :breadcrumbs="[{ title: 'Elders', href: route('elders.index') }, { title: 'Create', href: route('elders.create') }]">
    <div class="flex flex-col gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                Add new elder
            </h1>
            <p class="text-sm text-slate-600 dark:text-slate-300">
                Create a new elder profile and assign them to a branch.
            </p>
        </div>

        <GlassCard>
            <form class="space-y-5" @submit.prevent="submit">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Branch
                    </label>
                    <select
                        v-model="form.branch_id"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    >
                        <option value="" disabled>Select a branch</option>
                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                            {{ branch.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.branch_id" class="mt-2" />
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                            First Name
                        </label>
                        <input
                            v-model="form.first_name"
                            type="text"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.first_name" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                            Last Name
                        </label>
                        <input
                            v-model="form.last_name"
                            type="text"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.last_name" class="mt-2" />
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                            Date of Birth
                        </label>
                        <input
                            v-model="form.date_of_birth"
                            type="date"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.date_of_birth" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                            Gender
                        </label>
                        <select
                            v-model="form.gender"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <InputError :message="form.errors.gender" class="mt-2" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Address
                    </label>
                    <input
                        v-model="form.address"
                        type="text"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    />
                    <InputError :message="form.errors.address" class="mt-2" />
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                            City
                        </label>
                        <input
                            v-model="form.city"
                            type="text"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.city" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                            Country
                        </label>
                        <input
                            v-model="form.country"
                            type="text"
                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.country" class="mt-2" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Phone
                    </label>
                    <input
                        v-model="form.phone"
                        type="text"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    />
                    <InputError :message="form.errors.phone" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Bio
                    </label>
                    <textarea
                        v-model="form.bio"
                        rows="3"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    ></textarea>
                    <InputError :message="form.errors.bio" class="mt-2" />
                </div>
                <div>
                    <FileUploadField
                        label="Profile Picture"
                        hint="Upload a profile picture for the elder."
                        accept="image/*"
                        variant="image"
                        :model-value="form.profile_picture"
                        @update:modelValue="updateProfilePicture"
                    />
                    <InputError :message="form.errors.profile_picture" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Priority Level
                    </label>
                    <select
                        v-model="form.priority_level"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    >
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <InputError :message="form.errors.priority_level" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Health Status
                    </label>
                    <textarea
                        v-model="form.health_status"
                        rows="3"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    ></textarea>
                    <InputError :message="form.errors.health_status" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Special Needs
                    </label>
                    <textarea
                        v-model="form.special_needs"
                        rows="3"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    ></textarea>
                    <InputError :message="form.errors.special_needs" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Monthly Expenses (ETB)
                    </label>
                    <input
                        v-model="form.monthly_expenses"
                        type="number"
                        step="0.01"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                    />
                    <InputError :message="form.errors.monthly_expenses" class="mt-2" />
                </div>
                <div>
                    <FileUploadField
                        label="Video"
                        hint="Upload a video for the elder."
                        accept="video/*"
                        variant="file"
                        :model-value="form.video"
                        @update:modelValue="updateVideo"
                    />
                    <InputError :message="form.errors.video" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <GlassButton
                        size="sm"
                        variant="secondary"
                    >
                        <Link :href="route('elders.index')" class="flex items-center gap-2">
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
