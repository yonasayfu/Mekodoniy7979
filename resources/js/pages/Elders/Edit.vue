<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ActivityTimeline from '@/components/ActivityTimeline.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import FileUploadField from '@/components/FileUploadField.vue'; // For profile picture upload
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

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

interface BranchOption {
    id: number;
    name: string;
}

const props = defineProps<{
    elder: {
        id: number;
        branch_id: number;
        first_name: string;
        last_name: string;
        date_of_birth: string | null;
        gender: string | null;
        address: string | null;
        city: string | null;
        country: string | null;
        phone: string | null;
        bio: string | null;
        profile_picture_path: string | null;
        priority_level: 'low' | 'medium' | 'high';
        health_status: string | null;
        special_needs: string | null;
        monthly_expenses: number | null;
        video_url: string | null; // Keep video_url for existing value
    };
    activity: ActivityEntry[];
    branches: BranchOption[];
}>();

const form = useForm({
    branch_id: props.elder.branch_id,
    first_name: props.elder.first_name,
    last_name: props.elder.last_name,
    date_of_birth: props.elder.date_of_birth ?? '',
    gender: props.elder.gender ?? 'male',
    address: props.elder.address ?? '',
    city: props.elder.city ?? '',
    country: props.elder.country ?? '',
    phone: props.elder.phone ?? '',
    bio: props.elder.bio ?? '',
    profile_picture: null as File | null,
    remove_profile_picture: false,
    priority_level: props.elder.priority_level,
    health_status: props.elder.health_status ?? '',
    special_needs: props.elder.special_needs ?? '',
    monthly_expenses: props.elder.monthly_expenses,
    video: null as File | null, // Changed from video_url to video (File | null)
    remove_video: false, // Add remove_video for explicit video removal
});

const existingProfilePictureUrl = ref<string | null>(props.elder.profile_picture_path ? `/storage/${props.elder.profile_picture_path}` : null);
const existingVideoUrl = ref<string | null>(props.elder.video_url ? `/storage/${props.elder.video_url}` : null); // Add existingVideoUrl

const updateProfilePicture = (file: File | null) => {
    form.profile_picture = file;

    if (file) {
        form.remove_profile_picture = false;
    }
};

const clearExistingProfilePicture = () => {
    existingProfilePictureUrl.value = null;
    form.profile_picture = null;
    form.remove_profile_picture = true;
};

const updateVideo = (file: File | null) => { // Add updateVideo method
    form.video = file;

    if (file) {
        form.remove_video = false;
    }
};

const clearExistingVideo = () => { // Add clearExistingVideo method
    existingVideoUrl.value = null;
    form.video = null;
    form.remove_video = true;
};
</script>

<template>
    <Head :title="`Edit ${elder.first_name} ${elder.last_name}`" />

    <AppLayout :breadcrumbs="[{ title: 'Elders', href: route('elders.index') }, { title: `${elder.first_name} ${elder.last_name}`, href: route('elders.edit', elder.id) }]">
    <div class="flex flex-col gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                Edit elder
            </h1>
            <p class="text-sm text-slate-600 dark:text-slate-300">
                Update details for {{ elder.first_name }} {{ elder.last_name }}.
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
                        :existing-url="existingProfilePictureUrl"
                        @update:modelValue="updateProfilePicture"
                        @clear-existing="clearExistingProfilePicture"
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
                        :existing-url="existingVideoUrl"
                        @update:modelValue="updateVideo"
                        @clear-existing="clearExistingVideo"
                    />
                    <InputError :message="form.errors.video" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <GlassButton
                        size="sm"
                        variant="secondary"
                    >
                        <Link :href="route('elders.index')" class="flex items-center gap-2">Cancel</Link>
                    </GlassButton>
                    <GlassButton size="sm" type="submit" :disabled="form.processing" variant="primary">
                        Save changes
                    </GlassButton>
                </div>
            </form>
        </GlassCard>

        <GlassCard variant="lite" content-class="space-y-4" :disable-shine="true">
            <div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                    Recent activity
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Track changes applied to this elder profile.
                </p>
            </div>
            <ActivityTimeline :entries="activity" />
        </GlassCard>
    </div>
    </AppLayout>
</template>
