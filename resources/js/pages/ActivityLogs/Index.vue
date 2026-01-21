<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type ActivityLog } from '@/types';
import { Head } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { format, formatDistanceToNow } from 'date-fns';
import { computed, ref } from 'vue';

interface Props {
    activityLogs: {
        data: ActivityLog[];
        links: [];
    };
    breadcrumbs: [];
}

defineProps<Props>();

const selectedLog = ref<ActivityLog | null>(null);
const detailsOpen = ref(false);

const escapeHtml = (value: string) =>
    value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');

const formatValueMarkup = (value: unknown) => {
    if (
        value === null ||
        value === undefined ||
        (typeof value === 'string' && value.length === 0)
    ) {
        return '<span class="text-muted-foreground">—</span>';
    }

    if (typeof value === 'object') {
        return `<code>${escapeHtml(JSON.stringify(value))}</code>`;
    }

    return `<span>${escapeHtml(String(value))}</span>`;
};

const emptyChangesPlaceholder =
    '<span class="text-muted-foreground italic">No field-level data</span>';

const formatChanges = (changes: ActivityLog['changes']) => {
    if (
        !changes ||
        typeof changes !== 'object' ||
        Object.keys(changes).length === 0
    ) {
        return emptyChangesPlaceholder;
    }

    const segments: string[] = [];

    for (const [key, change] of Object.entries(changes)) {
        if (!change || (change.old === undefined && change.new === undefined)) {
            continue;
        }

        segments.push(
            `<div class="space-y-1 pb-2 last:pb-0">
                <div class="font-medium text-slate-900 dark:text-slate-100">${escapeHtml(key)}</div>
                <div class="text-xs text-muted-foreground">Before: ${formatValueMarkup(change.old)}</div>
                <div class="text-xs text-muted-foreground">After: ${formatValueMarkup(change.new)}</div>
            </div>`,
        );
    }

    if (segments.length === 0) {
        return emptyChangesPlaceholder;
    }

    return segments.join('');
};

const detailChanges = computed(() => {
    if (
        !selectedLog.value?.changes ||
        typeof selectedLog.value.changes !== 'object'
    ) {
        return [];
    }

    return Object.entries(selectedLog.value.changes)
        .filter(([, change]) => change !== null && change !== undefined)
        .map(([field, change]) => ({
            field,
            before: change?.old ?? null,
            after: change?.new ?? null,
        }));
});

const openDetails = (log: ActivityLog) => {
    selectedLog.value = log;
    detailsOpen.value = true;
};

const handleDialogToggle = (value: boolean) => {
    detailsOpen.value = value;
    if (!value) {
        selectedLog.value = null;
    }
};

const closeDetails = () => {
    handleDialogToggle(false);
};

const formatDetailValue = (value: unknown) => {
    if (value === null || value === undefined) {
        return '—';
    }

    if (typeof value === 'string') {
        return value.trim().length ? value : '—';
    }

    if (typeof value === 'object') {
        try {
            return JSON.stringify(value, null, 2);
        } catch (error) {
            return String(value);
        }
    }

    return String(value);
};

const isComplexValue = (value: unknown) => {
    if (value === null || value === undefined) {
        return false;
    }

    if (typeof value === 'string') {
        return value.includes('\n');
    }

    return typeof value === 'object';
};

const getSubjectName = (subjectType: string | null | undefined) => {
    if (!subjectType) {
        return 'Unknown';
    }

    const parts = subjectType.split('\\');
    return parts[parts.length - 1] ?? subjectType;
};

const detailTimestamp = computed(() => {
    if (!selectedLog.value) {
        return { relative: '', absolute: '' };
    }

    const date = new Date(selectedLog.value.created_at);

    if (Number.isNaN(date.getTime())) {
        const fallback = selectedLog.value.created_at;
        return { relative: fallback, absolute: fallback };
    }

    return {
        relative: formatDistanceToNow(date, { addSuffix: true }),
        absolute: format(date, 'PPpp'),
    };
});

const rawChangePayload = computed(() => {
    if (!selectedLog.value?.changes) {
        return null;
    }

    try {
        return JSON.stringify(selectedLog.value.changes, null, 2);
    } catch (error) {
        return String(selectedLog.value.changes);
    }
});

const exportHref = route('activity-logs.export', undefined, false);

