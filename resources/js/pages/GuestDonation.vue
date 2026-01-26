<script setup lang="ts">
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import http from '@/lib/http';
import { computed, onMounted, ref, watch } from 'vue';
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

type RelationshipPreset = 'father' | 'mother' | 'brother' | 'sister';

const relationshipOptions: Array<{
    value: RelationshipPreset;
    label: string;
    description: string;
}> = [
    {
        value: 'father',
        label: 'Father',
        description: 'Extend your care as a Father sponsor—monthly essentials and visits stay on schedule.',
    },
    {
        value: 'mother',
        label: 'Mother',
        description: 'Bring warmth and comfort through a Mother sponsorship that funds meals and companionship.',
    },
    {
        value: 'brother',
        label: 'Brother',
        description: 'Be the dependable Brother who keeps healthcare, visits, and hope consistent.',
    },
    {
        value: 'sister',
        label: 'Sister',
        description: 'Share empathy as a Sister sponsor by funding wellness kits and regular check-ins.',
    },
];

const relationshipCopy: Record<RelationshipPreset, string> = {
    father:
        'You’re beginning a Father sponsorship—covering monthly essentials and visits.',
    mother:
        'You’re beginning a Mother sponsorship—bringing warmth, meals, and comfort.',
    brother:
        'You’re beginning a Brother sponsorship—standing beside an elder with regular support.',
    sister:
        'You’re beginning a Sister sponsorship—sharing empathy, wellness checks, and care.',
};

const relationshipLabelMap: Record<RelationshipPreset, string> = {
    father: 'Father',
    mother: 'Mother',
    brother: 'Brother',
    sister: 'Sister',
};

const normalizeRelationshipParam = (
    value?: string | null,
): RelationshipPreset | null => {
    if (!value) {
        return null;
    }

    return (
        relationshipOptions.find((option) => option.value === value)?.value ?? null
    );
};

const selectedRelationshipFromQuery = normalizeRelationshipParam(
    getQueryParam('relationship'),
);
const preSponsorshipId = getQueryParam('pre_sponsorship_id');
const modeFromQuery = (getQueryParam('mode') as DonationMode | null) ?? null;

const inferredMode: DonationMode =
    modeFromQuery ??
    (selectedRelationshipFromQuery ? 'sponsorship' : 'one_time');

const donationMode = ref<DonationMode>(inferredMode);

const inferredNotes = preSponsorshipId
    ? `Lead #${preSponsorshipId}${
          selectedRelationshipFromQuery &&
          relationshipLabelMap[selectedRelationshipFromQuery]
              ? ` (${relationshipLabelMap[selectedRelationshipFromQuery]})`
              : ''
      }`
    : '';

type PaymentMethod = 'telebirr' | 'bank' | 'manual';

const paymentMethods: Array<{
    value: PaymentMethod;
    label: string;
    description: string;
}> = [
    {
        value: 'telebirr',
        label: 'Telebirr transfer',
        description: 'Scan the QR code and complete the transfer inside Telebirr; upload the confirmation below.',
    },
    {
        value: 'bank',
        label: 'Bank transfer',
        description: 'Download the mandate, sign it, and re-upload. Use the account list to make a deposit.',
    },
];

type CadenceOption = 'one_time' | 'weekly' | 'monthly' | 'quarterly' | 'annual' | 'custom';

const cadenceOptions: Array<{ value: CadenceOption; label: string; description: string }> = [
    { value: 'one_time', label: 'One-time', description: 'Single contribution or meal.' },
    { value: 'weekly', label: 'Weekly', description: 'Deduct every week (4x per month).' },
    { value: 'monthly', label: 'Monthly', description: 'Ideal for sponsorship commitments.' },
    { value: 'quarterly', label: 'Quarterly', description: 'Every 3 months for seasonal giving.' },
    { value: 'annual', label: 'Annual', description: 'One deduction per year.' },
    { value: 'custom', label: 'Custom', description: 'Define a bespoke schedule (months/days/etc.).' },
];

