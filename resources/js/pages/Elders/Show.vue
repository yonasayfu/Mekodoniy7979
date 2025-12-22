<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ActivityTimeline from '@/components/ActivityTimeline.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import { confirmDialog } from '@/lib/confirm';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Edit3, Plus, Printer, Trash2 } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

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
    elder: {
        id: number;
        branch_id: number;
        branch: {
            id: number;
            name: string;
        };
        first_name: string;
        last_name: string;
        full_name: string;
        date_of_birth: string | null;
        gender: string | null;
        address: string | null;
        city: string | null;
        country: string | null;
        phone: string | null;
        bio: string | null;
        profile_picture_path: string | null;
        priority_level: 'low' | 'medium' | 'high';
        health_status: string | null;
        special_needs: string | null;
        monthly_expenses: number | null;
        video_url: string | null;
        current_status?: string | null;
        admitted_at?: string | null;
        deceased_at?: string | null;
    };
    activity: ActivityEntry[];
    statusEvents: Array<{
        id: number;
        from_status: string;
        to_status: string;
        reason: string | null;
        occurred_at: string;
        creator?: { id: number; name: string } | null;
        created_at?: string;
    }>;
    healthAssessments: Array<{
        id: number;
        assessment_date: string;
        summary: string;
        mobility_level: string | null;
        risk_level: string | null;
        creator?: { id: number; name: string } | null;
        created_at?: string;
    }>;
    medicalConditions: Array<{
        id: number;
        condition_name: string;
        diagnosed_at: string | null;
        status: string;
        notes: string | null;
        created_at?: string;
    }>;
    medications: Array<{
        id: number;
        medication_name: string;
        dosage: string | null;
        frequency: string | null;
        started_at: string | null;
        ended_at: string | null;
        notes: string | null;
        created_at?: string;
    }>;
    breadcrumbs: { title: string; href: string }[];
    print?: boolean;
}>();

const lifecycleForm = useForm({
    to_status: (props.elder.current_status ?? 'available') as string,
    reason: '',
    occurred_at: '',
});

const submitLifecycle = () => {
    lifecycleForm.post(route('elders.lifecycle.status', props.elder.id), {
        preserveScroll: true,
        onSuccess: () => {
            lifecycleForm.reset('reason', 'occurred_at');
        },
    });
};

const assessmentEditingId = ref<number | null>(null);
const assessmentForm = useForm({
    assessment_date: '',
    summary: '',
    mobility_level: '',
    risk_level: '',
});

const startEditAssessment = (assessment: (typeof props.healthAssessments)[number]) => {
    assessmentEditingId.value = assessment.id;
    assessmentForm.assessment_date = assessment.assessment_date;
    assessmentForm.summary = assessment.summary;
    assessmentForm.mobility_level = assessment.mobility_level ?? '';
    assessmentForm.risk_level = assessment.risk_level ?? '';
};

const resetAssessmentForm = () => {
    assessmentEditingId.value = null;
    assessmentForm.reset();
};

const submitAssessment = () => {
    if (assessmentEditingId.value) {
        assessmentForm.put(
            route('elders.health-assessments.update', {
                elder: props.elder.id,
                assessment: assessmentEditingId.value,
            }),
            {
                preserveScroll: true,
                onSuccess: () => resetAssessmentForm(),
            },
        );
        return;
    }

    assessmentForm.post(route('elders.health-assessments.store', { elder: props.elder.id }), {
        preserveScroll: true,
        onSuccess: () => resetAssessmentForm(),
    });
};

const destroyAssessment = async (id: number) => {
    const accepted = await confirmDialog({
        title: 'Delete assessment?',
        message: 'This will permanently remove the assessment entry.',
        confirmText: 'Delete',
        cancelText: 'Cancel',
    });

    if (!accepted) {
        return;
    }

    router.delete(
        route('elders.health-assessments.destroy', {
            elder: props.elder.id,
            assessment: id,
        }),
        { preserveScroll: true },
    );
};

const conditionEditingId = ref<number | null>(null);
const conditionForm = useForm({
    condition_name: '',
    diagnosed_at: '',
    status: 'active',
    notes: '',
});

