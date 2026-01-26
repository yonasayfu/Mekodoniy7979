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
                    <div class="space-y-1 text-[11px] text-slate-300">
                    <p>{{ paymentGatewayLabel }}</p>
                    <p>{{ paymentStatusLabel }}</p>
                    <p>
                        Reference:
                        <span class="font-semibold text-white">{{ paymentReferenceLabel }}</span>
                    </p>
                    <p>
                        Cadence:
                        <span class="font-semibold text-white">{{ cadenceLabel }}</span>
                    </p>
                    <p>
                        {{ scheduleLabel }}
                    </p>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <a
                        v-if="receiptLink"
                        :href="receiptLink"
                        target="_blank"
                        rel="noreferrer"
                        class="rounded-full border border-indigo-200 px-3 py-1 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 dark:border-indigo-500/40 dark:text-indigo-200"
                    >
                        Download receipt
                    </a>
                    <a
                        v-if="mandateLink"
                        :href="mandateLink"
                        target="_blank"
                        rel="noreferrer"
                        class="rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:text-slate-100"
                    >
                        Download mandate
                        </a>
                    </div>
                    <div
                        v-if="memberCredentials"
                        class="mt-4 rounded-2xl border border-indigo-300/60 bg-indigo-50/60 p-3 text-xs text-slate-700 dark:border-indigo-500/40 dark:bg-indigo-500/10 dark:text-indigo-100"
                    >
                        <p class="font-semibold text-slate-900 dark:text-white">
                            Your member access
                        </p>
                        <p>
                            Phone: <strong>{{ memberCredentials.phone }}</strong>
                        </p>
                        <p>
                            Password: <strong>{{ memberCredentials.password }}</strong>
                        </p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-300">
                            Use these credentials to log in and review your history.
                        </p>
                    </div>
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
            <p class="mt-4 text-[11px] text-slate-300">
                Prefer to check all your gifts before editing a pledge? Visit
                <Link
                    :href="myDonationsLink"
                    class="font-semibold text-indigo-200 hover:text-white"
                >
                    My donations
                </Link>
                for a full archive.
            </p>
            <div class="flex flex-wrap justify-center gap-3 pt-2 text-sm">
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
                <Link
                    v-if="managePledgeLink"
                    :href="managePledgeLink"
                    class="rounded-full border border-emerald-500 px-6 py-3 text-sm font-semibold uppercase tracking-[0.4em] text-emerald-200 transition hover:border-emerald-400 hover:bg-emerald-500/20"
                >
                    Manage my pledge
                </Link>
                <Link
                    :href="myDonationsLink"
                    class="rounded-full border border-slate-200 px-6 py-3 text-sm font-semibold uppercase tracking-[0.4em] text-slate-700 transition hover:border-slate-400 hover:text-slate-900"
                >
                    View my donations
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
        cadence?: string | null;
        payment_gateway?: string | null;
        payment_status?: string | null;
        deduction_schedule?: string | null;
        receipt_url?: string | null;
        mandate_url?: string | null;
        payment_reference?: string | null;
        donor_name?: string | null;
        donor_phone?: string | null;
        member_login_phone?: string | null;
        member_password?: string | null;
    };
}>();

const mode = computed(() => props.donation.mode ?? 'sponsorship');
const isOneTime = computed(() => mode.value === 'one_time');

const cadenceLabel = computed(() => {
    if (props.donation.cadence) {
        return props.donation.cadence;
    }

    return isOneTime.value ? 'One-time' : 'Recurring';
});

const scheduleLabel = computed(() =>
    props.donation.deduction_schedule
        ? props.donation.deduction_schedule
        : 'Schedule will follow once the finance team confirms the transfer.',
);

const paymentGatewayLabel = computed(() => {
    if (!props.donation.payment_gateway) {
        return 'Payment method: not specified';
    }

    return props.donation.payment_gateway === 'telebirr'
        ? 'Telebirr transfer'
        : props.donation.payment_gateway === 'bank'
        ? 'Bank transfer'
        : props.donation.payment_gateway;
});

const paymentStatusLabel = computed(() =>
    props.donation.payment_status
        ? `Status: ${props.donation.payment_status}`
        : 'Status: pending confirmation',
);

const paymentReferenceLabel = computed(
    () => props.donation.payment_reference ?? 'Awaiting reference link from Telebirr or bank transfer',
);

const receiptLink = computed(() => props.donation.receipt_url ?? null);
const mandateLink = computed(() => props.donation.mandate_url ?? null);

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

const memberCredentials = computed(() => {
    if (!props.donation.member_login_phone || !props.donation.member_password) {
        return null;
    }

    return {
        phone: props.donation.member_login_phone,
        password: props.donation.member_password,
    };
});

const managePledgeLink = computed(() =>
    props.donation.payment_reference
        ? `${route('guest.donation', undefined, false)}?payment_reference=${encodeURIComponent(
              props.donation.payment_reference,
          )}`
        : null,
);

const myDonationsLink = route('donors.donations.index', undefined, false);
</script>
