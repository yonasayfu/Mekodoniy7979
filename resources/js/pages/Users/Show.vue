<script setup lang="ts">
import ActivityTimeline from '@/components/ActivityTimeline.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3'; // Import router
import { ArrowLeft, Edit3, Printer } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'; // Import ref

type ActivityEntry = {
    id: number | string;
    action: string;
    description?: string | null;
    causer?: {
        id: number | string | null;
        name?: string | null;
    } | null;
    changes?: {
        before?: Record<string, unknown> | null;
        after?: Record<string, unknown> | null;
    } | null;
    created_at?: string | null;
    created_at_for_humans?: string | null;
};

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        branch: {
            // Add branch to User prop
            id: number;
            name: string;
        } | null;
        account_status: string;
        account_type: string;
        approved_at: string | null;
        approved_by: string | null;
        roles: string[];
        permissions: string[];
        has_two_factor: boolean;
        staff: {
            id: number;
            full_name: string;
            status: string;
        } | null;
        created_at?: string | null;
        updated_at?: string | null;
        is_kicked: boolean; // Add kick status
        kicked_at: string | null; // Add kick details
        kicked_until: string | null; // Add kick details
        kick_reason: string | null; // Add kick details
    };
    activity: ActivityEntry[];
    breadcrumbs: { title: string; href: string }[];
    print?: boolean;
}>();

const statusLabels: Record<string, string> = {
    pending: 'Pending approval',
    active: 'Active',
    suspended: 'Suspended',
};
const accountTypeLabels: Record<string, string> = {
    internal: 'Internal',
    external: 'External',
};
const statusBadgeClasses: Record<string, string> = {
    pending:
        'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-100',
    active: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-100',
    suspended:
        'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-100',
};

const formatStatus = (status: string): string => statusLabels[status] ?? status;
const formatAccountType = (type: string): string =>
    accountTypeLabels[type] ?? type;
const statusBadgeClass = (status: string): string =>
    `inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ${statusBadgeClasses[status] ?? 'bg-slate-200 text-slate-700 dark:bg-slate-800/40 dark:text-slate-200'}`;

const approvedFormatter = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
});
const formatApprovedAt = (value: string | null): string => {
    if (!value) {
        return '';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return approvedFormatter.format(date);
};

const printMode = computed(() => props.print ?? false);
let printTimer: number | undefined;

const printTimestamp = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
}).format(new Date());

const buildPrintTitle = () => `User Profile - ${props.user.name}`;

const triggerPrint = () => {
    const originalTitle = document.title;
    document.title = buildPrintTitle();
    window.print();
    document.title = originalTitle;
};

const closeAfterPrint = () => {
    if (printMode.value && window.opener && !window.opener.closed) {
        window.close();
    }
};

onMounted(() => {
    if (printMode.value) {
        printTimer = window.setTimeout(() => {
            triggerPrint();
        }, 150);
        window.addEventListener('afterprint', closeAfterPrint);
    }
});

onBeforeUnmount(() => {
    if (printTimer) {
        window.clearTimeout(printTimer);
    }

    window.removeEventListener('afterprint', closeAfterPrint);
});

const printRecord = () => {
    triggerPrint();
};

const showKickModal = ref(false);
const kickForm = useForm({
    reason: '',
    kicked_until: '',
});

const isKicked = computed(() => props.user.is_kicked);

const kickUser = () => {
    kickForm.post(route('users.kick', props.user.id), {
        onSuccess: () => {
            showKickModal.value = false;
            kickForm.reset();
        },
    });
};

const unkickUser = () => {
    router.post(route('users.unkick', props.user.id));
};

const formatKickTime = (timestamp: string | null): string => {
    if (!timestamp) return '-';
    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(timestamp));
};
</script>

