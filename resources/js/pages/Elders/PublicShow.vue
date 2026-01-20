<script setup lang="ts">
import GlassCard from '@/components/GlassCard.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { User } from 'lucide-vue-next';

const props = defineProps<{
    elder: {
        id: number;
        first_name: string;
        last_name: string;
        profile_picture_path: string | null;
        priority_level: string;
        bio: string | null;
        special_needs: string | null;
        branch: {
            name: string;
        };
    };
}>();
</script>

<template>
    <Head :title="`${props.elder.first_name} ${props.elder.last_name}`" />

    <GuestLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <GlassCard>
                    <div class="md:flex md:space-x-8">
                        <div class="flex-shrink-0">
                            <img
                                v-if="props.elder.profile_picture_path"
                                :src="`/storage/${props.elder.profile_picture_path}`"
                                class="h-64 w-64 rounded-lg object-cover shadow-md"
                                :alt="props.elder.first_name"
                            />
                            <div
                                v-else
                                class="flex h-64 w-64 items-center justify-center rounded-lg bg-gray-200 text-gray-500 shadow-md dark:bg-gray-700 dark:text-gray-400"
                            >
                                <User class="size-32" />
                            </div>
                        </div>
                        <div class="mt-6 md:mt-0">
                            <div class="flex items-center justify-between">
                                <h1
                                    class="text-3xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ props.elder.first_name }}
                                    {{ props.elder.last_name }}
                                </h1>
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-0.5 text-sm font-medium"
                                    :class="{
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200':
                                            props.elder.priority_level ===
                                            'high',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200':
                                            props.elder.priority_level ===
                                            'medium',
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200':
                                            props.elder.priority_level ===
                                            'low',
                                    }"
                                >
                                    {{
                                        props.elder.priority_level.toUpperCase()
                                    }}
                                    PRIORITY
                                </span>
                            </div>
                            <p
                                class="mt-2 text-sm text-gray-500 dark:text-gray-400"
                            >
                                Location: {{ props.elder.branch.name }}
                            </p>

                            <div class="mt-6">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-white"
                                >
                                    About
                                </h3>
                                <p
                                    class="mt-2 text-gray-600 dark:text-gray-300"
                                >
                                    {{
                                        props.elder.bio ||
                                        'No biography available.'
                                    }}
                                </p>
                            </div>

                            <div class="mt-6" v-if="props.elder.special_needs">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-white"
                                >
                                    Special Needs
                                </h3>
                                <p
                                    class="mt-2 text-gray-600 dark:text-gray-300"
                                >
                                    {{ props.elder.special_needs }}
                                </p>
                            </div>

                            <div class="mt-8 flex space-x-4">
                                <Link
                                    :href="route('register')"
                                    class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
                                >
                                    Sponsor This Elder
                                </Link>
                                <Link
                                    :href="route('guest.donation')"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700"
                                >
                                    One-Time Donation
                                </Link>
                            </div>
                        </div>
                    </div>
                </GlassCard>
            </div>
        </div>
    </GuestLayout>
</template>
