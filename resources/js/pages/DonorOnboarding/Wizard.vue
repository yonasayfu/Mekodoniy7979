<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

type Step = {
    key: string;
    title: string;
    description: string;
};

const props = defineProps<{
    profile: {
        relationship_goal: string | null;
        monthly_budget: number | null;
        frequency: string;
        preferred_contact_method: string | null;
        contact_channels: string[];
        payment_preference: string | null;
        notes: string | null;
        onboarding_step: string;
        is_completed: boolean;
    };
    steps: Step[];
}>();

const stepIndex = computed(() =>
    Math.max(
        0,
        props.steps.findIndex((step) => step.key === props.profile.onboarding_step),
    ),
);

const currentStep = ref(stepIndex.value === -1 ? 0 : stepIndex.value);

const form = useForm({
    relationship_goal: props.profile.relationship_goal ?? '',
    monthly_budget: props.profile.monthly_budget ?? 1500,
    frequency: props.profile.frequency ?? 'monthly',
    preferred_contact_method: props.profile.preferred_contact_method ?? '',
    contact_channels: props.profile.contact_channels ?? [],
    payment_preference: props.profile.payment_preference ?? '',
    notes: props.profile.notes ?? '',
    onboarding_step: props.profile.onboarding_step ?? 'relationship',
    complete: false,
});

watch(
    () => currentStep.value,
    (step) => {
        form.onboarding_step = props.steps[step]?.key ?? 'relationship';
    },
);

const channels = [
    { label: 'SMS', value: 'sms' },
    { label: 'Email', value: 'email' },
    { label: 'Phone Call', value: 'phone' },
    { label: 'WhatsApp', value: 'whatsapp' },
];

const relationshipOptions = [
    { key: 'father', title: 'Father', copy: 'Full monthly coverage + visits.' },
    { key: 'mother', title: 'Mother', copy: 'Nurturing support + essentials.' },
    { key: 'brother', title: 'Brother', copy: 'Reliable monthly stipend.' },
    { key: 'sister', title: 'Sister', copy: 'Light but steady companionship.' },
    { key: 'open', title: 'Open Match', copy: 'Let Mekodonia decide.' },
];

const frequencyOptions = [
    { label: 'Monthly', value: 'monthly' },
    { label: 'Quarterly', value: 'quarterly' },
    { label: 'Annually', value: 'annually' },
    { label: 'One-time', value: 'once' },
];

const paymentChoices = [
    { label: 'Telebirr Auto-Debit', value: 'telebirr_auto', helper: 'Recommended for donors in Ethiopia using Telebirr.' },
    { label: 'CBE Auto-Debit', value: 'cbe_auto', helper: 'Connect your CBE account for scheduled pulls.' },
    { label: 'Manual / Reminders Only', value: 'manual', helper: 'We will remind you each cycle.' },
];

const isLastStep = computed(() => currentStep.value === props.steps.length - 1);

const toggleChannel = (value: string) => {
    if (form.contact_channels.includes(value)) {
        form.contact_channels = form.contact_channels.filter((item) => item !== value);
    } else {
        form.contact_channels = [...form.contact_channels, value];
    }
};

const saveProgress = (complete = false) => {
    form.complete = complete;
    form.post(route('donors.onboarding.store'), {
        preserveScroll: true,
        onSuccess: () => {
            if (!complete && !isLastStep.value) {
                currentStep.value += 1;
            }
        },
    });
};
</script>