const formatExactDate = (value: string) => {
    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return format(date, 'PPpp');
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Activity Logs" />

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading
                title="Activity Logs"
                description="View all system and user activity logs."
            />
            <Button as-child variant="outline">
                <a :href="`${exportHref}?type=csv`">Download CSV</a>
            </Button>
        </div>

        <div class="mt-6 rounded-lg bg-white p-6 shadow-md">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Time</TableHead>
                        <TableHead>User</TableHead>
                        <TableHead>Action</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead>Subject</TableHead>
                        <TableHead>Changes</TableHead>
                        <TableHead class="w-28 text-right">Details</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="log in activityLogs.data" :key="log.id">
                        <TableCell>
                            <div class="flex flex-col">
                                <span>{{
                                    formatDistanceToNow(
                                        new Date(log.created_at),
                                        { addSuffix: true },
                                    )
                                }}</span>
                                <span class="text-xs text-muted-foreground">{{
                                    formatExactDate(log.created_at)
                                }}</span>
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="flex flex-col">
                                <span>{{ log.causer?.name ?? 'System' }}</span>
                                <span
                                    v-if="log.causer?.email"
                                    class="text-xs text-muted-foreground"
                                >
                                    {{ log.causer.email }}
                                </span>
                            </div>
                        </TableCell>
                        <TableCell>{{ log.action }}</TableCell>
                        <TableCell>{{ log.description ?? '—' }}</TableCell>
                        <TableCell>
                            <div class="flex flex-col">
                                <span>{{
                                    getSubjectName(log.subject_type)
                                }}</span>
                                <span class="text-xs text-muted-foreground"
                                    >ID: {{ log.subject_id ?? '—' }}</span
                                >
                            </div>
                        </TableCell>
                        <TableCell
                            v-html="formatChanges(log.changes)"
                        ></TableCell>
                        <TableCell class="text-right">
                            <Button
                                size="sm"
                                variant="outline"
                                @click="openDetails(log)"
                            >
                                View details
                            </Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <Pagination :links="activityLogs.links" class="mt-6" />
        </div>

        <Dialog :open="detailsOpen" @update:open="handleDialogToggle">
            <DialogContent class="sm:max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Activity details</DialogTitle>
                    <DialogDescription v-if="selectedLog">
                        Logged {{ detailTimestamp.relative }}
                        <span class="ml-1 text-muted-foreground"
                            >(on {{ detailTimestamp.absolute }})</span
                        >
                    </DialogDescription>
                </DialogHeader>

                <div v-if="selectedLog" class="space-y-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <p
                                class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                            >
                                Action
                            </p>
                            <p
                                class="font-medium text-slate-900 dark:text-slate-100"
                            >
                                {{ selectedLog.action }}
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                            >
                                Subject
                            </p>
                            <p
                                class="font-medium text-slate-900 dark:text-slate-100"
                            >
                                {{ getSubjectName(selectedLog.subject_type) }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                Identifier: {{ selectedLog.subject_id ?? '—' }}
                            </p>
                            <p class="truncate text-xs text-muted-foreground">
                                <span
                                    class="font-semibold text-slate-500 dark:text-slate-400"
                                    >Class:</span
                                >
                                {{ selectedLog.subject_type ?? 'n/a' }}
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                            >
                                Triggered by
                            </p>
                            <p
                                class="font-medium text-slate-900 dark:text-slate-100"
                            >
                                {{ selectedLog.causer?.name ?? 'System' }}
                            </p>
                            <p
                                v-if="selectedLog.causer?.email"
                                class="text-xs text-muted-foreground"
                            >
                                {{ selectedLog.causer.email }}
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                            >
                                Event reference
                            </p>
                            <p
                                class="font-medium text-slate-900 dark:text-slate-100"
                            >
                                #{{ selectedLog.id }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                Created {{ detailTimestamp.absolute }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Description
                        </p>
                        <p class="mt-2 text-sm leading-relaxed">
                            {{
                                selectedLog.description ??
                                'No description was provided for this activity.'
                            }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Field changes
                        </p>
                        <div v-if="detailChanges.length" class="mt-2 space-y-3">
                            <div
                                v-for="change in detailChanges"
                                :key="change.field"
                                class="rounded-md border border-border/60 bg-muted/40 px-4 py-3"
                            >
                                <p
                                    class="text-sm font-medium text-slate-900 dark:text-slate-100"
                                >
                                    {{ change.field }}
                                </p>
                                <div class="mt-2 grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <p
                                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                        >
                                            Before
                                        </p>
                                        <pre
                                            v-if="isComplexValue(change.before)"
                                            class="mt-1 overflow-x-auto rounded bg-background/70 p-2 text-xs whitespace-pre-wrap"
                                            >{{
                                                formatDetailValue(change.before)
                                            }}</pre
                                        >
                                        <p v-else class="mt-1 text-sm">
                                            {{
                                                formatDetailValue(change.before)
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                        >
                                            After
                                        </p>
                                        <pre
                                            v-if="isComplexValue(change.after)"
                                            class="mt-1 overflow-x-auto rounded bg-background/70 p-2 text-xs whitespace-pre-wrap"
                                            >{{
                                                formatDetailValue(change.after)
                                            }}</pre
                                        >
                                        <p v-else class="mt-1 text-sm">
                                            {{
                                                formatDetailValue(change.after)
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p
                            v-else
                            class="mt-2 text-sm text-muted-foreground italic"
                        >
                            No field-level changes were recorded for this event.
                        </p>
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Raw change payload
                        </p>
                        <pre
                            v-if="rawChangePayload"
                            class="mt-2 max-h-60 overflow-y-auto rounded bg-muted/30 p-3 text-xs whitespace-pre-wrap"
                            >{{ rawChangePayload }}</pre
                        >
                        <p
                            v-else
                            class="mt-2 text-sm text-muted-foreground italic"
                        >
                            No raw change payload is available for this
                            activity.
                        </p>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="secondary" @click="closeDetails"
                        >Close</Button
                    >
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
