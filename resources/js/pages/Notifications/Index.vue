<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import http from '@/lib/http';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';

interface GuestDonationNotificationData {
    donation_id?: number;
    payment_gateway?: string;
    payment_status?: string;
    cadence?: string;
    deduction_schedule?: string;
    receipt_url?: string;
    mandate_url?: string;
    elder_name?: string;
    donation_type?: string;
    url?: string;
    message?: string;
    type?: string;
}

interface NotificationPayload {
    id: string;
    type: string;
    data: GuestDonationNotificationData;
    read_at: string | null;
    created_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    notifications: {
        data: NotificationPayload[];
        links: PaginationLink[];
    };
}>();

const currencyFormatter = new Intl.NumberFormat('en-ET', {
    style: 'currency',
    currency: 'ETB',
    maximumFractionDigits: 0,
});

const formatCurrency = (value?: number | null) =>
    currencyFormatter.format(value ?? 0);

const markAsRead = async (notificationId: string) => {
    try {
        await http.post(`/notifications/${notificationId}/read`);
        router.reload({ only: ['notifications'] });
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
};

const markAllAsRead = async () => {
    try {
        await http.post('/notifications/read-all');
        router.reload({ only: ['notifications'] });
    } catch (error) {
        console.error('Error marking all notifications as read:', error);
    }
};

const unreadNotifications = computed(() =>
    props.notifications.data.filter((notification) => !notification.read_at),
);

const formatTimestamp = (timestamp: string | null) => {
    if (!timestamp) {
        return '';
    }

    const date = new Date(timestamp);

    if (Number.isNaN(date.getTime())) {
        return timestamp;
    }

    return date.toLocaleString();
};

const isGuestDonationNotification = (notification: NotificationPayload) =>
    notification.data?.type === 'donation.guest_recorded';

const confirmingNotificationId = ref<string | null>(null);

const confirmPayment = async (notification: NotificationPayload) => {
    if (!isGuestDonationNotification(notification) || !notification.data.donation_id) {
        return;
    }

    confirmingNotificationId.value = notification.id;

    try {
        await http.post(
            route('donations.confirm', { donation: notification.data.donation_id }),
            { send_receipt: true },
        );
        await markAsRead(notification.id);
        router.reload({ only: ['notifications'] });
    } catch (error) {
        console.error('Error confirming guest payment', error);
    } finally {
        confirmingNotificationId.value = null;
    }
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[{ title: 'Notifications', href: '/notifications' }]"
    >
        <Head title="Notifications" />

        <div
            class="flex items-center justify-between rounded-3xl border border-slate-200/70 bg-white/80 px-6 py-5 shadow-lg dark:border-slate-800/70 dark:bg-slate-900/60"
        >
            <div>
                <h1
                    class="text-2xl font-semibold text-slate-900 dark:text-slate-100"
                >
                    Notifications
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Stay up to date with system events and assignment updates.
                </p>
            </div>
            <button
                v-if="unreadNotifications.length > 0"
                type="button"
                class="rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
                @click="markAllAsRead"
            >
                Mark all as read
            </button>
        </div>

        <div
            class="mt-6 overflow-hidden rounded-3xl border border-slate-200/70 bg-white shadow-xl dark:border-slate-800/70 dark:bg-slate-950/60"
        >
            <div
                v-if="notifications.data.length === 0"
                class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400"
            >
                You don't have any notifications yet.
            </div>
            <ul v-else class="divide-y divide-slate-200 dark:divide-slate-800">
                <li
                    v-for="notification in notifications.data"
                    :key="notification.id"
                    :class="{
                        'bg-indigo-50/60 dark:bg-indigo-500/10':
                            !notification.read_at,
                        'bg-white/40 dark:bg-transparent': notification.read_at,
                    }"
                    class="group transition hover:bg-slate-50 dark:hover:bg-slate-900/80"
                >
                    <div
                        class="flex flex-col gap-2 px-6 py-4 md:flex-row md:items-center md:justify-between"
                    >
                            <div class="flex-1">
                                <Link
                                    :href="
                                        (notification.data?.url as string) || '#'
                                    "
                                    class="inline-flex items-start gap-2 text-left"
                                    @click="
                                        () => {
                                            if (!notification.read_at) {
                                                markAsRead(notification.id);
                                            }
                                        }
                                    "
                                >
                                    <span
                                        class="mt-1 h-2 w-2 rounded-full"
                                        :class="
                                            notification.read_at
                                                ? 'bg-slate-300 dark:bg-slate-600'
                                                : 'bg-indigo-500 dark:bg-indigo-400'
                                        "
                                    />
                                    <div>
                                        <p
                                            class="text-sm font-medium"
                                            :class="
                                                notification.read_at
                                                    ? 'text-slate-600 dark:text-slate-300'
                                                    : 'text-slate-900 dark:text-slate-100'
                                            "
                                        >
                                            {{
                                                (notification.data
                                                    ?.message as string) ??
                                                'Notification'
                                            }}
                                        </p>
                                        <p
                                            class="text-xs text-slate-500 dark:text-slate-400"
                                        >
                                            {{
                                                formatTimestamp(
                                                    notification.created_at,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </Link>
                                <div
                                    v-if="isGuestDonationNotification(notification)"
                                    class="mt-3 space-y-1 text-[11px] text-slate-500 dark:text-slate-400"
                                >
                                    <p>
                                        <strong>Gateway:</strong>
                                        {{ notification.data.payment_gateway ?? 'manual' }}
                                    </p>
                                    <p>
                                        <strong>Status:</strong>
                                        {{ notification.data.payment_status ?? 'pending' }}
                                    </p>
                                    <p v-if="notification.data.payment_reference">
                                        <strong>Reference:</strong>
                                        {{ notification.data.payment_reference }}
                                    </p>
                                    <p v-if="notification.data.branch_name">
                                        <strong>Branch:</strong>
                                        {{ notification.data.branch_name }}
                                    </p>
                                    <p v-if="notification.data.cadence">
                                        <strong>Cadence:</strong>
                                        {{ notification.data.cadence }}
                                        <span
                                            v-if="notification.data.deduction_schedule"
                                            >· {{ notification.data.deduction_schedule }}</span
                                        >
                                    </p>
                                    <p v-if="notification.data.elder_name">
                                        <strong>Elder:</strong> {{
                                            notification.data.elder_name
                                        }}
                                    </p>
                                    <p
                                        v-if="notification.data.elder_funding_goal"
                                        class="text-[11px]"
                                    >
                                        <strong>Goal:</strong>
                                        {{ formatCurrency(
                                            notification.data.elder_funding_goal,
                                        ) }}
                                    </p>
                                    <p
                                        v-if="notification.data.elder_is_funded"
                                        class="text-emerald-600"
                                    >
                                        Campaign fully funded
                                    </p>
                                    <p
                                        v-else-if="notification.data.elder_funding_needed"
                                        class="text-amber-600"
                                    >
                                        Remaining:
                                        {{ formatCurrency(
                                            notification.data.elder_funding_needed,
                                        ) }}
                                    </p>
                                    <p v-if="notification.data.notes">
                                        <strong>Notes:</strong>
                                        {{ notification.data.notes }}
                                    </p>
                                </div>
                                <div
                                    v-if="isGuestDonationNotification(notification)"
                                    class="mt-3 flex flex-wrap gap-2 text-[11px]"
                                >
                                    <Link
                                        v-if="notification.data.receipt_url"
                                        :href="notification.data.receipt_url"
                                        class="rounded-full border border-slate-200 px-3 py-1 text-indigo-600 hover:bg-slate-50"
                                        target="_blank"
                                        rel="noreferrer"
                                    >
                                        View Receipt
                                    </Link>
                                    <Link
                                        v-if="notification.data.mandate_url"
                                        :href="notification.data.mandate_url"
                                        class="rounded-full border border-slate-200 px-3 py-1 text-indigo-600 hover:bg-slate-50"
                                        target="_blank"
                                        rel="noreferrer"
                                    >
                                        Download Mandate
                                    </Link>
                                    <button
                                        v-if="
                                            notification.data.payment_status !==
                                            'confirmed'
                                        "
                                        type="button"
                                        class="rounded-full bg-emerald-600 px-3 py-1 text-[11px] font-semibold text-white shadow hover:bg-emerald-500 disabled:bg-slate-300 disabled:text-slate-500"
                                        :disabled="
                                            confirmingNotificationId === notification.id
                                        "
                                        @click.prevent="
                                            confirmPayment(notification)
                                        "
                                    >
                                        {{
                                            confirmingNotificationId ===
                                            notification.id
                                                ? 'Confirming...'
                                                : 'Confirm Payment'
                                        }}
                                    </button>
                                </div>
                            </div>
                        <button
                            v-if="!notification.read_at"
                            type="button"
                            class="rounded-full bg-slate-200 px-3 py-1 text-xs font-medium text-slate-700 transition hover:bg-slate-300 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                            @click="markAsRead(notification.id)"
                        >
                            Mark as read
                        </button>
                    </div>
                </li>
            </ul>
        </div>

        <div
            v-if="notifications.links.length > 3"
            class="mt-6 flex justify-center"
        >
            <nav class="flex flex-wrap gap-2 text-sm">
                <template
                    v-for="(link, index) in notifications.links"
                    :key="`pagination-${index}`"
                >
                    <span
                        v-if="link.url === null"
                        v-html="link.label"
                        class="rounded-md border border-transparent px-4 py-2 text-slate-400"
                    />
                    <Link
                        v-else
                        :href="link.url"
                        v-html="link.label"
                        class="rounded-md border border-slate-200 px-4 py-2 transition hover:border-indigo-500 hover:text-indigo-600 focus:border-indigo-500 focus:text-indigo-600 dark:border-slate-700 dark:text-slate-300 dark:hover:text-indigo-300"
                        :class="{
                            'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300':
                                link.active,
                        }"
                    />
                </template>
            </nav>
        </div>
    </AppLayout>
</template>
