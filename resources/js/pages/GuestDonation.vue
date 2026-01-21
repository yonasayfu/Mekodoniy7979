<script setup lang="ts">
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';

type DonationMode = 'one_time' | 'sponsorship';

const props = defineProps<{
    paymentOptions: {
        telebirr: {
            account_code: string;
            receiver_name: string;
            reference_hint: string;
        };
        bankAccounts: {
            bank: string;
            branch: string;
            account_name: string;
            account_number: string;
            iban?: string;
        }[];
    };
}>();

const getQueryParam = (param: string) => {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
};

const relationshipCopy: Record<string, string> = {
    father:
        'You’re beginning a Father sponsorship—covering monthly essentials and visits.',
    mother:
        'You’re beginning a Mother sponsorship—bringing warmth, meals, and comfort.',
    brother:
        'You’re beginning a Brother sponsorship—standing beside an elder with regular support.',
    sister:
        'You’re beginning a Sister sponsorship—sharing empathy, wellness checks, and care.',
};

const relationshipLabelMap: Record<string, string> = {
    father: 'Father',
    mother: 'Mother',
    brother: 'Brother',
    sister: 'Sister',
};

const selectedRelationship = getQueryParam('relationship');
const preSponsorshipId = getQueryParam('pre_sponsorship_id');
const modeFromQuery = (getQueryParam('mode') as DonationMode | null) ?? null;

const inferredMode: DonationMode =
    modeFromQuery ??
    (selectedRelationship ? 'sponsorship' : 'one_time');

const donationMode = ref<DonationMode>(inferredMode);

const inferredNotes = preSponsorshipId
    ? `Lead #${preSponsorshipId}${
          selectedRelationship && relationshipLabelMap[selectedRelationship]
              ? ` (${relationshipLabelMap[selectedRelationship]})`
              : ''
      }`
    : '';

const form = useForm({
    amount: 70,
    name: '',
    email: '',
    phone: '',
    notes: inferredNotes,
    elder_id: getQueryParam('elder_id')
        ? parseInt(getQueryParam('elder_id')!)
        : (null as number | null),
    campaign_id: getQueryParam('campaign_id')
        ? parseInt(getQueryParam('campaign_id')!)
        : (null as number | null),
});

const modePills = [
    { value: 'one_time', label: 'One-Time Meal' },
    { value: 'sponsorship', label: 'Monthly Sponsorship' },
] as const;

const amountSuggestions = [
    { label: '70 ETB — Breakfast', value: 70 },
    { label: '150 ETB — Lunch', value: 150 },
    { label: '250 ETB — Dinner', value: 250 },
];

const telebirrInfo = computed(() => props.paymentOptions?.telebirr ?? null);
const bankAccounts = computed(() => props.paymentOptions?.bankAccounts ?? []);

const relationshipMessage = computed(() => {
    if (!selectedRelationship) {
        return null;
    }
    return (
        relationshipCopy[selectedRelationship] ??
        'You’re beginning a sponsorship commitment.'
    );
});

const leadContext = computed(() => {
    if (!preSponsorshipId) {
        return null;
    }

    return `Pre-sponsorship lead #${preSponsorshipId}${
        selectedRelationship && relationshipLabelMap[selectedRelationship]
            ? ` (${relationshipLabelMap[selectedRelationship]})`
            : ''
    }`;
});

const homeHref = route('home', undefined, false);

const submit = () => {
    form.post(route('donations.guest.store'));
};
</script>

