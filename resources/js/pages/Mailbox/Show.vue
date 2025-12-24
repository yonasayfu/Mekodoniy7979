<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    message: Object,
});

const viewMode = ref('html'); // html, plain, raw

const getRecipients = (type) => {
    return props.message.recipients
        .filter((r) => r.type === type)
        .map((r) => r.address)
        .join(', ');
};

const from = computed(() => getRecipients('from'));
const to = computed(() => getRecipients('to'));
const cc = computed(() => getRecipients('cc'));
const bcc = computed(() => getRecipients('bcc'));

const resetLink = computed(() => {
    if (!props.message.html_body) return null;
    const matches = props.message.html_body.match(
        /href="([^"\s]*\/reset-password\/[^"\s]*)"/,
    );
    return matches ? matches[1] : null;
});

const processForm = useForm({
    note: '',
});

const markAsProcessed = () => {
    processForm.post(`/mailbox/${props.message.id}/process`, {
        preserveScroll: true,
        onSuccess: () => processForm.reset(),
    });
};
</script>

<template>
    <Head :title="`Mailbox - ${message.subject}`" />

    <div class="p-6 md:p-10">
        <div class="mb-4">
            <Link
                href="/mailbox"
                class="text-sm text-indigo-600 hover:text-indigo-900"
                >&larr; Back to Inbox</Link
            >
        </div>

        <div class="mb-6 flex items-center justify-between">
            <h1 class="truncate text-3xl font-bold text-gray-800">
                {{ message.subject }}
            </h1>
        </div>

        <!-- Workflow Actions -->
        <div
            v-if="message.status !== 'processed'"
            class="mb-6 rounded-lg border-l-4 border-yellow-500 bg-yellow-100 p-4 text-yellow-800 shadow-md"
            role="alert"
        >
            <h3 class="font-bold">Password Reset Workflow</h3>
            <div class="mt-2 items-center md:flex md:space-x-4">
                <a
                    v-if="resetLink"
                    :href="resetLink"
                    target="_blank"
                    class="mb-2 inline-block rounded bg-blue-500 px-4 py-2 font-bold text-white hover:bg-blue-700 md:mb-0"
                >
                    Open Reset Link
                </a>
                <span
                    v-else
                    class="mb-2 inline-block text-sm text-gray-600 md:mb-0"
                    >Reset link not found in email body.</span
                >

                <form
                    @submit.prevent="markAsProcessed"
                    class="flex flex-grow items-center space-x-2"
                >
                    <input
                        type="text"
                        v-model="processForm.note"
                        placeholder="Add an optional note..."
                        class="w-full rounded-md border px-3 py-2 text-gray-900"
                    />
                    <button
                        type="submit"
                        :disabled="processForm.processing"
                        class="rounded bg-green-500 px-4 py-2 font-bold text-white hover:bg-green-700 disabled:opacity-50"
                    >
                        Mark as Processed
                    </button>
                </form>
            </div>
        </div>
        <div
            v-else
            class="mb-6 rounded-lg border-l-4 border-green-500 bg-green-100 p-4 text-green-800 shadow-md"
            role="alert"
        >
            <p class="font-bold">This message was marked as processed.</p>
        </div>

        <div class="rounded-lg bg-white shadow-md">
            <!-- Headers -->
            <div class="border-b p-6">
                <div class="grid grid-cols-1 gap-2 text-sm md:grid-cols-6">
                    <div class="font-bold md:col-span-1">From:</div>
                    <div class="md:col-span-5">{{ from }}</div>
                    <div class="font-bold md:col-span-1">To:</div>
                    <div class="md:col-span-5">{{ to }}</div>
                    <div v-if="cc" class="font-bold md:col-span-1">CC:</div>
                    <div v-if="cc" class="md:col-span-5">{{ cc }}</div>
                    <div class="font-bold md:col-span-1">Date:</div>
                    <div class="md:col-span-5">
                        {{ new Date(message.created_at).toLocaleString() }}
                    </div>
                </div>
            </div>

            <!-- View Toggles -->
            <div class="flex items-center space-x-2 bg-gray-50 p-3">
                <button
                    @click="viewMode = 'html'"
                    :class="{ 'bg-blue-500 text-white': viewMode === 'html' }"
                    class="rounded-md px-3 py-1 text-sm hover:bg-gray-200"
                >
                    HTML
                </button>
                <button
                    @click="viewMode = 'plain'"
                    :class="{ 'bg-blue-500 text-white': viewMode === 'plain' }"
                    class="rounded-md px-3 py-1 text-sm hover:bg-gray-200"
                >
                    Plain
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                <iframe
                    v-if="viewMode === 'html' && message.html_body"
                    :srcdoc="message.html_body"
                    class="h-96 w-full rounded-md border"
                ></iframe>
                <pre
                    v-if="viewMode === 'plain'"
                    class="text-sm whitespace-pre-wrap"
                    >{{ message.text_body }}</pre
                >
                <div
                    v-if="viewMode === 'html' && !message.html_body"
                    class="text-gray-500"
                >
                    No HTML body available.
                </div>
            </div>

            <!-- Attachments -->
            <div
                v-if="message.attachments && message.attachments.length > 0"
                class="border-t p-6"
            >
                <h3 class="mb-2 font-bold">Attachments</h3>
                <ul>
                    <li
                        v-for="attachment in message.attachments"
                        :key="attachment.id"
                    >
                        <!-- TODO: Add download link -->
                        <span class="text-blue-600 hover:underline">{{
                            attachment.filename
                        }}</span>
                        ({{ (attachment.size / 1024).toFixed(2) }} KB)
                    </li>
                </ul>
            </div>
        </div>

        <!-- Audit Trail -->
        <div class="mt-8">
            <h2 class="mb-4 text-2xl font-bold text-gray-800">History</h2>
            <div class="space-y-4 rounded-lg bg-white p-6 text-sm shadow-md">
                <div
                    v-for="event in message.events"
                    :key="event.id"
                    class="flex justify-between"
                >
                    <span
                        >{{ event.event_type }} by
                        {{ event.user?.name ?? 'System' }}</span
                    >
                    <span class="text-gray-500">{{
                        new Date(event.created_at).toLocaleString()
                    }}</span>
                </div>
                <div
                    v-if="!message.events || message.events.length === 0"
                    class="text-gray-500"
                >
                    No history recorded.
                </div>
            </div>
        </div>
    </div>
</template>