const initialCadence: CadenceOption =
    donationMode.value === 'sponsorship' ? 'monthly' : 'one_time';

const selectedCadence = ref<CadenceOption>(initialCadence);
const selectedPaymentMethod = ref<PaymentMethod>('telebirr');
const recurrenceDuration = ref<number | null>(null);
const deductionSchedule = ref('');

const generatePaymentReference = () =>
    `MHC-${Date.now().toString().slice(-5)}-${Math.floor(
        1000 + Math.random() * 9000,
    )}`;
const paymentReference = ref(generatePaymentReference());
const form = useForm({
    amount: 70,
    name: '',
    email: '',
    phone: '',
    notes: inferredNotes,
    relationship: selectedRelationshipFromQuery,
    donation_mode: donationMode.value,
    payment_gateway: selectedPaymentMethod.value,
    cadence: initialCadence,
    recurrence_duration: null,
    deduction_schedule: '',
    receipt: null,
    mandate: null,
    existing_donation_id: null,
    elder_id: getQueryParam('elder_id')
        ? parseInt(getQueryParam('elder_id')!)
        : (null as number | null),
    campaign_id: getQueryParam('campaign_id')
        ? parseInt(getQueryParam('campaign_id')!)
        : (null as number | null),
    payment_reference: paymentReference.value,
});
const page = usePage();
const isAuthenticated = computed(() => Boolean(page.props.auth.user));
const prefillLoading = ref(false);
const prefillError = ref('');

const paymentReferenceQuery = getQueryParam('payment_reference');

const prefillDonationBase = async (reference: string) => {
    if (!reference || !isAuthenticated.value) {
        return;
    }

    try {
        prefillLoading.value = true;
        const { data } = await http.get(
            route('donations.prefill', { payment_reference: reference }),
        );
        const donation = data?.donation;
        if (!donation) {
            return;
        }

        const modeFromDonation = donation.donation_mode
            ?? (donation.donation_type === 'guest_sponsorship'
                ? 'sponsorship'
                : 'one_time');
        donationMode.value = modeFromDonation as DonationMode;
        form.setData('donation_mode', donationMode.value);

        selectedRelationship.value = normalizeRelationshipParam(
            donation.relationship,
        );
        selectedPaymentMethod.value = donation.payment_gateway ?? 'manual';
        selectedCadence.value = donation.cadence ?? initialCadence;
        form.setData('amount', donation.amount ?? form.amount);
        form.setData('notes', donation.notes ?? '');
        form.setData('elder_id', donation.elder_id ?? form.elder_id);
        form.setData('campaign_id', donation.campaign_id ?? form.campaign_id);
        form.setData('existing_donation_id', donation.id);
        paymentReference.value = donation.payment_reference ?? paymentReference.value;
        form.setData('payment_reference', paymentReference.value);
    } catch (error) {
        prefillError.value = 'Unable to load your pledge.';
    } finally {
        prefillLoading.value = false;
    }
};

onMounted(() => {
    if (paymentReferenceQuery && isAuthenticated.value) {
        prefillDonationBase(paymentReferenceQuery);
    }
});

const selectedRelationship = ref<RelationshipPreset | null>(
    selectedRelationshipFromQuery,
);

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
    if (!selectedRelationship.value) {
        return null;
    }
    return (
        relationshipCopy[selectedRelationship.value] ??
        'You’re beginning a sponsorship commitment.'
    );
});

const leadContext = computed(() => {
    if (!preSponsorshipId) {
        return null;
    }

    const relationLabel = selectedRelationship.value
        ? relationshipLabelMap[selectedRelationship.value]
        : null;

    return `Pre-sponsorship lead #${preSponsorshipId}${
        relationLabel ? ` (${relationLabel})` : ''
    }`;
});

const activeCadenceDescription = computed(() => {
    const option = cadenceOptions.find(
        (item) => item.value === selectedCadence.value,
    );
    return option ? option.description : '';
});