<template>
    <Head title="Donate a Meal" />

    <GuestLayout>
        <div class="flex flex-col items-center justify-center py-12 px-4">
            <div class="w-full max-w-2xl">
                <div class="mb-4 flex justify-between">
                    <Link
                        :href="homeHref"
                        class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >
                        ← Back to Welcome
                    </Link>
                </div>
                <GlassCard>
                    <div class="text-center">
                        <p
                            class="text-xs uppercase tracking-[0.35em] text-indigo-500"
                        >
                            Mekodonia Home Connect
                        </p>
                        <h1 class="mt-2 text-3xl font-bold">
                            {{ donationMode === 'sponsorship'
                                ? 'Sponsor an Elder'
                                : 'Donate a Meal' }}
                        </h1>
                        <p
                            class="mt-2 text-sm text-slate-600 dark:text-slate-400"
                        >
                            {{
                                donationMode === 'sponsorship'
                                    ? 'Complete this quick form to lock your monthly promise—our team will follow up to finalize the match.'
                                    : 'Your contribution can make a huge difference in an elder’s day. Choose an amount and submit securely.'
                            }}
                        </p>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button
                            v-for="pill in modePills"
                            :key="pill.value"
                            type="button"
                            class="flex-1 rounded-full border px-4 py-2 text-sm font-semibold transition"
                            :class="
                                donationMode === pill.value
                                    ? 'border-indigo-500 bg-indigo-50 text-indigo-600 dark:border-indigo-400 dark:bg-indigo-500/10 dark:text-indigo-200'
                                    : 'border-slate-200 text-slate-600 hover:border-slate-300 dark:border-slate-700 dark:text-slate-300'
                            "
                            @click="donationMode = pill.value"
                        >
                            {{ pill.label }}
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="mt-8 space-y-6">
                        <div
                            v-if="
                                selectedRelationship ||
                                donationMode === 'sponsorship'
                            "
                            class="rounded-lg border border-indigo-200 bg-indigo-50/60 px-4 py-3 text-sm font-medium text-indigo-700 dark:border-indigo-500/40 dark:bg-indigo-500/10 dark:text-indigo-200"
                        >
                            <span v-if="relationshipMessage">
                                {{ relationshipMessage }}
                            </span>
                            <span v-else>
                                You’re about to set up your monthly sponsorship.
                                Complete the form to confirm your interest.
                            </span>
                        </div>

                        <div
                            v-if="leadContext"
                            class="rounded-lg border border-amber-200 bg-amber-50/80 px-4 py-3 text-xs font-medium text-amber-700 dark:border-amber-400/40 dark:bg-amber-400/10 dark:text-amber-200"
                        >
                            {{ leadContext }}
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

                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >
                                Donation Amount (ETB)
                            </label>
                            <div class="mt-2 flex flex-col gap-3">
                                <input
                                    type="number"
                                    min="50"
                                    step="10"
                                    v-model.number="form.amount"
                                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-white"
                                    placeholder="Enter the amount you wish to give"
                                />
                                <div class="flex flex-wrap gap-2 text-[11px] text-slate-500">
                                    <button
                                        v-for="item in amountSuggestions"
                                        :key="item.value"
                                        type="button"
                                        @click="form.amount = item.value"
                                        class="rounded-full border border-slate-300 px-3 py-1 capitalize text-slate-600 transition hover:border-indigo-500 hover:text-indigo-500 dark:border-slate-600 dark:text-slate-300"
                                    >
                                        {{ item.label }}
                                    </button>
                                </div>
                            </div>
                            <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Enter any amount (minimum 50 ETB). Your support matters,
                                no matter the size.
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
                                for="notes"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            >
                                Notes or dedication (Optional)
                            </label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                placeholder="Tell us about your intention or leave a dedication message"
                            ></textarea>
                            <InputError
                                :message="form.errors.notes"
                                class="mt-2"
                            />
                        </div>

                        <div
                            v-if="telebirrInfo || bankAccounts.length"
                            class="space-y-4"
                        >
                            <div
                                v-if="telebirrInfo"
                                class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700 shadow-inner dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
                            >
                                <p
                                    class="text-xs uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400"
                                >
                                    Telebirr transfers
                                </p>
                                <p class="mt-2 text-base font-semibold text-slate-900 dark:text-white">
                                    {{ telebirrInfo.receiver_name }}
                                </p>
                                <p class="text-sm">Account code: <strong>{{ telebirrInfo.account_code }}</strong></p>
                                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                    Open Telebirr, choose “Send Money,” paste the code above,
                                    and include this reference: {{ telebirrInfo.reference_hint }}. Once
                                    the transfer completes, keep the confirmation for us.
                                </p>
                            </div>
                            <div
                                v-if="bankAccounts.length"
                                class="rounded-2xl border border-slate-200 bg-white p-4 text-sm text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
                            >
                                <p class="text-xs uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">
                                    Bank transfers
                                </p>
                                <ul class="mt-2 space-y-2 text-[12px]">
                                    <li
                                        v-for="bank in bankAccounts"
                                        :key="bank.account_number"
                                        class="rounded-lg border border-slate-200 p-3 dark:border-slate-800"
                                    >
                                        <p class="font-semibold text-slate-900 dark:text-white">
                                            {{ bank.bank }} — {{ bank.branch }}
                                        </p>
                                        <p>Account: {{ bank.account_number }}</p>
                                        <p>Account name: {{ bank.account_name }}</p>
                                        <p v-if="bank.iban">IBAN: {{ bank.iban }}</p>
                                    </li>
                                </ul>
                                <p class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                                    Email your transaction reference to
                                    <a class="text-indigo-600" href="mailto:support@mekodonia.org">support@mekodonia.org</a>
                                    so we can confirm your sponsorship quickly.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <span
                                class="text-xs text-slate-500 dark:text-slate-400"
                            >
                                Need help? Call Mekodonia support or return to
                                the welcome page.
                            </span>
                            <Link
                                :href="homeHref"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                            >
                                ← Back to Welcome
                            </Link>
                        </div>

                        <div class="flex justify-end">
                            <GlassButton type="submit" :disabled="form.processing">
                                <span v-if="form.processing">Processing...</span>
                                <span v-else>
                                    {{
                                        donationMode === 'sponsorship'
                                            ? 'Submit Sponsorship Request'
                                            : 'Proceed to Payment'
                                    }}
                                </span>
                            </GlassButton>
                        </div>
                    </form>
                </GlassCard>
            </div>
        </div>
    </GuestLayout>
</template>
