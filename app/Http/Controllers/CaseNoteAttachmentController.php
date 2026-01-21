<?php

namespace App\Http\Controllers;

use App\Models\CaseNote;
use App\Models\CaseNoteAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CaseNoteAttachmentController extends Controller
{
    public function download(CaseNote $caseNote, CaseNoteAttachment $attachment)
    {
        Gate::authorize('view', $caseNote);
        abort_unless($attachment->case_note_id === $caseNote->id, 404);

        $path = $attachment->storagePath();

        if (! Storage::disk('public')->exists($path)) {
            abort(404, 'Attachment missing.');
        }

        return Storage::disk('public')->download($path, $attachment->file_name, [
            'Content-Type' => $attachment->mime_type ?? 'application/octet-stream',
        ]);
    }

    public function destroy(CaseNote $caseNote, CaseNoteAttachment $attachment, Request $request)
    {
        Gate::authorize('update', $caseNote);
        abort_unless($attachment->case_note_id === $caseNote->id, 404);

        $attachment->deleteFile();
        $attachment->delete();

        return back()->with('success', 'Attachment removed.');
    }
}
