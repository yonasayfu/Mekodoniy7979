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
            ->with(['author' => function ($query) {
                $query->select('id', 'name', 'email');
            }])
            ->latest()
            ->paginate(10);

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
        $note = $elder->caseNotes()->create([
            'branch_id' => $elder->branch_id,
            'created_by' => auth()->id(),
            'content' => $request->content,
            'visibility' => $request->visibility,
        ]);

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

        $caseNote->update($request->validated());

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
}
