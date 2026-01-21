<template>
    <GlassCard
        padding="p-0"
        class="print:border print:bg-white print:shadow-none"
    >
        <div
            class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 p-6 dark:border-slate-800/60 dark:bg-slate-900/60 print:border print:bg-white"
        >
            <div class="space-y-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2
                            class="text-sm font-semibold text-slate-900 dark:text-slate-100"
                        >
                            Case Notes
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Internal notes and observations about the elder.
                        </p>
                    </div>
                    <GlassButton
                        v-if="can.create"
                        size="sm"
                        variant="primary"
                        class="w-full sm:w-auto"
                        @click="showForm = true"
                    >
                        <PlusIcon class="mr-2 h-4 w-4" />
                        Add note
                    </GlassButton>
                </div>

                <div class="space-y-4">
                    <div v-if="notes.data.length > 0" class="space-y-4">
                        <div
                            v-for="note in notes.data"
                            :key="note.id"
                            class="rounded-lg border border-slate-200/70 bg-white p-4 shadow-sm dark:border-slate-800/50 dark:bg-slate-800/50"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="text-sm font-medium text-slate-900 dark:text-slate-100"
                                        >
                                            {{ note.author.name }}
                                        </span>
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                            :class="
                                                note.visibility ===
                                                'donor_visible'
                                                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200'
                                                    : note.visibility === 'branch'
                                                        ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200'
                                                        : 'bg-slate-100 text-slate-800 dark:bg-slate-700/50 dark:text-slate-200'
                                            "
                                        >
                                            {{ visibilityLabel(note.visibility) }}
                                        </span>
                                        <span
                                            class="text-xs text-slate-500 dark:text-slate-400"
                                        >
                                            {{ formatDate(note.created_at) }}
                                        </span>
                                    </div>
                                    <p
                                        class="mt-1 text-sm whitespace-pre-line text-slate-700 dark:text-slate-300"
                                    >
                                        {{ note.content }}
                                    </p>
                                    <div
                                        v-if="note.attachments?.length"
                                        class="mt-3 rounded border border-slate-100 bg-slate-50/70 p-3 text-xs text-slate-600 dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-300"
                                    >
                                        <p class="font-semibold text-slate-700 dark:text-slate-200">
                                            Attachments
                                        </p>
                                        <ul class="mt-2 space-y-1">
                                            <li
                                                v-for="attachment in note.attachments"
                                                :key="attachment.id"
                                                class="flex items-center justify-between"
                                            >
                                                <div>
                                                    <a
                                                        :href="attachment.download_url"
                                                        target="_blank"
                                                        class="text-indigo-600 hover:underline"
                                                    >
                                                        {{ attachment.file_name }}
                                                    </a>
                                                    <span v-if="attachment.uploaded_by" class="ml-2 text-slate-400">
                                                        · {{ attachment.uploaded_by }}
                                                    </span>
                                                </div>
                                                <button
                                                    v-if="can.update"
                                                    type="button"
                                                    class="text-xs text-red-500 hover:underline"
                                                    @click="removeAttachment(note.id, attachment.id)"
                                                >
                                                    Remove
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div v-if="note.versions?.length" class="mt-3">
                                        <button
                                            type="button"
                                            class="text-xs font-medium text-indigo-600 hover:underline"
                                            @click="toggleHistory(note.id)"
                                        >
                                            {{
                                                expandedHistory[note.id]
                                                    ? 'Hide history'
                                                    : `Show history (${note.versions.length})`
                                            }}
                                        </button>
                                        <div
                                            v-if="expandedHistory[note.id]"
                                            class="mt-2 space-y-2 rounded border border-slate-200/70 bg-white/80 p-3 text-xs dark:border-slate-700 dark:bg-slate-900/40"
                                        >
                                            <div
                                                v-for="version in note.versions"
                                                :key="version.id"
                                                class="border-b border-slate-100 pb-2 last:border-b-0 last:pb-0"
                                            >
                                                <p class="font-semibold text-slate-600 dark:text-slate-300">
                                                    {{ version.edited_by ?? 'Unknown' }}
                                                    <span class="ml-2 text-slate-400">
                                                        {{ formatDate(version.created_at) }}
                                                    </span>
                                                </p>
                                                <p class="text-slate-500">
                                                    Visibility: {{ visibilityLabel(version.visibility) }}
                                                </p>
                                                <p class="mt-1 whitespace-pre-line text-slate-600">
                                                    {{ version.content }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-if="can.update || can.delete"
                                    class="ml-4 flex space-x-2"
                                >
                                    <button
                                        v-if="can.update"
                                        @click="editNote(note)"
                                        class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-500 dark:hover:bg-slate-700/50 dark:hover:text-slate-300"
                                        title="Edit note"
                                    >
                                        <PencilIcon class="h-4 w-4" />
                                        <span class="sr-only">Edit</span>
                                    </button>
                                    <button
                                        v-if="can.delete"
                                        @click="confirmDelete(note)"
                                        class="rounded p-1 text-slate-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400"
                                        title="Delete note"
                                    >
                                        <TrashIcon class="h-4 w-4" />
                                        <span class="sr-only">Delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <Pagination :links="notes.links" class="mt-4" />
                    </div>
                    <div
                        v-else
                        class="rounded-lg border-2 border-dashed border-slate-300 p-8 text-center dark:border-slate-700"
                    >
                        <DocumentTextIcon
                            class="mx-auto h-12 w-12 text-slate-400"
                        />
                        <h3
                            class="mt-2 text-sm font-medium text-slate-900 dark:text-slate-100"
                        >
                            No case notes yet
                        </h3>
                        <p
                            class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                        >
                            Get started by creating a new case note.
                        </p>
                        <div class="mt-6">
                            <button
                                v-if="can.create"
                                type="button"
                                @click="showCreateForm = true"
                                class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                            >
                                <PlusIcon class="mr-2 -ml-1 h-5 w-5" />
                                New Case Note
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create/Edit Form Modal -->
            <Dialog :open="showForm" @update:open="showForm = $event">
                <DialogContent class="sm:max-w-2xl">
                    <DialogHeader>
                        <DialogTitle
                            >{{ editingNote ? 'Edit' : 'Add' }} Case
                            Note</DialogTitle
                        >
                    </DialogHeader>
                    <form
                        @submit.prevent="
                            editingNote ? updateNote() : createNote()
                        "
                        class="mt-6 space-y-6"
                    >
                        <div>
                            <label
                                for="content"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >Note Content</label
                            >
                            <textarea
                                id="content"
                                ref="contentInput"
                                v-model="form.content"
                                rows="6"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-100"
                                required
                            ></textarea>
                            <InputError
                                :message="form.errors.content"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <label
                                for="visibility"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >Visibility</label
                            >
                            <select
                                id="visibility"
                                v-model="form.visibility"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-100"
                            >
                                <option value="internal">Internal Only</option>
                                <option value="branch">Branch Staff</option>
                                <option value="donor_visible">
                                    Visible to Donors
                                </option>
                            </select>
                            <InputError
                                :message="form.errors.visibility"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                                >Attachments</label
                            >
                            <input
                                ref="attachmentsInput"
                                type="file"
                                multiple
                                class="mt-1 block w-full rounded-md border border-dashed border-slate-300 bg-white/80 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800/50"
                                @change="handleAttachmentSelection"
                            />
                            <p v-if="form.attachments?.length" class="mt-2 text-xs text-slate-500">
                                {{ form.attachments.length }} file(s) selected.
                            </p>
                            <InputError :message="form.errors.attachments" class="mt-2" />
                        </div>

                        <div class="flex justify-end space-x-3">
                            <Button
                                type="button"
                                variant="secondary"
                                @click="closeForm"
                            >
                                Cancel
                            </Button>
                            <Button :disabled="form.processing">
                                {{ editingNote ? 'Update' : 'Create' }} Note
                            </Button>
                        </div>
                    </form>
                </DialogContent>
            </Dialog>

            <!-- Delete Confirmation Modal -->
            <ConfirmModal
                :open="showDeleteModal"
                @update:open="showDeleteModal = $event"
                title="Delete Case Note"
                message="Are you sure you want to delete this case note? This action cannot be undone."
                confirm-text="Delete Note"
                @confirm="deleteNote"
            />
        </div>
    </GlassCard>
</template>

<script setup lang="ts">
import ConfirmModal from '@/components/ConfirmModal.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DocumentTextIcon,
    PencilIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { router, useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { nextTick, reactive, ref } from 'vue';

type Attachment = {
    id: number;
    file_name: string;
    download_url: string;
    uploaded_by?: string | null;
    uploaded_at?: string | null;
};

type Version = {
    id: number;
    content: string;
    visibility: string;
    edited_by?: string | null;
    created_at?: string | null;
};

const props = defineProps({
    elder: {
        type: Object,
        required: true,
    },
    notes: {
        type: Object as () => {
            data: Array<{
                id: number;
                content: string;
                visibility: string;
                created_at: string;
                author: { id: number; name: string };
                attachments: Attachment[];
                versions: Version[];
            }>;
            links: Array<{ url: string | null; label: string; active: boolean }>;
        },
        required: true,
    },
    can: {
        type: Object,
        default: () => ({
            create: false,
            update: false,
            delete: false,
        }),
    },
});

const showForm = ref(false);
const showDeleteModal = ref(false);
const editingNote = ref(null);
const contentInput = ref(null);
const attachmentsInput = ref<HTMLInputElement | null>(null);
const expandedHistory = reactive<Record<number, boolean>>({});

const form = useForm({
    content: '',
    visibility: 'internal',
    attachments: [] as File[],
});

const deleteForm = useForm({});

function formatDate(dateString) {
    return format(new Date(dateString), 'MMM d, yyyy h:mm a');
}

function createNote() {
    form.post(route('elders.case-notes.store', props.elder.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            closeForm();
            form.reset();
            clearAttachmentInput();
        },
    });
}

function updateNote() {
    form.put(
        route('elders.case-notes.update', [
            props.elder.id,
            editingNote.value.id,
        ]),
        {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                closeForm();
                form.reset();
                clearAttachmentInput();
            },
        },
    );
}

