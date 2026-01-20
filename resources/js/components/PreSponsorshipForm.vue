<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

type RelationshipType = 'father' | 'mother' | 'brother' | 'sister';

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

const showModal = ref(false);
const selectedRelationship = ref<RelationshipType>('father');
const form = ref({
    name: '',
    email: '',
    phone: '',
    relationship_type: selectedRelationship.value,
});

const submitForm = () => {
    form.value.relationship_type = selectedRelationship.value;
    router.post(route('pre-sponsorships.store'), form.value, {
        onSuccess: () => {
            // Optionally, you can reset the form or show a success message
            form.value.name = '';
            form.value.email = '';
            form.value.phone = '';
            selectedRelationship.value = 'father';
            form.value.relationship_type = 'father';
            showModal.value = false;
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors);
            // Handle errors, e.g., display them to the user
        },
    });
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
        <div
            class="relative w-full max-w-md rounded-lg bg-white p-8 shadow-lg dark:bg-gray-800"
        >
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
            <h2 class="mb-6 text-2xl font-bold text-gray-800 dark:text-white">
                Become a
                {{
                    relationshipOptions.find(
                        (option) => option.value === selectedRelationship,
                    )?.label
                }}
                Today
            </h2>
            <p class="mb-4 text-gray-600 dark:text-gray-300">
                {{
                    relationshipDescriptions[
                        selectedRelationship as RelationshipType
                    ]
                }}
            </p>
            <div class="mb-6">
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                    >Relationship Type</label
                >
                <div
                    class="mt-3 grid grid-cols-2 gap-3 text-sm text-gray-700 dark:text-gray-200"
                >
                    <button
                        v-for="option in relationshipOptions"
                        :key="option.value"
                        type="button"
                        class="rounded-md border px-3 py-2 transition"
                        :class="
                            selectedRelationship === option.value
                                ? 'border-indigo-500 bg-indigo-50 text-indigo-600 dark:border-indigo-400 dark:bg-indigo-500/20 dark:text-indigo-200'
                                : 'border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-700'
                        "
                        @click="selectedRelationship = option.value"
                    >
                        {{ option.label }}
                    </button>
                </div>
            </div>
            <form @submit.prevent="submitForm">
                <div class="mb-4">
                    <label
                        for="name"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                        >Name</label
                    >
                    <input
                        type="text"
                        id="name"
                        v-model="form.name"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                </div>
                <div class="mb-4">
                    <label
                        for="email"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                        >Email (Optional)</label
                    >
                    <input
                        type="email"
                        id="email"
                        v-model="form.email"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                </div>
                <div class="mb-6">
                    <label
                        for="phone"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                        >Phone (Optional)</label
                    >
                    <input
                        type="tel"
                        id="phone"
                        v-model="form.phone"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                </div>
                <div class="flex justify-end">
                    <button
                        type="button"
                        @click="closeModal"
                        class="mr-3 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Proceed
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