const homeHref = route('home', undefined, false);

watch(donationMode, (value) => {
    form.setData('donation_mode', value);
    if (value === 'sponsorship' && selectedCadence.value === 'one_time') {
        selectedCadence.value = 'monthly';
    }
    if (value === 'one_time') {
        selectedCadence.value = 'one_time';
    }
});

watch(selectedRelationship, (value) => {
    form.setData('relationship', value);
});

watch(selectedPaymentMethod, (value) => {
    form.setData('payment_gateway', value ?? 'manual');
});

watch(selectedCadence, (value) => {
    form.setData('cadence', value);
});

watch(recurrenceDuration, (value) => {
    form.setData('recurrence_duration', value ?? null);
});

watch(deductionSchedule, (value) => {
    form.setData('deduction_schedule', value);
});

watch(paymentReference, (value) => {
    form.setData('payment_reference', value ?? null);
});

const handleReceiptUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;
    form.setData('receipt', file);
};

const handleMandateUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;
    form.setData('mandate', file);
};

const selectPaymentMethod = (method: PaymentMethod) => {
    selectedPaymentMethod.value = method;
};

const selectRelationship = (relation: RelationshipPreset | null) => {
    selectedRelationship.value = relation;
};

const selectCadenceOption = (option: CadenceOption) => {
    selectedCadence.value = option;
};