const startEditCondition = (condition: (typeof props.medicalConditions)[number]) => {
    conditionEditingId.value = condition.id;
    conditionForm.condition_name = condition.condition_name;
    conditionForm.diagnosed_at = condition.diagnosed_at ?? '';
    conditionForm.status = condition.status;
    conditionForm.notes = condition.notes ?? '';
};

const resetConditionForm = () => {
    conditionEditingId.value = null;
    conditionForm.reset();
};

const submitCondition = () => {
    if (conditionEditingId.value) {
        conditionForm.put(
            route('elders.medical-conditions.update', {
                elder: props.elder.id,
                condition: conditionEditingId.value,
            }),
            {
                preserveScroll: true,
                onSuccess: () => resetConditionForm(),
            },
        );
        return;
    }

    conditionForm.post(route('elders.medical-conditions.store', { elder: props.elder.id }), {
        preserveScroll: true,
        onSuccess: () => resetConditionForm(),
    });
};

const destroyCondition = async (id: number) => {
    const accepted = await confirmDialog({
        title: 'Delete medical condition?',
        message: 'This will permanently remove the medical condition entry.',
        confirmText: 'Delete',
        cancelText: 'Cancel',
    });

    if (!accepted) {
        return;
    }

    router.delete(
        route('elders.medical-conditions.destroy', {
            elder: props.elder.id,
            condition: id,
        }),
        { preserveScroll: true },
    );
};

const medicationEditingId = ref<number | null>(null);
const medicationForm = useForm({
    medication_name: '',
    dosage: '',
    frequency: '',
    started_at: '',
    ended_at: '',
    notes: '',
});

const startEditMedication = (medication: (typeof props.medications)[number]) => {
    medicationEditingId.value = medication.id;
    medicationForm.medication_name = medication.medication_name;
    medicationForm.dosage = medication.dosage ?? '';
    medicationForm.frequency = medication.frequency ?? '';
    medicationForm.started_at = medication.started_at ?? '';
    medicationForm.ended_at = medication.ended_at ?? '';
    medicationForm.notes = medication.notes ?? '';
};

const resetMedicationForm = () => {
    medicationEditingId.value = null;
    medicationForm.reset();
};

const submitMedication = () => {
    if (medicationEditingId.value) {
        medicationForm.put(
            route('elders.medications.update', {
                elder: props.elder.id,
                medication: medicationEditingId.value,
            }),
            {
                preserveScroll: true,
                onSuccess: () => resetMedicationForm(),
            },
        );
        return;
    }

    medicationForm.post(route('elders.medications.store', { elder: props.elder.id }), {
        preserveScroll: true,
        onSuccess: () => resetMedicationForm(),
    });
};

const destroyMedication = async (id: number) => {
    const accepted = await confirmDialog({
        title: 'Delete medication?',
        message: 'This will permanently remove the medication entry.',
        confirmText: 'Delete',
        cancelText: 'Cancel',
    });

    if (!accepted) {
        return;
    }

    router.delete(
        route('elders.medications.destroy', {
            elder: props.elder.id,
            medication: id,
        }),
        { preserveScroll: true },
    );
};

const priorityBadgeClass = computed(() => {
    switch (props.elder.priority_level) {
        case 'high':
            return 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-200';
        case 'medium':
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-200';
        case 'low':
            return 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-200';
        default:
            return 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300';
    }
});

const printMode = computed(() => props.print ?? false);
let printTimer: number | undefined;

const printTimestamp = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
}).format(new Date());

const buildPrintTitle = () => `Elder Profile - ${props.elder.first_name} ${props.elder.last_name}`;

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
</script>