<template>
    <Head title="Donor Onboarding" />

    <AppLayout>
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-6 px-4 py-10">
            <div class="text-center">
                <p class="text-sm uppercase tracking-[0.35em] text-indigo-500">
                    Mekodonia
                </p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                    Let’s personalize your giving journey
                </h1>
                <p class="mt-3 text-base text-slate-600 dark:text-slate-300">
                    Answer a few quick questions so we can match you with the right elders,
                    payment cadence, and communication rhythm.
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-3">
                <button
                    v-for="(step, index) in steps"
                    :key="step.key"
                    class="relative flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition"
                    :class="[
                        index === currentStep
                            ? 'bg-indigo-600 text-white shadow-lg'
                            : index < currentStep
                              ? 'bg-indigo-100 text-indigo-700'
                              : 'bg-slate-100 text-slate-500',
                    ]"
                    type="button"
                    @click="currentStep = index"
                >
                    <span class="size-2 rounded-full bg-white/70"></span>
                    {{ step.title }}
                </button>
            </div>

            <GlassCard variant="lite" padding="p-6">
                <div class="flex flex-col gap-2">
                    <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">
                        {{ steps[currentStep]?.title }}
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        {{ steps[currentStep]?.description }}
                    </p>
                </div>

                <!-- Step 1 -->
                <div v-if="steps[currentStep]?.key === 'relationship'" class="mt-6 grid gap-4 md:grid-cols-2">
                    <button
                        v-for="option in relationshipOptions"
                        :key="option.key"
                        type="button"
                        class="rounded-2xl border p-4 text-left transition hover:-translate-y-1 hover:shadow-lg focus:outline-none"
                        :class="[
                            form.relationship_goal === option.key
                                ? 'border-indigo-500 bg-indigo-50/70'
                                : 'border-slate-200 bg-white dark:bg-slate-900/40',
                        ]"
                        @click="form.relationship_goal = option.key"
                    >
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                            {{ option.title }}
                        </h3>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                            {{ option.copy }}
                        </p>
                    </button>
                    <InputError :message="form.errors.relationship_goal" class="md:col-span-2" />
                </div>

                <!-- Step 2 -->
                <div v-else-if="steps[currentStep]?.key === 'pledge'" class="mt-6 space-y-6">
                    <div>
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Monthly pledge target (ETB)
                        </label>
                        <input
                            v-model.number="form.monthly_budget"
                            type="number"
                            min="100"
                            step="50"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-base focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-900/40"
                        />
                        <InputError :message="form.errors.monthly_budget" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Frequency
                        </label>
                        <div class="mt-3 flex flex-wrap gap-3">
                            <button
                                v-for="freq in frequencyOptions"
                                :key="freq.value"
                                type="button"
                                class="rounded-full px-4 py-2 text-sm font-medium transition"
                                :class="[
                                    form.frequency === freq.value
                                        ? 'bg-indigo-600 text-white shadow'
                                        : 'bg-slate-100 text-slate-600 dark:bg-slate-800/60 dark:text-slate-300',
                                ]"
                                @click="form.frequency = freq.value"
                            >
                                {{ freq.label }}
                            </button>
                        </div>
                        <InputError :message="form.errors.frequency" class="mt-2" />
                    </div>
                </div>

                <!-- Step 3 -->
                <div v-else-if="steps[currentStep]?.key === 'contact'" class="mt-6 space-y-6">
                    <div>
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Preferred primary contact
                        </label>
                        <select
                            v-model="form.preferred_contact_method"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-base focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-900/40"
                        >
                            <option disabled value="">Select an option</option>
                            <option value="sms">SMS</option>
                            <option value="email">Email</option>
                            <option value="phone">Phone Call</option>
                            <option value="whatsapp">WhatsApp</option>
                        </select>
                        <InputError :message="form.errors.preferred_contact_method" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Backup channels
                        </label>
                        <div class="mt-3 flex flex-wrap gap-3">
                            <button
                                v-for="channel in channels"
                                :key="channel.value"
                                type="button"
                                class="rounded-full px-4 py-2 text-sm transition"
                                :class="[
                                    form.contact_channels.includes(channel.value)
                                        ? 'bg-indigo-600 text-white shadow'
                                        : 'bg-slate-100 text-slate-600 dark:bg-slate-800/60 dark:text-slate-300',
                                ]"
                                @click="toggleChannel(channel.value)"
                            >
                                {{ channel.label }}
                            </button>
                        </div>
                        <InputError :message="form.errors.contact_channels" class="mt-2" />
                    </div>
                </div>

                <!-- Step 4 -->
                <div v-else-if="steps[currentStep]?.key === 'payment'" class="mt-6 space-y-6">
                    <div class="grid gap-4 md:grid-cols-3">
                        <button
                            v-for="choice in paymentChoices"
                            :key="choice.value"
                            type="button"
                            class="rounded-2xl border p-4 text-left transition hover:-translate-y-1 hover:shadow-lg focus:outline-none"
                            :class="[
                                form.payment_preference === choice.value
                                    ? 'border-indigo-500 bg-indigo-50/70'
                                    : 'border-slate-200 bg-white dark:bg-slate-900/40',
                            ]"
                            @click="form.payment_preference = choice.value"
                        >
                            <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                                {{ choice.label }}
                            </h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                {{ choice.helper }}
                            </p>
                        </button>
                    </div>
                    <InputError :message="form.errors.payment_preference" />

                    <div>
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Anything else we should know?
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-base focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-900/40"
                            placeholder="Visits? Dietary preferences? Family-only contact?"
                        />
                        <InputError :message="form.errors.notes" class="mt-2" />
                    </div>
                </div>
            </GlassCard>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <Link
                    v-if="currentStep > 0"
                    href="#"
                    class="text-sm font-medium text-slate-500 transition hover:text-slate-700 dark:text-slate-400"
                    @click.prevent="currentStep -= 1"
                >
                    Back
                </Link>
                <span v-else></span>

                <div class="flex gap-3">
                    <GlassButton variant="secondary" @click="saveProgress(false)">
                        Save Step
                    </GlassButton>
                    <GlassButton
                        variant="primary"
                        @click="isLastStep ? saveProgress(true) : saveProgress(false)"
                    >
                        {{ isLastStep ? 'Finish & Go to Dashboard' : 'Next' }}
                    </GlassButton>
                </div>
            </div>

            <p class="text-center text-xs text-slate-500">
                Want to pause? You can always revisit this onboarding from the donor dashboard.
            </p>
        </div>
    </AppLayout>
</template>