const regeneratePaymentReference = () => {
    paymentReference.value = generatePaymentReference();
};

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
                            @click.prevent="donationMode = pill.value"
                        >
                            {{ pill.label }}
                        </button>
                    </div>
                    <div class="mt-4 text-sm">
                        <p v-if="prefillLoading" class="text-indigo-600">
                            Loading your previous pledge…
                        </p>
                        <p v-else-if="form.existing_donation_id" class="text-slate-600 dark:text-slate-300">
                            You are editing pledge #{{ form.existing_donation_id }}.
                        </p>
                        <p v-if="prefillError" class="text-red-600 dark:text-red-400">
                            {{ prefillError }}
                        </p>
                    </div>

                    <div class="mt-6">
                        <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">
                            Choose your relationship
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button
                                v-for="option in relationshipOptions"
                                :key="option.value"
                                type="button"
                                class="rounded-full border px-4 py-2 text-xs font-semibold uppercase transition"
                                :class="[
                                    selectedRelationship === option.value
                                        ? 'border-indigo-500 bg-indigo-50 text-indigo-600 dark:border-indigo-400 dark:bg-indigo-500/10 dark:text-indigo-200'
                                        : 'border-slate-200 text-slate-600 hover:border-slate-400 dark:border-slate-700 dark:text-slate-300',
                                ]"
                                @click.prevent="selectRelationship(option.value)"
                            >
                                {{ option.label }}
                            </button>
                            <button
                                type="button"
                                class="rounded-full border px-4 py-2 text-xs font-semibold uppercase transition"
                                :class="[
                                    !selectedRelationship
                                        ? 'border-indigo-500 bg-indigo-50 text-indigo-600 dark:border-indigo-400 dark:bg-indigo-500/10 dark:text-indigo-200'
                                        : 'border-slate-200 text-slate-600 hover:border-slate-400 dark:border-slate-700 dark:text-slate-300',
                                ]"
                                @click.prevent="selectRelationship(null)"
                            >
                                Custom support
                            </button>
                        </div>
                        <p
                            v-if="selectedRelationship"
                            class="mt-2 text-sm text-slate-600 dark:text-slate-200"
                        >
                            {{ relationshipCopy[selectedRelationship] }}
                        </p>
                        <p
                            v-else
                            class="mt-2 text-sm text-slate-500 dark:text-slate-400"
                        >
                            Selecting a relationship helps our team match you with the right elder.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="mt-8 space-y-6">
                        <input
                            type="hidden"
                            name="existing_donation_id"
                            :value="form.existing_donation_id"
                        />
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
                                Your Phone (used for login)
                            </label>
                            <input
                                id="phone"
                                v-model="form.phone"
                                type="text"
                                required
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
                            Your Name (required for member access)
                        </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
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

                        <div class="space-y-6">
                            <div>
                                <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">
                                    Payment preference
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button
                                        v-for="method in paymentMethods"
                                        :key="method.value"
                                        type="button"
                                        class="flex-1 rounded-full border px-4 py-2 text-left text-[11px] uppercase transition"
                                        :class="[
                                            selectedPaymentMethod === method.value
                                                ? 'border-indigo-500 bg-indigo-50 text-indigo-600 dark:border-indigo-400 dark:bg-indigo-500/10 dark:text-indigo-200'
                                                : 'border-slate-200 text-slate-600 hover:border-indigo-400 dark:border-slate-700 dark:text-slate-300',
                                        ]"
                                    @click.prevent="selectPaymentMethod(method.value)"
                                    >
                                        <span class="block text-[11px] font-semibold">{{ method.label }}</span>
                                        <span class="text-[10px] font-normal text-slate-500 dark:text-slate-400">
                                            {{ method.description }}
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-4 text-sm text-slate-700 shadow-sm dark:border-slate-800 dark:bg-slate-900/40">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">
                                        Payment reference
                                    </p>
                                    <button
                                        type="button"
                                        class="text-xs font-semibold uppercase text-indigo-600 transition hover:text-indigo-800"
                                        @click.prevent="regeneratePaymentReference"
                                    >
                                        New reference
                                    </button>
                                </div>
                                <input
                                    v-model="paymentReference"
                                    type="text"
                                    class="mt-3 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                />
                                <p class="mt-2 text-[11px] text-slate-500 dark:text-slate-400">
                                    Copy this reference into the Telebirr or bank transfer note so our webhook can link your donation.
                                </p>
                                <InputError
                                    :message="form.errors.payment_reference"
                                    class="mt-2"
                                />
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <article
                                    class="rounded-3xl border p-4 shadow-sm transition"
                                    :class="[
                                        selectedPaymentMethod === 'telebirr'
                                            ? 'border-indigo-400 bg-indigo-50/80 shadow-lg dark:border-indigo-300 dark:bg-indigo-900/40'
                                            : 'border-slate-200 bg-white dark:border-gray-700 dark:bg-slate-900/40',
                                    ]"
                                >
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">
                                                Telebirr transfers
                                            </p>
                                            <h3 class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">
                                                Scan the QR
                                            </h3>
                                        </div>
                                        <button
                                            type="button"
                                            class="text-[11px] font-semibold uppercase text-indigo-600 transition hover:text-indigo-800"
                                        @click.prevent="selectPaymentMethod('telebirr')"
                                        >
                                            Use Telebirr
                                        </button>
                                    </div>
                                    <div class="mt-4 space-y-3">
                                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-700">
                                            <img
                                                src="/images/telebirr-qr.svg"
                                                alt="Telebirr QR code"
                                                class="h-40 w-full object-cover"
                                            />
                                        </div>
                                        <div class="text-sm text-slate-600 dark:text-slate-200">
                                            <p class="font-semibold text-slate-900 dark:text-white">
                                                {{
                                                    telebirrInfo
                                                        ? telebirrInfo.receiver_name
                                                        : 'Mekodonia Home Connect'
                                                }}
                                            </p>
                                            <p class="text-sm">
                                                Account code:
                                                <strong>{{ telebirrInfo ? telebirrInfo.account_code : 'Provided on confirmation' }}</strong>
                                            </p>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                                Include reference: {{
                                                    telebirrInfo ? telebirrInfo.reference_hint : 'Guest donation'
                                                }}.
                                                Keep the confirmation for our team.
                                            </p>
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">
                                                Upload Telebirr proof (optional)
                                            </label>
                                            <input
                                                type="file"
                                                accept=".jpg,.jpeg,.png,.pdf"
                                                @change="handleReceiptUpload"
                                                class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError
                                                :message="form.errors.receipt"
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>
                                </article>

                                <article
                                    class="rounded-3xl border p-4 shadow-sm transition"
                                    :class="[
                                        selectedPaymentMethod === 'bank'
                                            ? 'border-indigo-400 bg-white/80 shadow-lg dark:border-indigo-500 dark:bg-slate-900/40'
                                            : 'border-slate-200 bg-white dark:border-gray-700 dark:bg-slate-900/40',
                                    ]"
                                >
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">
                                                Bank transfer
                                            </p>
                                            <h3 class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">
                                                Deposit with a signed mandate
                                            </h3>
                                        </div>
                                        <button
                                            type="button"
                                            class="text-[11px] font-semibold uppercase text-indigo-600 transition hover:text-indigo-800"
                                            @click.prevent="selectPaymentMethod('bank')"
                                        >
                                            Use Bank
                                        </button>
                                    </div>
                                    <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-200">
                                        <p>
                                            <a
                                                href="/pdf/mandate-template.pdf"
                                                target="_blank"
                                                rel="noreferrer"
                                                class="font-semibold text-indigo-600 hover:underline"
                                            >
                                                Download the mandate form
                                            </a>
                                            , sign it, and re-upload below.
                                        </p>
                                        <p>
                                            Notify <a class="text-indigo-600" href="mailto:support@mekodonia.org">support@mekodonia.org</a>
                                            with the transfer reference so we can confirm the payment quickly.
                                        </p>
                                        <div v-if="bankAccounts.length" class="space-y-2 rounded-2xl border border-slate-200 bg-white/90 p-3 dark:border-slate-800 dark:bg-slate-900/40">
                                            <p class="text-[11px] uppercase tracking-[0.4em] text-slate-400 dark:text-slate-500">
                                                Bank list
                                            </p>
                                            <div
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
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label class="text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">
                                            Upload signed mandate or proof
                                        </label>
                                        <input
                                            type="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            @change="handleMandateUpload"
                                            class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        />
                                        <InputError
                                            :message="form.errors.mandate"
                                            class="mt-2"
                                        />
                                    </div>
                                </article>
                            </div>

                            <div class="space-y-3 rounded-3xl border border-slate-200 bg-white p-4 text-sm text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-900/40">
                                <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">
                                    Deduction cadence
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button
                                        v-for="option in cadenceOptions"
                                        :key="option.value"
                                        type="button"
                                        class="rounded-full border px-4 py-2 text-xs font-semibold uppercase transition"
                                        :class="[
                                            selectedCadence === option.value
                                                ? 'border-indigo-500 bg-indigo-50 text-indigo-600 dark:border-indigo-400 dark:bg-indigo-500/10 dark:text-indigo-200'
                                                : 'border-slate-200 text-slate-600 hover:border-indigo-400 dark:border-slate-700 dark:text-slate-300',
                                        ]"
                                        @click.prevent="selectCadenceOption(option.value)"
                                    >
                                        {{ option.label }}
                                    </button>
                                </div>
                                <p
                                    v-if="activeCadenceDescription"
                                    class="text-[11px] text-slate-500 dark:text-slate-400"
                                >
                                    {{ activeCadenceDescription }}
                                </p>
                                <div
                                    v-if="selectedCadence === 'custom'"
                                    class="grid gap-3 md:grid-cols-2"
                                >
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                                            Custom duration (months/days)
                                        </label>
                                        <input
                                            type="number"
                                            min="1"
                                            v-model.number="recurrenceDuration"
                                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            placeholder="e.g. 12"
                                        />
                                        <InputError
                                            :message="form.errors.recurrence_duration"
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                                        Schedule notes (optional)
                                    </label>
                                    <textarea
                                        v-model="deductionSchedule"
                                        rows="2"
                                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        placeholder="Describe the cadence or any special instructions (e.g. deduct for 12 months)"
                                    ></textarea>
                                    <InputError
                                        :message="form.errors.deduction_schedule"
                                        class="mt-2"
                                    />
                                </div>
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