<template>
    <Head :title="`Elder - ${props.elder.first_name} ${props.elder.last_name}`" />

    <AppLayout :breadcrumbs="props.breadcrumbs">
        <div class="space-y-6">
            <div class="liquidGlass-wrapper print:hidden">
                <span class="liquidGlass-inner-shine" aria-hidden="true" />
                <div class="liquidGlass-content flex flex-col gap-4 px-5 py-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                            Elder profile
                        </h1>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            Detailed view for {{ props.elder.first_name }} {{ props.elder.last_name }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <GlassButton as="span" size="sm" variant="secondary">
                            <Link :href="route('elders.index')" class="flex items-center gap-2">
                                <ArrowLeft class="size-4" />
                                <span>Back to list</span>
                            </Link>
                        </GlassButton>

                        <GlassButton as="span" size="sm" variant="primary">
                            <Link :href="route('elders.edit', props.elder.id)" class="flex items-center gap-2">
                                <Edit3 class="size-4" />
                                <span>Edit</span>
                            </Link>
                        </GlassButton>

                        <GlassButton size="sm" type="button" class="flex items-center gap-2" variant="warning" @click="printRecord">
                            <Printer class="size-4" />
                            <span>Print</span>
                        </GlassButton>
                    </div>
                </div>
            </div>

            <div class="hidden print:block text-center text-slate-800">
                <img src="/images/logo.svg" alt="Logo" class="mx-auto mb-3 h-12 w-auto print-logo" />
                <h1 class="text-xl font-semibold">{{ $page.props.name }}</h1>
                <p class="text-sm">Elder Profile: {{ props.elder.first_name }} {{ props.elder.last_name }}</p>
                <p class="text-xs text-slate-500">Printed {{ printTimestamp }}</p>
                <hr class="print-divider" />
            </div>

            <GlassCard padding="p-0" class="print:shadow-none print:bg-white print:border">
                <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 dark:border-slate-800/60 dark:bg-slate-900/60 print:border print:bg-white">
                    <div class="flex flex-col gap-6 p-6 md:flex-row md:items-start">
                        <div class="flex flex-col items-center gap-3 md:w-1/4">
                            <div class="relative flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-white shadow-md dark:border-slate-700 dark:bg-slate-950">
                                <img
                                    v-if="elder.profile_picture_path"
                                    :src="`/storage/${elder.profile_picture_path}`"
                                    :alt="elder.first_name + ' ' + elder.last_name"
                                    class="h-full w-full object-cover"
                                />
                                <span v-else class="text-3xl font-semibold text-slate-500 dark:text-slate-400">
                                    {{ elder.first_name.charAt(0) }}{{ elder.last_name.charAt(0) }}
                                </span>
                            </div>
                            <div class="text-center">
                                <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                    {{ props.elder.first_name }} {{ props.elder.last_name }}
                                </p>
                                <span
                                    class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="priorityBadgeClass"
                                >
                                    {{ props.elder.priority_level }}
                                </span>
                            </div>
                        </div>

                        <div class="grid flex-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                    General Information
                                </p>
                                <div class="space-y-2 rounded-lg border border-slate-200/70 bg-white/70 p-4 text-sm shadow-sm dark:border-slate-800/50 dark:bg-slate-900/60">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Branch</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.branch.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Date of Birth</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.date_of_birth ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Gender</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.gender ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Address</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.address ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">City, Country</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.city ?? '-' }}, {{ props.elder.country ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Phone</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.phone ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Bio</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.bio ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                    Health & Financial
                                </p>
                                <div class="space-y-2 rounded-lg border border-slate-200/70 bg-white/70 p-4 text-sm shadow-sm dark:border-slate-800/50 dark:bg-slate-900/60">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Health Status</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.health_status ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Special Needs</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.special_needs ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Monthly Expenses</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.monthly_expenses ?? '-' }} ETB</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Video</p>
                                        <div v-if="props.elder.video_url" class="mt-1">
                                            <video controls class="w-full rounded-lg" :src="`/storage/${props.elder.video_url}`"></video>
                                        </div>
                                        <p v-else class="font-medium text-slate-900 dark:text-slate-100">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </GlassCard>

            <GlassCard padding="p-0" class="print:shadow-none print:bg-white print:border">
                <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 p-6 dark:border-slate-800/60 dark:bg-slate-900/60 print:border print:bg-white">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-4">
                            <div>
                                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Lifecycle</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Update the elder status and keep an audit trail.</p>
                            </div>

                            <div class="rounded-lg border border-slate-200/70 bg-white/70 p-4 text-sm shadow-sm dark:border-slate-800/50 dark:bg-slate-900/60">
                                <div class="grid gap-3 md:grid-cols-2">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Current status</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.current_status ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Admitted at</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.admitted_at ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">Deceased at</p>
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ props.elder.deceased_at ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <form class="space-y-4" @submit.prevent="submitLifecycle">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Change to</label>
                                    <select
                                        v-model="lifecycleForm.to_status"
                                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                    >
                                        <option value="available">Available</option>
                                        <option value="admitted">Admitted</option>
                                        <option value="sponsored">Sponsored</option>
                                        <option value="transferred">Transferred</option>
                                        <option value="deceased">Deceased</option>
                                    </select>
                                    <InputError :message="lifecycleForm.errors.to_status" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Occurred at (optional)</label>
                                    <input
                                        v-model="lifecycleForm.occurred_at"
                                        type="datetime-local"
                                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                    />
                                    <InputError :message="lifecycleForm.errors.occurred_at" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Reason (optional)</label>
                                    <textarea
                                        v-model="lifecycleForm.reason"
                                        rows="3"
                                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                    ></textarea>
                                    <InputError :message="lifecycleForm.errors.reason" class="mt-2" />
                                </div>

                                <div class="flex justify-end">
                                    <GlassButton size="sm" type="submit" :disabled="lifecycleForm.processing" variant="primary" class="flex items-center gap-2">
                                        <Plus class="size-4" />
                                        <span>Update status</span>
                                    </GlassButton>
                                </div>
                            </form>

                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">Recent status events</p>
                                <div v-if="props.statusEvents.length" class="space-y-2">
                                    <div
                                        v-for="event in props.statusEvents"
                                        :key="event.id"
                                        class="rounded-lg border border-slate-200/70 bg-white/70 p-3 text-sm dark:border-slate-800/50 dark:bg-slate-900/60"
                                    >
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="font-medium text-slate-900 dark:text-slate-100">
                                                    {{ event.from_status }} → {{ event.to_status }}
                                                </p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                                    {{ event.occurred_at }}
                                                    <template v-if="event.creator?.name"> · by {{ event.creator.name }}</template>
                                                </p>
                                                <p v-if="event.reason" class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                                                    {{ event.reason }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p v-else class="text-sm text-slate-500 dark:text-slate-400">No status changes recorded.</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="space-y-4">
                                <div>
                                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Health assessments</h2>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Track periodic assessments.</p>
                                </div>

                                <form class="space-y-4" @submit.prevent="submitAssessment">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Assessment date</label>
                                        <input
                                            v-model="assessmentForm.assessment_date"
                                            type="date"
                                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        />
                                        <InputError :message="assessmentForm.errors.assessment_date" class="mt-2" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Summary</label>
                                        <textarea
                                            v-model="assessmentForm.summary"
                                            rows="3"
                                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        ></textarea>
                                        <InputError :message="assessmentForm.errors.summary" class="mt-2" />
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Mobility level</label>
                                            <input
                                                v-model="assessmentForm.mobility_level"
                                                type="text"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError :message="assessmentForm.errors.mobility_level" class="mt-2" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Risk level</label>
                                            <input
                                                v-model="assessmentForm.risk_level"
                                                type="text"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError :message="assessmentForm.errors.risk_level" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-2">
                                        <GlassButton v-if="assessmentEditingId" size="sm" variant="secondary" type="button" @click="resetAssessmentForm">Cancel</GlassButton>
                                        <GlassButton size="sm" type="submit" :disabled="assessmentForm.processing" variant="primary" class="flex items-center gap-2">
                                            <Plus class="size-4" />
                                            <span>{{ assessmentEditingId ? 'Save changes' : 'Add assessment' }}</span>
                                        </GlassButton>
                                    </div>
                                </form>

                                <div class="space-y-2">
                                    <div v-if="props.healthAssessments.length" class="space-y-2">
                                        <div
                                            v-for="assessment in props.healthAssessments"
                                            :key="assessment.id"
                                            class="rounded-lg border border-slate-200/70 bg-white/70 p-3 text-sm dark:border-slate-800/50 dark:bg-slate-900/60"
                                        >
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="font-medium text-slate-900 dark:text-slate-100">
                                                        {{ assessment.assessment_date }}
                                                    </p>
                                                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ assessment.summary }}</p>
                                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                                        <span v-if="assessment.mobility_level">Mobility: {{ assessment.mobility_level }}</span>
                                                        <span v-if="assessment.risk_level"> · Risk: {{ assessment.risk_level }}</span>
                                                        <template v-if="assessment.creator?.name"> · by {{ assessment.creator.name }}</template>
                                                    </p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button
                                                        type="button"
                                                        class="text-xs font-medium text-indigo-700 hover:underline dark:text-indigo-200"
                                                        @click="startEditAssessment(assessment)"
                                                    >
                                                        Edit
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center gap-1 text-xs font-medium text-red-700 hover:underline dark:text-red-200"
                                                        @click="destroyAssessment(assessment.id)"
                                                    >
                                                        <Trash2 class="size-4" />
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p v-else class="text-sm text-slate-500 dark:text-slate-400">No assessments recorded.</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Medical conditions</h2>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Maintain active/inactive conditions.</p>
                                </div>

                                <form class="space-y-4" @submit.prevent="submitCondition">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Condition name</label>
                                        <input
                                            v-model="conditionForm.condition_name"
                                            type="text"
                                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        />
                                        <InputError :message="conditionForm.errors.condition_name" class="mt-2" />
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Diagnosed at</label>
                                            <input
                                                v-model="conditionForm.diagnosed_at"
                                                type="date"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError :message="conditionForm.errors.diagnosed_at" class="mt-2" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Status</label>
                                            <select
                                                v-model="conditionForm.status"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            >
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="resolved">Resolved</option>
                                            </select>
                                            <InputError :message="conditionForm.errors.status" class="mt-2" />
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Notes</label>
                                        <textarea
                                            v-model="conditionForm.notes"
                                            rows="3"
                                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        ></textarea>
                                        <InputError :message="conditionForm.errors.notes" class="mt-2" />
                                    </div>

                                    <div class="flex items-center justify-end gap-2">
                                        <GlassButton v-if="conditionEditingId" size="sm" variant="secondary" type="button" @click="resetConditionForm">Cancel</GlassButton>
                                        <GlassButton size="sm" type="submit" :disabled="conditionForm.processing" variant="primary" class="flex items-center gap-2">
                                            <Plus class="size-4" />
                                            <span>{{ conditionEditingId ? 'Save changes' : 'Add condition' }}</span>
                                        </GlassButton>
                                    </div>
                                </form>

                                <div class="space-y-2">
                                    <div v-if="props.medicalConditions.length" class="space-y-2">
                                        <div
                                            v-for="condition in props.medicalConditions"
                                            :key="condition.id"
                                            class="rounded-lg border border-slate-200/70 bg-white/70 p-3 text-sm dark:border-slate-800/50 dark:bg-slate-900/60"
                                        >
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="font-medium text-slate-900 dark:text-slate-100">
                                                        {{ condition.condition_name }}
                                                    </p>
                                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                                        Status: {{ condition.status }}
                                                        <span v-if="condition.diagnosed_at"> · Diagnosed: {{ condition.diagnosed_at }}</span>
                                                    </p>
                                                    <p v-if="condition.notes" class="mt-1 text-xs text-slate-600 dark:text-slate-300">{{ condition.notes }}</p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button
                                                        type="button"
                                                        class="text-xs font-medium text-indigo-700 hover:underline dark:text-indigo-200"
                                                        @click="startEditCondition(condition)"
                                                    >
                                                        Edit
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center gap-1 text-xs font-medium text-red-700 hover:underline dark:text-red-200"
                                                        @click="destroyCondition(condition.id)"
                                                    >
                                                        <Trash2 class="size-4" />
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p v-else class="text-sm text-slate-500 dark:text-slate-400">No conditions recorded.</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Medications</h2>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Track active and historical medications.</p>
                                </div>

                                <form class="space-y-4" @submit.prevent="submitMedication">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Medication name</label>
                                        <input
                                            v-model="medicationForm.medication_name"
                                            type="text"
                                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        />
                                        <InputError :message="medicationForm.errors.medication_name" class="mt-2" />
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Dosage</label>
                                            <input
                                                v-model="medicationForm.dosage"
                                                type="text"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError :message="medicationForm.errors.dosage" class="mt-2" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Frequency</label>
                                            <input
                                                v-model="medicationForm.frequency"
                                                type="text"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError :message="medicationForm.errors.frequency" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Started at</label>
                                            <input
                                                v-model="medicationForm.started_at"
                                                type="date"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError :message="medicationForm.errors.started_at" class="mt-2" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Ended at</label>
                                            <input
                                                v-model="medicationForm.ended_at"
                                                type="date"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError :message="medicationForm.errors.ended_at" class="mt-2" />
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Notes</label>
                                        <textarea
                                            v-model="medicationForm.notes"
                                            rows="3"
                                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        ></textarea>
                                        <InputError :message="medicationForm.errors.notes" class="mt-2" />
                                    </div>

                                    <div class="flex items-center justify-end gap-2">
                                        <GlassButton v-if="medicationEditingId" size="sm" variant="secondary" type="button" @click="resetMedicationForm">Cancel</GlassButton>
                                        <GlassButton size="sm" type="submit" :disabled="medicationForm.processing" variant="primary" class="flex items-center gap-2">
                                            <Plus class="size-4" />
                                            <span>{{ medicationEditingId ? 'Save changes' : 'Add medication' }}</span>
                                        </GlassButton>
                                    </div>
                                </form>

                                <div class="space-y-2">
                                    <div v-if="props.medications.length" class="space-y-2">
                                        <div
                                            v-for="medication in props.medications"
                                            :key="medication.id"
                                            class="rounded-lg border border-slate-200/70 bg-white/70 p-3 text-sm dark:border-slate-800/50 dark:bg-slate-900/60"
                                        >
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="font-medium text-slate-900 dark:text-slate-100">
                                                        {{ medication.medication_name }}
                                                    </p>
                                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                                        <span v-if="medication.dosage">Dosage: {{ medication.dosage }}</span>
                                                        <span v-if="medication.frequency"> · Frequency: {{ medication.frequency }}</span>
                                                    </p>
                                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                                        <span v-if="medication.started_at">Start: {{ medication.started_at }}</span>
                                                        <span v-if="medication.ended_at"> · End: {{ medication.ended_at }}</span>
                                                    </p>
                                                    <p v-if="medication.notes" class="mt-1 text-xs text-slate-600 dark:text-slate-300">{{ medication.notes }}</p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button
                                                        type="button"
                                                        class="text-xs font-medium text-indigo-700 hover:underline dark:text-indigo-200"
                                                        @click="startEditMedication(medication)"
                                                    >
                                                        Edit
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center gap-1 text-xs font-medium text-red-700 hover:underline dark:text-red-200"
                                                        @click="destroyMedication(medication.id)"
                                                    >
                                                        <Trash2 class="size-4" />
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p v-else class="text-sm text-slate-500 dark:text-slate-400">No medications recorded.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </GlassCard>

            <GlassCard
                v-if="props.activity.length"
                variant="lite"
                content-class="space-y-4"
                :disable-shine="true"
                class="print:shadow-none print:bg-white print:border"
            >
                <div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        Recent activity
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Audit trail for updates to this elder profile.
                    </p>
                </div>
                <ActivityTimeline :entries="props.activity" />
            </GlassCard>
        </div>
    </AppLayout>
</template>

<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 1.5cm;
    }

    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        background-color: #ffffff !important;
        color: #0f172a !important;
    }

    .print-logo {
        max-height: 48px;
    }

    .print-divider {
        border: 0;
        border-top: 1px solid #cbd5f5;
        margin: 1rem auto 1.5rem;
        width: 100%;
    }

    .liquidGlass-wrapper,
    .liquidGlass-content {
        background: #ffffff !important;
        box-shadow: none !important;
    }

    .liquidGlass-inner-shine {
        display: none !important;
    }

    .rounded-lg,
    .rounded-xl {
        background: #ffffff !important;
        box-shadow: none !important;
        border-color: #e2e8f0 !important;
    }

    .text-slate-500,
    .text-slate-400,
    .text-slate-600 {
        color: #334155 !important;
    }
}
</style>