<template>
    <Head :title="`User - ${props.user.name}`" />

    <AppLayout :breadcrumbs="props.breadcrumbs">
        <div class="space-y-6">
            <div class="liquidGlass-wrapper print:hidden">
                <span class="liquidGlass-inner-shine" aria-hidden="true" />
                <div
                    class="liquidGlass-content flex flex-col gap-4 px-5 py-5 md:flex-row md:items-center md:justify-between"
                >
                    <div>
                        <h1
                            class="text-2xl font-semibold text-slate-900 dark:text-slate-100"
                        >
                            User profile
                        </h1>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            Access details for {{ props.user.name }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <GlassButton as="span" size="sm" variant="secondary">
                            <Link href="/users" class="flex items-center gap-2">
                                <ArrowLeft class="size-4" />
                                <span>Back to list</span>
                            </Link>
                        </GlassButton>
                        <GlassButton as="span" size="sm" variant="primary">
                            <Link
                                :href="`/users/${props.user.id}/edit`"
                                class="flex items-center gap-2"
                            >
                                <Edit3 class="size-4" />
                                <span>Edit</span>
                            </Link>
                        </GlassButton>
                        <GlassButton
                            size="sm"
                            type="button"
                            class="flex items-center gap-2"
                            variant="warning"
                            @click="printRecord"
                        >
                            <Printer class="size-4" />
                            <span>Print</span>
                        </GlassButton>

                        <template v-if="!isKicked">
                            <GlassButton
                                size="sm"
                                type="button"
                                class="flex items-center gap-2"
                                variant="danger"
                                @click="showKickModal = true"
                            >
                                Kick User
                            </GlassButton>
                        </template>
                        <template v-else>
                            <GlassButton
                                size="sm"
                                type="button"
                                class="flex items-center gap-2"
                                variant="success"
                                @click="unkickUser"
                            >
                                Unkick User
                            </GlassButton>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Kick User Modal -->
            <div
                v-if="showKickModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            >
                <div
                    class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-slate-800"
                >
                    <h2
                        class="text-xl font-semibold text-slate-900 dark:text-slate-100"
                    >
                        Kick User
                    </h2>
                    <form @submit.prevent="kickUser" class="mt-4 space-y-4">
                        <div>
                            <label
                                for="reason"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >Reason for Kick</label
                            >
                            <textarea
                                v-model="kickForm.reason"
                                id="reason"
                                rows="3"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            ></textarea>
                            <InputError
                                :message="kickForm.errors.reason"
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <label
                                for="kicked_until"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >Kick Until (optional)</label
                            >
                            <input
                                v-model="kickForm.kicked_until"
                                id="kicked_until"
                                type="datetime-local"
                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                            />
                            <InputError
                                :message="kickForm.errors.kicked_until"
                                class="mt-2"
                            />
                        </div>
                        <div class="flex justify-end gap-2">
                            <GlassButton
                                type="button"
                                size="sm"
                                variant="secondary"
                                @click="showKickModal = false"
                                >Cancel</GlassButton
                            >
                            <GlassButton
                                type="submit"
                                size="sm"
                                variant="danger"
                                :disabled="kickForm.processing"
                                >Kick User</GlassButton
                            >
                        </div>
                    </form>
                </div>
            </div>

            <div class="hidden text-center text-slate-800 print:block">
                <img
                    src="/images/logo.svg"
                    alt="Logo"
                    class="print-logo mx-auto mb-3 h-12 w-auto"
                />
                <h1 class="text-xl font-semibold">{{ $page.props.name }}</h1>
                <p class="text-sm">User Profile: {{ props.user.name }}</p>
                <p class="text-xs text-slate-500">
                    Printed {{ printTimestamp }}
                </p>
                <hr class="print-divider" />
            </div>

            <GlassCard
                padding="p-0"
                class="print:border print:bg-white print:shadow-none"
            >
                <div
                    class="grid gap-4 border border-slate-200/70 bg-white/80 p-6 text-sm shadow-sm md:grid-cols-2 dark:border-slate-800/60 dark:bg-slate-900/60 print:border print:bg-white"
                >
                    <div class="space-y-2">
                        <p
                            class="text-xs font-semibold tracking-wide text-slate-400 uppercase dark:text-slate-500"
                        >
                            Account
                        </p>
                        <div
                            class="space-y-2 rounded-lg border border-slate-200/60 bg-white/70 p-4 dark:border-slate-800/50 dark:bg-slate-900/70"
                        >
                            <div>
                                <p
                                    class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                >
                                    Name
                                </p>
                                <p
                                    class="font-medium text-slate-900 dark:text-slate-100"
                                >
                                    {{ props.user.name }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                >
                                    Email
                                </p>
                                <p
                                    class="font-medium text-slate-900 dark:text-slate-100"
                                >
                                    {{ props.user.email }}
                                </p>
                            </div>
                            <div v-if="props.user.branch">
                                <p
                                    class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                >
                                    Branch
                                </p>
                                <p
                                    class="font-medium text-slate-900 dark:text-slate-100"
                                >
                                    {{ props.user.branch.name }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                >
                                    Two-factor authentication
                                </p>
                                <p
                                    class="font-medium text-slate-900 dark:text-slate-100"
                                >
                                    {{
                                        props.user.has_two_factor
                                            ? 'Enabled'
                                            : 'Disabled'
                                    }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                >
                                    Account status
                                </p>
                                <div
                                    class="mt-1 flex flex-wrap items-center gap-2"
                                >
                                    <span
                                        :class="
                                            statusBadgeClass(
                                                props.user.account_status,
                                            )
                                        "
                                    >
                                        {{
                                            formatStatus(
                                                props.user.account_status,
                                            )
                                        }}
                                    </span>
                                    <span
                                        class="text-xs text-slate-500 dark:text-slate-400"
                                    >
                                        {{
                                            formatAccountType(
                                                props.user.account_type,
                                            )
                                        }}
                                    </span>
                                </div>
                                <p
                                    v-if="props.user.approved_at"
                                    class="mt-1 text-xs text-slate-400 dark:text-slate-500"
                                >
                                    Approved
                                    {{
                                        formatApprovedAt(props.user.approved_at)
                                    }}
                                    <template v-if="props.user.approved_by">
                                        by {{ props.user.approved_by }}
                                    </template>
                                </p>
                                <p
                                    v-else
                                    class="mt-1 text-xs text-slate-400 italic dark:text-slate-500"
                                >
                                    Awaiting approval
                                </p>
                            </div>
                            <div v-if="isKicked" class="mt-4">
                                <p
                                    class="text-xs tracking-wide text-red-400 uppercase dark:text-red-500"
                                >
                                    Kicked Status
                                </p>
                                <div
                                    class="rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-200"
                                >
                                    <p class="font-medium">
                                        Kicked since:
                                        {{
                                            formatKickTime(props.user.kicked_at)
                                        }}
                                    </p>
                                    <p v-if="props.user.kicked_until">
                                        Until:
                                        {{
                                            formatKickTime(
                                                props.user.kicked_until,
                                            )
                                        }}
                                    </p>
                                    <p v-else>Duration: Indefinite</p>
                                    <p class="mt-2 text-xs">
                                        Reason: {{ props.user.kick_reason }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="grid grid-cols-2 gap-3 text-xs text-slate-500 dark:text-slate-400"
                            >
                                <div>
                                    <p class="tracking-wide uppercase">
                                        Created
                                    </p>
                                    <p
                                        class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100"
                                    >
                                        {{ props.user.created_at ?? '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="tracking-wide uppercase">
                                        Updated
                                    </p>
                                    <p
                                        class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100"
                                    >
                                        {{ props.user.updated_at ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <p
                            class="text-xs font-semibold tracking-wide text-slate-400 uppercase dark:text-slate-500"
                        >
                            Roles & permissions
                        </p>
                        <div
                            class="space-y-4 rounded-lg border border-slate-200/60 bg-white/70 p-4 dark:border-slate-800/50 dark:bg-slate-900/70"
                        >
                            <div>
                                <p
                                    class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                >
                                    Roles
                                </p>
                                <div
                                    v-if="props.user.roles.length"
                                    class="mt-2 flex flex-wrap gap-2"
                                >
                                    <span
                                        v-for="role in props.user.roles"
                                        :key="`role-${role}`"
                                        class="inline-flex items-center rounded-full bg-indigo-100/80 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200"
                                    >
                                        {{ role }}
                                    </span>
                                </div>
                                <p
                                    v-else
                                    class="font-medium text-slate-500 dark:text-slate-400"
                                >
                                    No roles assigned
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                >
                                    Direct permissions
                                </p>
                                <div
                                    v-if="props.user.permissions.length"
                                    class="mt-2 flex flex-wrap gap-2"
                                >
                                    <span
                                        v-for="permission in props.user
                                            .permissions"
                                        :key="`permission-${permission}`"
                                        class="inline-flex items-center rounded-full bg-emerald-100/80 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200"
                                    >
                                        {{ permission }}
                                    </span>
                                </div>
                                <p
                                    v-else
                                    class="font-medium text-slate-500 dark:text-slate-400"
                                >
                                    Inherited from roles
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <p
                            class="text-xs font-semibold tracking-wide text-slate-400 uppercase dark:text-slate-500"
                        >
                            Staff linkage
                        </p>
                        <div
                            class="rounded-lg border border-slate-200/60 bg-white/70 p-4 text-sm shadow-sm dark:border-slate-800/50 dark:bg-slate-900/70"
                        >
                            <template v-if="props.user.staff">
                                <p
                                    class="font-medium text-slate-900 dark:text-slate-100"
                                >
                                    {{ props.user.staff.full_name }}
                                </p>
                                <p
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
                                    Status: {{ props.user.staff.status }}
                                </p>
                            </template>
                            <p
                                v-else
                                class="font-medium text-slate-500 dark:text-slate-400"
                            >
                                No staff member linked
                            </p>
                        </div>
                    </div>
                </div>
            </GlassCard>

            <GlassCard
                v-if="props.activity.length"
                variant="lite"
                content-class="space-y-4"
                :disable-shine="true"
                class="print:border print:bg-white print:shadow-none"
            >
                <div>
                    <h2
                        class="text-sm font-semibold text-slate-900 dark:text-slate-100"
                    >
                        Recent activity
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Audit trail for this account.
                    </p>
                </div>
                <ActivityTimeline :entries="props.activity" />
            </GlassCard>
        </div>
    </AppLayout>
</template>
