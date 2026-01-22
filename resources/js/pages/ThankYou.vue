<template>
    <div class="min-h-screen bg-gradient-to-br from-indigo-900 via-slate-900 to-black text-white">
        <Head title="Thank You" />
        <main class="mx-auto flex min-h-screen max-w-4xl flex-col items-center justify-center gap-6 px-4 py-16 text-center">
            <div class="flex items-center justify-center text-6xl">
                <span class="mr-3 animate-bounce text-yellow-300">✨</span>
                <span class="text-4xl font-bold text-white sm:text-5xl">{{ headingText }}</span>
                <span class="ml-3 animate-bounce text-yellow-300">🙏</span>
            </div>
            <p class="max-w-3xl text-lg text-slate-200">
                {{ descriptionText }}
            </p>
            <div class="space-y-3 rounded-3xl border border-white/30 bg-white/5 p-6 text-left shadow-2xl backdrop-blur">
                <p class="text-xs uppercase tracking-[0.5em] text-slate-400">Donation summary</p>
                <p class="text-xl font-semibold text-white">
                    {{ giverLabel }}<span v-if="donation.amount"> — {{ donation.amount }} ETB</span>
                </p>
                <p class="text-sm text-slate-300">
                    {{ reminderText }}
                </p>
            </div>
            <div class="grid gap-4 text-left text-sm sm:grid-cols-2">
                <div class="rounded-2xl border border-white/20 bg-white/5 p-4">
                    <p class="font-semibold text-white">{{ instructionsTitle }}</p>
                    <ul class="mt-3 list-disc space-y-2 pl-4 text-slate-300">
                        <li v-for="item in nextSteps" :key="item">{{ item }}</li>
                    </ul>
                </div>
                <div class="rounded-2xl border border-white/20 bg-white/5 p-4">
                    <p class="font-semibold text-white">{{ tipsTitle }}</p>
                    <ul class="mt-3 list-disc space-y-2 pl-4 text-slate-300">
                        <li v-for="tip in donorTips" :key="tip">{{ tip }}</li>
                    </ul>
                </div>
            </div>
            <div class="flex flex-wrap justify-center gap-3 pt-4 text-sm">
                <Link
                    :href="route('home')"
                    class="rounded-full border border-white/50 px-6 py-3 text-sm font-semibold uppercase tracking-[0.4em] text-white transition hover:border-white hover:bg-white/10"
                >
                    Back to Welcome
                </Link>
                <Link
                    v-if="isOneTime"
                    :href="route('guest.donation', undefined, false)"
                    class="rounded-full border border-indigo-400 px-6 py-3 text-sm font-semibold uppercase tracking-[0.4em] text-indigo-200 transition hover:border-indigo-100 hover:bg-indigo-500/30"
                >
                    Record another one-day meal
                </Link>
            </div>
        </main>
    </div>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps<{
    donation: {
        relationship: string;
        amount: number | null;
        elder_name: string | null;
        mode?: 'one_time' | 'sponsorship';
    };
}>();

const mode = computed(() => props.donation.mode ?? 'sponsorship');
const isOneTime = computed(() => mode.value === 'one_time');

const giverLabel = computed(() => {
    if (isOneTime.value) {
        return 'Thank you for the one-day meal contribution!';
    }
    if (props.donation.elder_name) {
        return `You’re supporting ${props.donation.elder_name} as a ${props.donation.relationship}`;
    }
    return `You requested a ${props.donation.relationship} sponsorship`;
});

const reminderText = computed(() => {
    if (isOneTime.value) {
        return 'Keep your transfer proof handy so the branch team can match it with the elder who received the meal.';
    }
    return props.donation.elder_name
        ? `Remember to visit and encourage ${props.donation.elder_name} whenever you can — your ongoing support changes lives.`
        : 'We’ll let you know as soon as a suitable elder is confirmed for this relationship.';
});

const headingText = computed(() =>
    isOneTime.value ? 'Thanks for the meal!' : 'Thank You!',
);

const descriptionText = computed(() =>
    isOneTime.value
        ? 'Your one-day meal will bring immediate comfort. We log the receipt so the branch team can confirm that the food delivery matched the need.'
        : 'We received your pledge. Our social care team will match you with the right elder and confirm the transfer shortly.',
);

const instructionsTitle = computed(() =>
    isOneTime.value ? 'Meal next steps' : 'What happens next',
);

const nextSteps = computed(() =>
    isOneTime.value
        ? [
              'Share the transfer code or receipt with the branch so we can reconcile it quickly.',
              'Expect a message from the care team once the elder receives the meal.',
              'Bookmark this page to revisit the welcome screen and track new ways to help.',
          ]
        : [
              'We’ll confirm the elder assignment and match it with the amount you provided.',
              'Keep the payment confirmation (Telebirr receipt or bank reference) for our team.',
              'Expect a short email/message with your elder’s story and updates.',
          ],
);

const tipsTitle = computed(() =>
    isOneTime.value ? 'Helpful reminders' : 'Donor tips',
);

const donorTips = computed(() =>
    isOneTime.value
        ? [
              'Talk to your friends and family about how shared meals can keep elders strong.',
              'Save the transfer reference for future support or follow-up questions.',
              'Return to this page if you’d like to log another meal donation.',
          ]
        : [
              'Set a reminder to check on your elder and send encouragement.',
              'Share your sponsorship on social so others can join the circle.',
              'Save this page or bookmark the welcome screen to log back in anytime.',
          ],
);
</script>
