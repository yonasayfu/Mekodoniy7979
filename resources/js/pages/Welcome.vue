<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-slate-100 p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-slate-900"
    >
        <header class="mb-6 w-full max-w-7xl text-sm lg:flex lg:justify-end lg:p-8">
            <nav class="flex items-center justify-end gap-4 p-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-500"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="register()"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-500"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <main class="flex w-full flex-col items-center justify-center p-6 text-slate-800 dark:text-slate-200">
            <div class="space-y-8 text-center">
                <h1 class="text-5xl font-extrabold tracking-tight sm:text-6xl lg:text-7xl">
                    Mekodonia Home Connect
                </h1>
                <p class="mt-4 text-xl text-slate-600 dark:text-slate-400">
                    A donor-elder matchmaking and stewardship platform.
                </p>
                <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <Link
                        href="#"
                        class="rounded-md bg-emerald-600 px-6 py-3 text-lg font-semibold text-white shadow-lg hover:bg-emerald-500"
                    >
                        Long-term support
                    </Link>
                    <Link
                        :href="route('guest.donation')"
                        class="rounded-md bg-indigo-600 px-6 py-3 text-lg font-semibold text-white shadow-lg hover:bg-indigo-500"
                    >
                        Donate a Meal
                    </Link>
                </div>
            </div>

            <!-- Placeholder for additional content: Hero Slider, Live Counters, Wall of Love, FAQ, Trust Badges -->
            <div class="mt-12 w-full max-w-4xl space-y-12">
                <section class="rounded-lg bg-white/70 p-8 shadow-xl dark:bg-slate-800/70">
                    <h2 class="text-3xl font-bold">Hero Slider: "Become a Father Today"</h2>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">
                        (Implementation for dynamic hero images and call to action)
                    </p>
                </section>

                <section class="rounded-lg bg-white/70 p-8 shadow-xl dark:bg-slate-800/70">
                    <h2 class="text-3xl font-bold">Live Counters</h2>
                    <div class="mt-4 grid grid-cols-3 gap-4">
                        <div class="rounded-md bg-blue-100 p-4 text-blue-800 dark:bg-blue-900 dark:text-blue-100">
                            Elders Waiting: <span class="text-2xl font-bold">0</span>
                        </div>
                        <div class="rounded-md bg-green-100 p-4 text-green-800 dark:bg-green-900 dark:text-green-100">
                            Matched: <span class="text-2xl font-bold">0</span>
                        </div>
                        <div class="rounded-md bg-purple-100 p-4 text-purple-800 dark:bg-purple-900 dark:text-purple-100">
                            Visited This Month: <span class="text-2xl font-bold">0</span>
                        </div>
                    </div>
                </section>

                <section class="rounded-lg bg-white/70 p-8 shadow-xl dark:bg-slate-800/70">
                    <h2 class="text-3xl font-bold">Wall of Love</h2>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">
                        (Auto-scrolling display of matched donors & elders)
                    </p>
                </section>

                <section class="rounded-lg bg-white/70 p-8 shadow-xl dark:bg-slate-800/70">
                    <h2 class="text-3xl font-bold">FAQ & Trust Badges</h2>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">
                        (Content for frequently asked questions and trust certifications)
                    </p>
                </section>
            </div>
        </main>
    </div>
</template>