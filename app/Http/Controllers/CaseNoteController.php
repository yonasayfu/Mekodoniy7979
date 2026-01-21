<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCaseNoteRequest;
use App\Http\Requests\UpdateCaseNoteRequest;
use App\Models\CaseNote;
use App\Models\Elder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CaseNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Elder $elder): Response
    {
        $this->authorize('viewAny', [CaseNote::class, $elder]);

        $notes = $elder->caseNotes()
            ->with([
                'author:id,name,email',
                'attachments.uploader:id,name',
                'versions' => fn ($query) => $query->with('editor:id,name')->latest()->take(5),
            ])
            ->latest()
            ->paginate(10)
            ->through(fn (CaseNote $note) => [
                'id' => $note->id,
                'content' => $note->content,
                'visibility' => $note->visibility,
                'created_at' => optional($note->created_at)->toDateTimeString(),
                'author' => $note->author?->only(['id', 'name', 'email']),
                'attachments' => $note->attachments->map(fn ($attachment) => [
                    'id' => $attachment->id,
                    'file_name' => $attachment->file_name,
                    'download_url' => $attachment->download_url,
                    'uploaded_by' => $attachment->uploader?->name,
                    'uploaded_at' => optional($attachment->created_at)->toDateTimeString(),
                ]),
                'versions' => $note->versions->map(fn ($version) => [
                    'id' => $version->id,
                    'content' => $version->content,
                    'visibility' => $version->visibility,
                    'edited_by' => $version->editor?->name,
                    'created_at' => optional($version->created_at)->toDateTimeString(),
                ]),
            ]);

        return Inertia::render('Elders/CaseNotes/Index', [
            'elder' => $elder->load('branch'),
            'notes' => $notes,
            'can' => [
                'create' => auth()->user()->can('create', [CaseNote::class, $elder]),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCaseNoteRequest $request, Elder $elder): RedirectResponse
    {
        $this->authorize('create', [CaseNote::class, $elder]);

        $note = $elder->caseNotes()->create([
            'branch_id' => $elder->branch_id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'content' => $request->content,
            'visibility' => $request->visibility,
        ]);

        $this->storeAttachments($note, $request->file('attachments', []));

        return redirect()
            ->route('elders.case-notes.index', $elder)
            ->with('success', 'Case note added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCaseNoteRequest $request, Elder $elder, CaseNote $caseNote): RedirectResponse
    {
        $this->authorize('update', $caseNote);

        $payload = $request->validated();

        $caseNote->versions()->create([
            'content' => $caseNote->content,
            'visibility' => $caseNote->visibility,
            'edited_by' => auth()->id(),
        ]);

        $caseNote->update([
            'content' => $payload['content'] ?? $caseNote->content,
            'visibility' => $payload['visibility'] ?? $caseNote->visibility,
            'updated_by' => auth()->id(),
        ]);

        if (! empty($payload['attachments'])) {
            $this->storeAttachments($caseNote, $request->file('attachments', []));
        }

        return back()->with('success', 'Case note updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Elder $elder, CaseNote $caseNote): JsonResponse
    {
        $this->authorize('delete', $caseNote);

        $caseNote->delete();

        return response()->json(['message' => 'Case note deleted successfully.']);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore(Elder $elder, int $caseNoteId): JsonResponse
    {
        $caseNote = CaseNote::withTrashed()->findOrFail($caseNoteId);
        
        $this->authorize('restore', $caseNote);

        $caseNote->restore();

        return response()->json(['message' => 'Case note restored successfully.']);
    }

    protected function storeAttachments(CaseNote $caseNote, array $files = []): void
    {
        $files = array_filter($files);

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store('case-notes/'.$caseNote->elder_id, 'public');

            $caseNote->attachments()->create([
                'branch_id' => $caseNote->branch_id,
                'uploaded_by' => auth()->id(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }
}
