<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import http from '@/lib/http';

type RelationshipType = 'father' | 'mother' | 'brother' | 'sister';

interface Interest {
    id: number;
    relationship: RelationshipType;
    created_at: string;
}

const HISTORY_KEY = 'mekodonia:pre-sponsorship-history';

const relationshipOptions: { label: string; value: RelationshipType }[] = [
    { label: 'Father', value: 'father' },
    { label: 'Mother', value: 'mother' },
    { label: 'Brother', value: 'brother' },
    { label: 'Sister', value: 'sister' },
];

const relationshipDescriptions: Record<RelationshipType, string> = {
    father:
        'Extend your family by supporting an elder as a Father figure. Your care adds dignity and stability.',
    mother:
        'Provide warmth and comfort as a Mother sponsor. Your presence brings reassurance and love.',
    brother:
        'Be the dependable Brother who steps in when help is needed most. Your help keeps hope alive.',
    sister:
        'Share kindness as a Sister sponsor. Your empathy offers companionship and security.',
};

const loadHistory = (): Interest[] => {
    try {
        const payload = localStorage.getItem(HISTORY_KEY);
        if (!payload) {
            return [];
        }
        return JSON.parse(payload);
    } catch {
        return [];
    }
};

const persistHistory = (items: Interest[]) => {
    try {
        localStorage.setItem(HISTORY_KEY, JSON.stringify(items));
    } catch {
        // ignore storage errors
    }
};

const history = ref<Interest[]>(loadHistory());
const confirmation = ref<Interest | null>(null);
const generalError = ref<string | null>(null);
const isSubmitting = ref(false);
const errors = ref<Record<string, string[]>>({});

const showModal = ref(false);
const selectedRelationship = ref<RelationshipType>('father');
const form = ref({
    name: '',
    email: '',
    phone: '',
    relationship_type: selectedRelationship.value,
});

const donationLink = computed(() => {
    if (!confirmation.value) {
        return route('guest.donation', undefined, false);
    }
    return route(
        'guest.donation',
        {
            pre_sponsorship_id: confirmation.value.id,
            relationship: confirmation.value.relationship,
            mode: 'sponsorship',
        },
        false,
    );
});

const resetForm = () => {
    form.value.name = '';
    form.value.email = '';
    form.value.phone = '';
    selectedRelationship.value = 'father';
    form.value.relationship_type = 'father';
};

const saveInterest = (entry: Interest) => {
    history.value = [entry, ...history.value].slice(0, 5);
    persistHistory(history.value);
};

const submitForm = async () => {
    isSubmitting.value = true;
    errors.value = {};
    generalError.value = null;
    form.value.relationship_type = selectedRelationship.value;

    try {
        const csrf = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');
        if (csrf) {
            http.defaults.headers.common['X-CSRF-TOKEN'] = csrf;
        }

        const response = await http.post(route('pre-sponsorships.store'), form.value);
        const preS = response.data.pre_sponsorship;
        const entry: Interest = {
            id: preS.id ?? Date.now(),
            relationship: preS.relationship_type,
            created_at: preS.created_at,
        };

        confirmation.value = entry;
        saveInterest(entry);
        resetForm();
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
            return;
        }
        generalError.value = 'We could not save your interest right now. Please try again.';
    } finally {
        isSubmitting.value = false;
    }
};

const openModal = (relationship: RelationshipType = 'father') => {
    selectedRelationship.value = relationship;
    form.value.relationship_type = relationship;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

defineExpose({
    openModal,
});
</script>

<template>
    <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
    >
        <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-gray-800">
            <button
                @click="closeModal"
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            >
                <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    ></path>
                </svg>
            </button>
            <h2 class="mb-4 text-2xl font-bold text-gray-800 dark:text-white">
                Become a
                {{ relationshipOptions.find((option) => option.value === selectedRelationship)?.label }}
                Today
            </h2>
            <p class="mb-3 text-sm text-gray-600 dark:text-gray-300">
                {{ relationshipDescriptions[selectedRelationship] }}
            </p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    Relationship Type
                </label>
                <div class="mt-2 grid grid-cols-2 gap-2 text-sm text-gray-700 dark:text-gray-200">
                    <button
                        v-for="option in relationshipOptions"
                        :key="option.value"
                        type="button"
                        class="rounded-md border px-3 py-2 transition"
                        :class="selectedRelationship === option.value ? 'border-indigo-500 bg-indigo-50 text-indigo-600 dark:border-indigo-400 dark:bg-indigo-500/20 dark:text-indigo-200' : 'border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-700'"
                        @click="selectedRelationship = option.value"
                    >
                        {{ option.label }}
                    </button>
                </div>
            </div>

            <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <label
                        for="name"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                        >Name</label
                    >
                    <input
                        type="text"
                        id="name"
                        v-model="form.name"
                        :disabled="isSubmitting"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                    <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name[0] }}</p>
                </div>

                <div>
                    <label
                        for="email"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                        >Email (Optional)</label
                    >
                    <input
                        type="email"
                        id="email"
                        v-model="form.email"
                        :disabled="isSubmitting"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                    <p v-if="errors.email" class="mt-1 text-xs text-red-600">{{ errors.email[0] }}</p>
                </div>

                <div>
                    <label
                        for="phone"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                        >Phone (Optional)</label
                    >
                    <input
                        type="tel"
                        id="phone"
                        v-model="form.phone"
                        :disabled="isSubmitting"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                    <p v-if="errors.phone" class="mt-1 text-xs text-red-600">{{ errors.phone[0] }}</p>
                </div>

                <div v-if="generalError" class="rounded-md bg-rose-50 p-3 text-sm text-rose-800">
                    {{ generalError }}
                </div>

                <div class="flex items-center justify-between">
                    <button
                        type="button"
                        @click="closeModal"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <span v-if="isSubmitting">Saving…</span>
                        <span v-else>Proceed</span>
                    </button>
                </div>
            </form>

            <div v-if="confirmation" class="mt-4 rounded-md border border-emerald-200 bg-emerald-50 p-3 text-sm text-slate-900">
                <p>
                    We recorded your
                    {{
                        relationshipOptions.find((option) => option.value === confirmation.relationship)?.label ??
                        confirmation.relationship
                    }}
                    interest (reference #{{ confirmation.id }}).
                </p>
                <p class="text-xs text-slate-500">
                    Submitted {{ new Date(confirmation.created_at).toLocaleString() }}
                </p>
                <Link
                    :href="donationLink"
                    class="mt-2 inline-flex items-center rounded-full border border-emerald-300 bg-emerald-100 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.4em] text-emerald-700 transition hover:bg-emerald-200"
                >
                    Continue to Donation
                </Link>
            </div>

            <div v-if="history.length" class="mt-4 text-sm text-slate-600 dark:text-slate-300">
                <p class="mb-2 text-xs uppercase tracking-[0.4em] text-slate-500">Recent interests</p>
                <ul class="space-y-2">
                    <li
                        v-for="item in history"
                        :key="item.id"
                        class="flex items-center justify-between rounded-md border border-slate-200 px-3 py-2 text-xs"
                    >
                        <span>
                            {{
                                relationshipOptions.find((option) => option.value === item.relationship)?.label ??
                                item.relationship
                            }}
                        </span>
                        <span class="text-slate-500">
                            {{ new Date(item.created_at).toLocaleString() }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
