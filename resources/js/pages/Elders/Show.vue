<script setup lang="ts">
import ActivityTimeline from '@/components/ActivityTimeline.vue';
import CaseNotes from '@/Components/CaseNotes/CaseNotes.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
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
        profile_photo_url: string | null;
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
    caseNotes: {
        data: Array<{
            id: number;
            content: string;
            visibility: string;
            created_at: string;
            author: {
                id: number;
                name: string;
                email: string;
            };
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    documents: Array<{
        id: number;
        type: string;
        label: string | null;
        file_name: string;
        mime_type: string | null;
        uploaded_at: string | null;
        uploader: { id: number; name: string } | null;
        download_url: string;
    }>;
    proposals: Array<{
        id: number;
        donor: { id: number; name: string; email: string };
        proposer: { id: number; name: string } | null;
        amount: number;
        frequency: string;
        relationship_type: string | null;
        notes: string | null;
        status: string;
        expires_at: string | null;
        responded_at: string | null;
    }>;
    donors: Array<{
        id: number;
        name: string;
        email: string;
    }>;
    can: {
        create_case_notes: boolean;
        update_case_notes: boolean;
        delete_case_notes: boolean;
        propose_match?: boolean;
        manage_documents?: boolean;
    };
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

const startEditAssessment = (
    assessment: (typeof props.healthAssessments)[number],
) => {
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

    assessmentForm.post(
        route('elders.health-assessments.store', { elder: props.elder.id }),
        {
            preserveScroll: true,
            onSuccess: () => resetAssessmentForm(),
        },
    );
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

const startEditCondition = (
    condition: (typeof props.medicalConditions)[number],
) => {
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

    conditionForm.post(
        route('elders.medical-conditions.store', { elder: props.elder.id }),
        {
            preserveScroll: true,
            onSuccess: () => resetConditionForm(),
        },
    );
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

const startEditMedication = (
    medication: (typeof props.medications)[number],
) => {
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

    medicationForm.post(
        route('elders.medications.store', { elder: props.elder.id }),
        {
            preserveScroll: true,
            onSuccess: () => resetMedicationForm(),
        },
    );
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

const statusTimelineEntries = computed(() =>
    props.statusEvents.map((event) => ({
        id: event.id,
        action: `${event.from_status} → ${event.to_status}`,
        description:
            event.reason ??
            `${event.from_status} → ${event.to_status}`,
        created_at: event.occurred_at,
        created_at_for_humans: event.created_at ?? event.occurred_at,
        causer: event.creator
            ? { id: event.creator.id, name: event.creator.name }
            : null,
    })),
);

const documentFileInput = ref<HTMLInputElement | null>(null);
const documentForm = useForm({
    type: 'consent',
    label: '',
    file: null as File | null,
});
const documentTypes = [
    { label: 'Consent Form', value: 'consent' },
    { label: 'Medical Report', value: 'medical_report' },
    { label: 'ID Document', value: 'id_document' },
    { label: 'Other', value: 'other' },
];

const handleDocumentFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    documentForm.file = target.files?.[0] ?? null;
};

const resetDocumentForm = () => {
    documentForm.reset();
    if (documentFileInput.value) {
        documentFileInput.value.value = '';
    }
};

const submitDocument = () => {
    if (!documentForm.file) {
        documentForm.setError('file', 'Please select a file to upload.');
        return;
    }

    documentForm.post(
        route('elders.documents.store', { elder: props.elder.id }),
        {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => resetDocumentForm(),
        },
    );
};

const destroyDocument = async (id: number) => {
    const accepted = await confirmDialog({
        title: 'Remove document?',
        message: 'This will delete the file permanently.',
        confirmText: 'Delete',
        cancelText: 'Cancel',
    });

    if (!accepted) {
        return;
    }

    router.delete(
        route('elders.documents.destroy', {
            elder: props.elder.id,
            document: id,
        }),
        { preserveScroll: true },
    );
};

const proposalForm = useForm({
    donor_id: '',
    amount: '',
    frequency: 'monthly',
    relationship_type: '',
    notes: '',
    expires_in_hours: 72,
});

const submitProposal = () => {
    proposalForm.post(
        route('elders.proposals.store', { elder: props.elder.id }),
        {
            preserveScroll: true,
            onSuccess: () => proposalForm.reset(),
        },
    );
};

const cancelProposal = async (id: number) => {
    const accepted = await confirmDialog({
        title: 'Cancel proposal?',
        message:
            'This will withdraw the proposal from the donor and mark it as cancelled.',
        confirmText: 'Cancel proposal',
        cancelText: 'Keep',
    });

    if (!accepted) {
        return;
    }

    router.delete(
        route('elders.proposals.cancel', {
            elder: props.elder.id,
            proposal: id,
        }),
        { preserveScroll: true },
    );
};

const proposalStatusClass = (status: string) => {
    switch (status) {
        case 'accepted':
            return 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-200';
        case 'pending':
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-200';
        case 'declined':
        case 'cancelled':
            return 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-200';
        case 'expired':
            return 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300';
        default:
            return 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300';
    }
};

const printMode = computed(() => props.print ?? false);
let printTimer: number | undefined;

const printTimestamp = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
}).format(new Date());

const buildPrintTitle = () =>
    `Elder Profile - ${props.elder.first_name} ${props.elder.last_name}`;

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
    <Head
        :title="`Elder - ${props.elder.first_name} ${props.elder.last_name}`"
    />

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
                            Elder profile
                        </h1>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            Detailed view for {{ props.elder.first_name }}
                            {{ props.elder.last_name }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <GlassButton as="span" size="sm" variant="secondary">
                            <Link
                                :href="route('elders.index')"
                                class="flex items-center gap-2"
                            >
                                <ArrowLeft class="size-4" />
                                <span>Back to list</span>
                            </Link>
                        </GlassButton>

                        <GlassButton as="span" size="sm" variant="primary">
                            <Link
                                :href="route('elders.edit', props.elder.id)"
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
                    </div>
                </div>
            </div>

            <div class="hidden text-center text-slate-800 print:block">
                <img
                    src="/images/logo.svg"
                    alt="Logo"
                    class="print-logo mx-auto mb-3 h-12 w-auto"
                />
                <h1 class="text-xl font-semibold">{{ $page.props.name }}</h1>
                <p class="text-sm">
                    Elder Profile: {{ props.elder.first_name }}
                    {{ props.elder.last_name }}
                </p>
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
                    class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 dark:border-slate-800/60 dark:bg-slate-900/60 print:border print:bg-white"
                >
                    <div
                        class="flex flex-col gap-6 p-6 md:flex-row md:items-start"
                    >
                        <div class="flex flex-col items-center gap-3 md:w-1/4">
                            <div
                                class="relative flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-white shadow-md dark:border-slate-700 dark:bg-slate-950"
                            >
                                <img
                                    v-if="elder.profile_photo_url"
                                    :src="elder.profile_photo_url"
                                    :alt="
                                        elder.first_name + ' ' + elder.last_name
                                    "
                                    class="h-full w-full object-cover"
                                />
                                <span
                                    v-else
                                    class="text-3xl font-semibold text-slate-500 dark:text-slate-400"
                                >
                                    {{ elder.first_name.charAt(0)
                                    }}{{ elder.last_name.charAt(0) }}
                                </span>
                            </div>
                            <div class="text-center">
                                <p
                                    class="text-lg font-semibold text-slate-900 dark:text-slate-100"
                                >
                                    {{ props.elder.first_name }}
                                    {{ props.elder.last_name }}
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
                                <p
                                    class="text-xs font-semibold tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                >
                                    General Information
                                </p>
                                <div
                                    class="space-y-2 rounded-lg border border-slate-200/70 bg-white/70 p-4 text-sm shadow-sm dark:border-slate-800/50 dark:bg-slate-900/60"
                                >
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Branch
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ props.elder.branch.name }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Date of Birth
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{
                                                props.elder.date_of_birth ?? '-'
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Gender
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ props.elder.gender ?? '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Address
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ props.elder.address ?? '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            City, Country
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ props.elder.city ?? '-' }},
                                            {{ props.elder.country ?? '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Phone
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ props.elder.phone ?? '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Bio
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ props.elder.bio ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <p
                                    class="text-xs font-semibold tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                >
                                    Health & Financial
                                </p>
                                <div
                                    class="space-y-2 rounded-lg border border-slate-200/70 bg-white/70 p-4 text-sm shadow-sm dark:border-slate-800/50 dark:bg-slate-900/60"
                                >
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Health Status
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{
                                                props.elder.health_status ?? '-'
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Special Needs
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{
                                                props.elder.special_needs ?? '-'
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Monthly Expenses
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{
                                                props.elder.monthly_expenses ??
                                                '-'
                                            }}
                                            ETB
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Video
                                        </p>
                                        <div
                                            v-if="props.elder.video_url"
                                            class="mt-1"
                                        >
                                            <video
                                                controls
                                                class="w-full rounded-lg"
                                                :src="`/storage/${props.elder.video_url}`"
                                            ></video>
                                        </div>
                                        <p
                                            v-else
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            -
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </GlassCard>

            <GlassCard
                padding="p-0"
                class="print:border print:bg-white print:shadow-none"
            >
                <div
                    class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 p-6 dark:border-slate-800/60 dark:bg-slate-900/60 print:border print:bg-white"
                >
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-4">
                            <div>
                                <h2
                                    class="text-sm font-semibold text-slate-900 dark:text-slate-100"
                                >
                                    Lifecycle
                                </h2>
                                <p
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
                                    Update the elder status and keep an audit
                                    trail.
                                </p>
                            </div>

                            <div
                                class="rounded-lg border border-slate-200/70 bg-white/70 p-4 text-sm shadow-sm dark:border-slate-800/50 dark:bg-slate-900/60"
                            >
                                <div class="grid gap-3 md:grid-cols-2">
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Current status
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{
                                                props.elder.current_status ??
                                                '-'
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Admitted at
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ props.elder.admitted_at ?? '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                        >
                                            Deceased at
                                        </p>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ props.elder.deceased_at ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <form
                                class="space-y-4"
                                @submit.prevent="submitLifecycle"
                            >
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                        >Change to</label
                                    >
                                    <select
                                        v-model="lifecycleForm.to_status"
                                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                    >
                                        <option value="available">
                                            Available
                                        </option>
                                        <option value="admitted">
                                            Admitted
                                        </option>
                                        <option value="sponsored">
                                            Sponsored
                                        </option>
                                        <option value="transferred">
                                            Transferred
                                        </option>
                                        <option value="deceased">
                                            Deceased
                                        </option>
                                    </select>
                                    <InputError
                                        :message="
                                            lifecycleForm.errors.to_status
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                        >Occurred at (optional)</label
                                    >
                                    <input
                                        v-model="lifecycleForm.occurred_at"
                                        type="datetime-local"
                                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                    />
                                    <InputError
                                        :message="
                                            lifecycleForm.errors.occurred_at
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                        >Reason (optional)</label
                                    >
                                    <textarea
                                        v-model="lifecycleForm.reason"
                                        rows="3"
                                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                    ></textarea>
                                    <InputError
                                        :message="lifecycleForm.errors.reason"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="flex justify-end">
                                    <GlassButton
                                        size="sm"
                                        type="submit"
                                        :disabled="lifecycleForm.processing"
                                        variant="primary"
                                        class="flex items-center gap-2"
                                    >
                                        <Plus class="size-4" />
                                        <span>Update status</span>
                                    </GlassButton>
                                </div>
                            </form>

                            <div class="space-y-2">
                                <p
                                    class="text-xs font-semibold tracking-wide text-slate-400 uppercase dark:text-slate-500"
                                >
                                    Recent status events
                                </p>
                                <ActivityTimeline
                                    :entries="statusTimelineEntries"
                                />
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="space-y-4">
                                <div>
                                    <h2
                                        class="text-sm font-semibold text-slate-900 dark:text-slate-100"
                                    >
                                        Health assessments
                                    </h2>
                                    <p
                                        class="text-xs text-slate-500 dark:text-slate-400"
                                    >
                                        Track periodic assessments.
                                    </p>
                                </div>

                                <form
                                    class="space-y-4"
                                    @submit.prevent="submitAssessment"
                                >
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                            >Assessment date</label
                                        >
                                        <input
                                            v-model="
                                                assessmentForm.assessment_date
                                            "
                                            type="date"
                                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        />
                                        <InputError
                                            :message="
                                                assessmentForm.errors
                                                    .assessment_date
                                            "
                                            class="mt-2"
                                        />
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                            >Summary</label
                                        >
                                        <textarea
                                            v-model="assessmentForm.summary"
                                            rows="3"
                                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        ></textarea>
                                        <InputError
                                            :message="
                                                assessmentForm.errors.summary
                                            "
                                            class="mt-2"
                                        />
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                                >Mobility level</label
                                            >
                                            <input
                                                v-model="
                                                    assessmentForm.mobility_level
                                                "
                                                type="text"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError
                                                :message="
                                                    assessmentForm.errors
                                                        .mobility_level
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                                >Risk level</label
                                            >
                                            <input
                                                v-model="
                                                    assessmentForm.risk_level
                                                "
                                                type="text"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError
                                                :message="
                                                    assessmentForm.errors
                                                        .risk_level
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-end gap-2"
                                    >
                                        <GlassButton
                                            v-if="assessmentEditingId"
                                            size="sm"
                                            variant="secondary"
                                            type="button"
                                            @click="resetAssessmentForm"
                                            >Cancel</GlassButton
                                        >
                                        <GlassButton
                                            size="sm"
                                            type="submit"
                                            :disabled="
                                                assessmentForm.processing
                                            "
                                            variant="primary"
                                            class="flex items-center gap-2"
                                        >
                                            <Plus class="size-4" />
                                            <span>{{
                                                assessmentEditingId
                                                    ? 'Save changes'
                                                    : 'Add assessment'
                                            }}</span>
                                        </GlassButton>
                                    </div>
                                </form>

                                <div class="space-y-2">
                                    <div
                                        v-if="props.healthAssessments.length"
                                        class="space-y-2"
                                    >
                                        <div
                                            v-for="assessment in props.healthAssessments"
                                            :key="assessment.id"
                                            class="rounded-lg border border-slate-200/70 bg-white/70 p-3 text-sm dark:border-slate-800/50 dark:bg-slate-900/60"
                                        >
                                            <div
                                                class="flex items-start justify-between gap-3"
                                            >
                                                <div>
                                                    <p
                                                        class="font-medium text-slate-900 dark:text-slate-100"
                                                    >
                                                        {{
                                                            assessment.assessment_date
                                                        }}
                                                    </p>
                                                    <p
                                                        class="mt-1 text-sm text-slate-700 dark:text-slate-200"
                                                    >
                                                        {{ assessment.summary }}
                                                    </p>
                                                    <p
                                                        class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                                                    >
                                                        <span
                                                            v-if="
                                                                assessment.mobility_level
                                                            "
                                                            >Mobility:
                                                            {{
                                                                assessment.mobility_level
                                                            }}</span
                                                        >
                                                        <span
                                                            v-if="
                                                                assessment.risk_level
                                                            "
                                                        >
                                                            · Risk:
                                                            {{
                                                                assessment.risk_level
                                                            }}</span
                                                        >
                                                        <template
                                                            v-if="
                                                                assessment
                                                                    .creator
                                                                    ?.name
                                                            "
                                                        >
                                                            · by
                                                            {{
                                                                assessment
                                                                    .creator
                                                                    .name
                                                            }}</template
                                                        >
                                                    </p>
                                                </div>
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <button
                                                        type="button"
                                                        class="text-xs font-medium text-indigo-700 hover:underline dark:text-indigo-200"
                                                        @click="
                                                            startEditAssessment(
                                                                assessment,
                                                            )
                                                        "
                                                    >
                                                        Edit
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center gap-1 text-xs font-medium text-red-700 hover:underline dark:text-red-200"
                                                        @click="
                                                            destroyAssessment(
                                                                assessment.id,
                                                            )
                                                        "
                                                    >
                                                        <Trash2
                                                            class="size-4"
                                                        />
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p
                                        v-else
                                        class="text-sm text-slate-500 dark:text-slate-400"
                                    >
                                        No assessments recorded.
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <h2
                                        class="text-sm font-semibold text-slate-900 dark:text-slate-100"
                                    >
                                        Medical conditions
                                    </h2>
                                    <p
                                        class="text-xs text-slate-500 dark:text-slate-400"
                                    >
                                        Maintain active/inactive conditions.
                                    </p>
                                </div>

                                <form
                                    class="space-y-4"
                                    @submit.prevent="submitCondition"
                                >
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                            >Condition name</label
                                        >
                                        <input
                                            v-model="
                                                conditionForm.condition_name
                                            "
                                            type="text"
                                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        />
                                        <InputError
                                            :message="
                                                conditionForm.errors
                                                    .condition_name
                                            "
                                            class="mt-2"
                                        />
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                                >Diagnosed at</label
                                            >
                                            <input
                                                v-model="
                                                    conditionForm.diagnosed_at
                                                "
                                                type="date"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError
                                                :message="
                                                    conditionForm.errors
                                                        .diagnosed_at
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                                >Status</label
                                            >
                                            <select
                                                v-model="conditionForm.status"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            >
                                                <option value="active">
                                                    Active
                                                </option>
                                                <option value="inactive">
                                                    Inactive
                                                </option>
                                                <option value="resolved">
                                                    Resolved
                                                </option>
                                            </select>
                                            <InputError
                                                :message="
                                                    conditionForm.errors.status
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                            >Notes</label
                                        >
                                        <textarea
                                            v-model="conditionForm.notes"
                                            rows="3"
                                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        ></textarea>
                                        <InputError
                                            :message="
                                                conditionForm.errors.notes
                                            "
                                            class="mt-2"
                                        />
                                    </div>

                                    <div
                                        class="flex items-center justify-end gap-2"
                                    >
                                        <GlassButton
                                            v-if="conditionEditingId"
                                            size="sm"
                                            variant="secondary"
                                            type="button"
                                            @click="resetConditionForm"
                                            >Cancel</GlassButton
                                        >
                                        <GlassButton
                                            size="sm"
                                            type="submit"
                                            :disabled="conditionForm.processing"
                                            variant="primary"
                                            class="flex items-center gap-2"
                                        >
                                            <Plus class="size-4" />
                                            <span>{{
                                                conditionEditingId
                                                    ? 'Save changes'
                                                    : 'Add condition'
                                            }}</span>
                                        </GlassButton>
                                    </div>
                                </form>

                                <div class="space-y-2">
                                    <div
                                        v-if="props.medicalConditions.length"
                                        class="space-y-2"
                                    >
                                        <div
                                            v-for="condition in props.medicalConditions"
                                            :key="condition.id"
                                            class="rounded-lg border border-slate-200/70 bg-white/70 p-3 text-sm dark:border-slate-800/50 dark:bg-slate-900/60"
                                        >
                                            <div
                                                class="flex items-start justify-between gap-3"
                                            >
                                                <div>
                                                    <p
                                                        class="font-medium text-slate-900 dark:text-slate-100"
                                                    >
                                                        {{
                                                            condition.condition_name
                                                        }}
                                                    </p>
                                                    <p
                                                        class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                                                    >
                                                        Status:
                                                        {{ condition.status }}
                                                        <span
                                                            v-if="
                                                                condition.diagnosed_at
                                                            "
                                                        >
                                                            · Diagnosed:
                                                            {{
                                                                condition.diagnosed_at
                                                            }}</span
                                                        >
                                                    </p>
                                                    <p
                                                        v-if="condition.notes"
                                                        class="mt-1 text-xs text-slate-600 dark:text-slate-300"
                                                    >
                                                        {{ condition.notes }}
                                                    </p>
                                                </div>
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <button
                                                        type="button"
                                                        class="text-xs font-medium text-indigo-700 hover:underline dark:text-indigo-200"
                                                        @click="
                                                            startEditCondition(
                                                                condition,
                                                            )
                                                        "
                                                    >
                                                        Edit
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center gap-1 text-xs font-medium text-red-700 hover:underline dark:text-red-200"
                                                        @click="
                                                            destroyCondition(
                                                                condition.id,
                                                            )
                                                        "
                                                    >
                                                        <Trash2
                                                            class="size-4"
                                                        />
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p
                                        v-else
                                        class="text-sm text-slate-500 dark:text-slate-400"
                                    >
                                        No conditions recorded.
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <h2
                                        class="text-sm font-semibold text-slate-900 dark:text-slate-100"
                                    >
                                        Medications
                                    </h2>
                                    <p
                                        class="text-xs text-slate-500 dark:text-slate-400"
                                    >
                                        Track active and historical medications.
                                    </p>
                                </div>

                                <form
                                    class="space-y-4"
                                    @submit.prevent="submitMedication"
                                >
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                            >Medication name</label
                                        >
                                        <input
                                            v-model="
                                                medicationForm.medication_name
                                            "
                                            type="text"
                                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        />
                                        <InputError
                                            :message="
                                                medicationForm.errors
                                                    .medication_name
                                            "
                                            class="mt-2"
                                        />
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                                >Dosage</label
                                            >
                                            <input
                                                v-model="medicationForm.dosage"
                                                type="text"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError
                                                :message="
                                                    medicationForm.errors.dosage
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                                >Frequency</label
                                            >
                                            <input
                                                v-model="
                                                    medicationForm.frequency
                                                "
                                                type="text"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError
                                                :message="
                                                    medicationForm.errors
                                                        .frequency
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                                >Started at</label
                                            >
                                            <input
                                                v-model="
                                                    medicationForm.started_at
                                                "
                                                type="date"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError
                                                :message="
                                                    medicationForm.errors
                                                        .started_at
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                                >Ended at</label
                                            >
                                            <input
                                                v-model="
                                                    medicationForm.ended_at
                                                "
                                                type="date"
                                                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                            />
                                            <InputError
                                                :message="
                                                    medicationForm.errors
                                                        .ended_at
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                            >Notes</label
                                        >
                                        <textarea
                                            v-model="medicationForm.notes"
                                            rows="3"
                                            class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                        ></textarea>
                                        <InputError
                                            :message="
                                                medicationForm.errors.notes
                                            "
                                            class="mt-2"
                                        />
                                    </div>

                                    <div
                                        class="flex items-center justify-end gap-2"
                                    >
                                        <GlassButton
                                            v-if="medicationEditingId"
                                            size="sm"
                                            variant="secondary"
                                            type="button"
                                            @click="resetMedicationForm"
                                            >Cancel</GlassButton
                                        >
                                        <GlassButton
                                            size="sm"
                                            type="submit"
                                            :disabled="
                                                medicationForm.processing
                                            "
                                            variant="primary"
                                            class="flex items-center gap-2"
                                        >
                                            <Plus class="size-4" />
                                            <span>{{
                                                medicationEditingId
                                                    ? 'Save changes'
                                                    : 'Add medication'
                                            }}</span>
                                        </GlassButton>
                                    </div>
                                </form>

                                <div class="space-y-2">
                                    <div
                                        v-if="props.medications.length"
                                        class="space-y-2"
                                    >
                                        <div
                                            v-for="medication in props.medications"
                                            :key="medication.id"
                                            class="rounded-lg border border-slate-200/70 bg-white/70 p-3 text-sm dark:border-slate-800/50 dark:bg-slate-900/60"
                                        >
                                            <div
                                                class="flex items-start justify-between gap-3"
                                            >
                                                <div>
                                                    <p
                                                        class="font-medium text-slate-900 dark:text-slate-100"
                                                    >
                                                        {{
                                                            medication.medication_name
                                                        }}
                                                    </p>
                                                    <p
                                                        class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                                                    >
                                                        <span
                                                            v-if="
                                                                medication.dosage
                                                            "
                                                            >Dosage:
                                                            {{
                                                                medication.dosage
                                                            }}</span
                                                        >
                                                        <span
                                                            v-if="
                                                                medication.frequency
                                                            "
                                                        >
                                                            · Frequency:
                                                            {{
                                                                medication.frequency
                                                            }}</span
                                                        >
                                                    </p>
                                                    <p
                                                        class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                                                    >
                                                        <span
                                                            v-if="
                                                                medication.started_at
                                                            "
                                                            >Start:
                                                            {{
                                                                medication.started_at
                                                            }}</span
                                                        >
                                                        <span
                                                            v-if="
                                                                medication.ended_at
                                                            "
                                                        >
                                                            · End:
                                                            {{
                                                                medication.ended_at
                                                            }}</span
                                                        >
                                                    </p>
                                                    <p
                                                        v-if="medication.notes"
                                                        class="mt-1 text-xs text-slate-600 dark:text-slate-300"
                                                    >
                                                        {{ medication.notes }}
                                                    </p>
                                                </div>
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <button
                                                        type="button"
                                                        class="text-xs font-medium text-indigo-700 hover:underline dark:text-indigo-200"
                                                        @click="
                                                            startEditMedication(
                                                                medication,
                                                            )
                                                        "
                                                    >
                                                        Edit
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center gap-1 text-xs font-medium text-red-700 hover:underline dark:text-red-200"
                                                        @click="
                                                            destroyMedication(
                                                                medication.id,
                                                            )
                                                        "
                                                    >
                                                        <Trash2
                                                            class="size-4"
                                                        />
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p
                                        v-else
                                        class="text-sm text-slate-500 dark:text-slate-400"
                                    >
                                        No medications recorded.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </GlassCard>

            <GlassCard variant="lite" class="print:hidden">
                <div class="space-y-4">
                    <div>
                        <h2
                            class="text-sm font-semibold text-slate-900 dark:text-slate-100"
                        >
                            Consent & Attachments
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Store signed consent forms, IDs, and other
                            supporting files for this elder.
                        </p>
                    </div>

                    <form
                        v-if="props.can.manage_documents"
                        class="space-y-4 rounded-lg border border-slate-200/70 bg-white/70 p-4 dark:border-slate-800/50 dark:bg-slate-900/60"
                        @submit.prevent="submitDocument"
                    >
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                    >Document type</label
                                >
                                <select
                                    v-model="documentForm.type"
                                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                >
                                    <option
                                        v-for="option in documentTypes"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                                <InputError
                                    :message="documentForm.errors.type"
                                    class="mt-2"
                                />
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                    >Label (optional)</label
                                >
                                <input
                                    v-model="documentForm.label"
                                    type="text"
                                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                />
                                <InputError
                                    :message="documentForm.errors.label"
                                    class="mt-2"
                                />
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >File</label
                            >
                            <input
                                ref="documentFileInput"
                                type="file"
                                class="mt-1 block w-full text-sm text-slate-700 dark:text-slate-200"
                                @change="handleDocumentFileChange"
                            />
                            <InputError
                                :message="documentForm.errors.file"
                                class="mt-2"
                            />
                        </div>
                        <div class="flex items-center justify-end gap-3">
                            <GlassButton
                                v-if="documentForm.processing"
                                size="sm"
                                variant="ghost"
                                type="button"
                                disabled
                                >Uploading…</GlassButton
                            >
                            <GlassButton
                                v-else
                                size="sm"
                                type="submit"
                                variant="primary"
                                class="flex items-center gap-2"
                            >
                                <Plus class="size-4" />
                                <span>Upload document</span>
                            </GlassButton>
                        </div>
                    </form>

                    <div
                        class="rounded-xl border border-slate-200/70 bg-white/70 dark:border-slate-800/50 dark:bg-slate-900/60"
                    >
                        <div class="divide-y divide-slate-200 dark:divide-slate-800">
                            <div
                                v-if="props.documents.length"
                                class="divide-y divide-slate-200 dark:divide-slate-800"
                            >
                                <div
                                    v-for="doc in props.documents"
                                    :key="doc.id"
                                    class="flex flex-col gap-2 px-4 py-4 text-sm md:flex-row md:items-center md:justify-between"
                                >
                                    <div>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ doc.label ?? doc.file_name }}
                                        </p>
                                        <p
                                            class="text-xs text-slate-500 dark:text-slate-400"
                                        >
                                            {{ doc.type }} ·
                                            {{ doc.uploaded_at }}
                                            <span
                                                v-if="doc.uploader?.name"
                                            >
                                                · by {{ doc.uploader.name }}
                                            </span>
                                        </p>
                                    </div>
                                    <div
                                        class="flex flex-wrap items-center gap-2"
                                    >
                                        <Link
                                            :href="doc.download_url"
                                            target="_blank"
                                            class="text-xs font-medium text-indigo-700 hover:underline dark:text-indigo-200"
                                        >
                                            Download
                                        </Link>
                                        <button
                                            v-if="props.can.manage_documents"
                                            type="button"
                                            class="inline-flex items-center gap-1 text-xs font-medium text-red-700 hover:underline dark:text-red-200"
                                            @click="destroyDocument(doc.id)"
                                        >
                                            <Trash2 class="size-4" />
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p
                                v-else
                                class="px-4 py-6 text-sm text-slate-500 dark:text-slate-400"
                            >
                                No documents uploaded yet.
                            </p>
                        </div>
                    </div>
                </div>
            </GlassCard>

            <GlassCard variant="lite" class="print:hidden">
                <div class="space-y-6">
                    <div class="flex flex-col gap-1">
                        <h2
                            class="text-sm font-semibold text-slate-900 dark:text-slate-100"
                        >
                            Match proposals
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Share this elder with potential donors and track
                            responses.
                        </p>
                    </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div
                        v-if="props.can.propose_match"
                        class="rounded-lg border border-slate-200/70 bg-white/70 p-5 dark:border-slate-800/50 dark:bg-slate-900/60"
                    >
                        <form class="space-y-4" @submit.prevent="submitProposal">
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                    >Select donor</label
                                >
                                <select
                                    v-model="proposalForm.donor_id"
                                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                >
                                    <option value="" disabled>
                                        Choose donor
                                    </option>
                                    <option
                                        v-for="donor in props.donors"
                                        :key="donor.id"
                                        :value="donor.id"
                                    >
                                        {{ donor.name }} · {{ donor.email }}
                                    </option>
                                </select>
                                <InputError
                                    :message="proposalForm.errors.donor_id"
                                    class="mt-2"
                                />
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                        >Relationship</label
                                    >
                                    <select
                                        v-model="proposalForm.relationship_type"
                                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                    >
                                        <option value="">Choose…</option>
                                        <option
                                            v-for="option in relationshipCards"
                                            :key="option.relation"
                                            :value="option.relation"
                                        >
                                            {{ option.title }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="
                                            proposalForm.errors
                                                .relationship_type
                                        "
                                        class="mt-2"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                        >Monthly amount (ETB)</label
                                    >
                                    <input
                                        v-model="proposalForm.amount"
                                        type="number"
                                        min="100"
                                        step="50"
                                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                    />
                                    <InputError
                                        :message="proposalForm.errors.amount"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                        >Frequency</label
                                    >
                                    <select
                                        v-model="proposalForm.frequency"
                                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                    >
                                        <option
                                            v-for="freq in frequencyOptions"
                                            :key="freq.value"
                                            :value="freq.value"
                                            >{{ freq.label }}</option
                                        >
                                    </select>
                                    <InputError
                                        :message="
                                            proposalForm.errors.frequency
                                        "
                                        class="mt-2"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                        >Expires in (hours)</label
                                    >
                                    <input
                                        v-model="proposalForm.expires_in_hours"
                                        type="number"
                                        min="1"
                                        max="168"
                                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                    />
                                    <InputError
                                        :message="
                                            proposalForm.errors
                                                .expires_in_hours
                                        "
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                    >Notes</label
                                >
                                <textarea
                                    v-model="proposalForm.notes"
                                    rows="3"
                                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40"
                                ></textarea>
                                <InputError
                                    :message="proposalForm.errors.notes"
                                    class="mt-2"
                                />
                            </div>

                            <div class="flex justify-end">
                                <GlassButton
                                    size="sm"
                                    type="submit"
                                    :disabled="proposalForm.processing"
                                    variant="primary"
                                    class="flex items-center gap-2"
                                >
                                    <Plus class="size-4" />
                                    <span>Send proposal</span>
                                </GlassButton>
                            </div>
                        </form>
                        <p
                            v-if="!props.donors.length"
                            class="mt-3 text-xs text-slate-500"
                        >
                            No donors available? Invite new donors under the user
                            management area.
                        </p>
                    </div>

                    <div
                        class="rounded-lg border border-slate-200/70 bg-white/70 p-5 dark:border-slate-800/50 dark:bg-slate-900/60"
                    >
                        <h3
                            class="text-sm font-semibold text-slate-900 dark:text-slate-100"
                        >
                            Proposal history
                        </h3>
                        <div
                            v-if="props.proposals.length"
                            class="mt-4 space-y-3"
                        >
                            <div
                                v-for="proposal in props.proposals"
                                :key="proposal.id"
                                class="rounded-lg border border-slate-200 p-3 text-sm dark:border-slate-800"
                            >
                                <div
                                    class="flex flex-wrap items-center justify-between gap-2"
                                >
                                    <div>
                                        <p
                                            class="font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ proposal.donor.name }}
                                        </p>
                                        <p
                                            class="text-xs text-slate-500 dark:text-slate-400"
                                        >
                                            {{
                                                proposal.relationship_type ||
                                                'General'
                                            }} ·
                                            {{ proposal.amount }} ETB
                                        </p>
                                        <p
                                            class="text-xs text-slate-500 dark:text-slate-400"
                                        >
                                            Expires:
                                            {{
                                                proposal.expires_at ?? 'N/A'
                                            }}
                                        </p>
                                    </div>
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="
                                            proposalStatusClass(
                                                proposal.status,
                                            )
                                        "
                                    >
                                        {{ proposal.status }}
                                    </span>
                                </div>
                                <p
                                    v-if="proposal.notes"
                                    class="mt-2 text-xs text-slate-600 dark:text-slate-300"
                                >
                                    {{ proposal.notes }}
                                </p>
                                <div
                                    v-if="
                                        props.can.propose_match &&
                                        proposal.status === 'pending'
                                    "
                                    class="mt-2 flex gap-3 text-xs"
                                >
                                    <button
                                        type="button"
                                        class="text-red-600 hover:underline dark:text-red-200"
                                        @click="cancelProposal(proposal.id)"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p
                            v-else
                            class="mt-4 text-sm text-slate-500 dark:text-slate-400"
                        >
                            No proposals recorded yet.
                        </p>
                    </div>
                </div>
                </div>
            </GlassCard>

            <CaseNotes
                :elder="elder"
                :notes="caseNotes"
                :can="{
                    create: can.create_case_notes,
                    update: can.update_case_notes,
                    delete: can.delete_case_notes,
                }"
                class="print:hidden"
            />

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
