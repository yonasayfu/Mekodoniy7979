<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import pickBy from 'lodash/pickBy';
import throttle from 'lodash/throttle';
import { onMounted, onUnmounted, reactive, watch } from 'vue';

const props = defineProps({
    messages: Object,
    filters: Object,
});

const form = reactive({
    email: props.filters.email,
});

watch(
    form,
    throttle(() => {
        router.get('/mailbox', pickBy(form), {
            preserveState: true,
            replace: true,
        });
    }, 300),
    { deep: true },
);

let polling = null;

onMounted(() => {
    polling = setInterval(() => {
        router.reload({
            only: ['messages'],
            preserveState: true,
            preserveScroll: true,
        });
    }, 5000);
});

onUnmounted(() => {
    clearInterval(polling);
});

const getToRecipients = (recipients) => {
    if (!recipients) return [];
    return recipients.filter((r) => r.type === 'to');
};
</script>

<template>
    <Head title="Mailbox" />

    <div class="p-6 md:p-10">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">Mailbox</h1>
        </div>

        <div class="mb-6">
            <input
                class="focus:ring-opacity-50 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 sm:w-1/2 md:w-1/3"
                type="text"
                name="email"
                placeholder="Search by recipient email..."
                v-model="form.email"
            />
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-md">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-6 pb-4">Subject</th>
                        <th class="px-6 pt-6 pb-4">From</th>
                        <th class="px-6 pt-6 pb-4">To</th>
                        <th class="px-6 pt-6 pb-4">Received</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="message in messages.data"
                        :key="message.id"
                        class="focus-within:bg-gray-100 hover:bg-gray-100"
                    >
                        <td class="border-t">
                            <Link
                                class="flex items-center px-6 py-4 focus:text-indigo-500"
                                :href="`/mailbox/${message.id}`"
                            >
                                {{ message.subject }}
                            </Link>
                        </td>
                        <td class="border-t">
                            <span class="flex items-center px-6 py-4">
                                {{ message.from?.address }}
                            </span>
                        </td>
                        <td class="border-t">
                            <span class="flex items-center px-6 py-4">
                                <span
                                    v-for="(
                                        recipient, index
                                    ) in getToRecipients(message.recipients)"
                                    :key="recipient.id"
                                >
                                    {{ recipient.address
                                    }}<span
                                        v-if="
                                            index <
                                            getToRecipients(message.recipients)
                                                .length -
                                                1
                                        "
                                        >,
                                    </span>
                                </span>
                            </span>
                        </td>
                        <td class="border-t">
                            <span class="flex items-center px-6 py-4">
                                {{
                                    new Date(
                                        message.created_at,
                                    ).toLocaleString()
                                }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="messages.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="4">
                            No messages found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- TODO: Add Pagination links -->
    </div>
</template>