function editNote(note) {
    editingNote.value = note;
    form.content = note.content;
    form.visibility = note.visibility;
    showForm.value = true;
    nextTick(() => contentInput.value?.focus());
}

function confirmDelete(note) {
    editingNote.value = note;
    showDeleteModal.value = true;
}

function deleteNote() {
    deleteForm.delete(
        route('elders.case-notes.destroy', [
            props.elder.id,
            editingNote.value.id,
        ]),
        {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteModal.value = false;
            },
        },
    );
}

function closeForm() {
    showForm.value = false;
    editingNote.value = null;
    form.clearErrors();
    form.reset();
    clearAttachmentInput();
}

function handleAttachmentSelection(event: Event) {
    const target = event.target as HTMLInputElement;
    form.attachments = Array.from(target.files ?? []);
}

function clearAttachmentInput() {
    form.attachments = [];
    if (attachmentsInput.value) {
        attachmentsInput.value.value = '';
    }
}

function removeAttachment(noteId: number, attachmentId: number) {
    router.delete(
        route('case-notes.attachments.destroy', {
            case_note: noteId,
            attachment: attachmentId,
        }),
        { preserveScroll: true },
    );
}

function toggleHistory(noteId: number) {
    expandedHistory[noteId] = !expandedHistory[noteId];
}

function visibilityLabel(value: string) {
    switch (value) {
        case 'donor_visible':
            return 'Donor Visible';
        case 'branch':
            return 'Branch Staff';
        default:
            return 'Internal';
    }
}
</script>
