<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    status: {
        type: String,
        default: '',
    },
});

const form = useForm({
    email: props.email,
    code: '',
});

const submit = () => {
    form.post('/verify-code', {
        onFinish: () => form.reset('code'),
    });
};
</script>

<template>
    <Head title="Enter Verification Code" />

    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-8 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <div class="mb-4 text-sm text-gray-600">
                A 6-digit verification code has been sent to your recovery email
                address. Please enter it below.
            </div>

            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <label
                        for="code"
                        class="block text-sm font-medium text-gray-700"
                        >Verification Code</label
                    >
                    <input
                        id="code"
                        v-model="form.code"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="6"
                        class="focus:ring-opacity-50 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        required
                        autofocus
                        autocomplete="one-time-code"
                    />
                    <div
                        v-if="form.errors.code"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.code }}
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase ring-gray-300 transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:ring focus:outline-none active:bg-gray-900 disabled:opacity-25"
                        :disabled="form.processing"
                    >
                        Verify Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
