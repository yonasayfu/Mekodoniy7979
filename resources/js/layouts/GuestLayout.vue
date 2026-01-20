<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();
const user = computed(() => page.props.auth?.user);
</script>

<template>
    <div
        class="min-h-screen bg-background font-sans text-foreground antialiased"
    >
        <!-- Navbar -->
        <nav
            class="fixed top-0 right-0 left-0 z-50 bg-white/80 shadow-sm backdrop-blur-md dark:bg-gray-900/80"
        >
            <div
                class="container mx-auto flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8"
            >
                <Link
                    :href="route('home', undefined, false)"
                    class="flex items-center space-x-2"
                >
                    <img
                        src="/images/mekodonia-logo.svg"
                        alt="Mekodonia Logo"
                        class="h-8 w-auto"
                    />
                    <span
                        class="text-xl font-bold text-gray-900 dark:text-white"
                        >Mekodonia</span
                    >
                </Link>
                <div class="flex items-center space-x-4">
                    <Link
                        v-if="user"
                        :href="route('dashboard', undefined, false)"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-gray-900"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login', undefined, false)"
                            class="text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="route('register', undefined, false)"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-gray-900"
                        >
                            Register
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <main class="pt-16">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 py-12 text-white dark:bg-gray-950">
            <div class="container mx-auto px-4 text-center">
                <p>
                    &copy; {{ new Date().getFullYear() }} Mekodonia Home
                    Connect. All rights reserved.
                </p>
                <div class="mt-4 space-x-4">
                    <a href="#" class="hover:text-gray-300">Privacy Policy</a>
                    <a href="#" class="hover:text-gray-300">Terms of Service</a>
                    <a href="#" class="hover:text-gray-300">Contact Us</a>
                </div>
            </div>
        </footer>
    </div>
</template>
