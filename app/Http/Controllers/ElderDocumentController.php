<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreElderDocumentRequest;
use App\Models\Elder;
use App\Models\ElderDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ElderDocumentController extends Controller
{
    public function store(Elder $elder, StoreElderDocumentRequest $request)
    {
        $file = $request->file('file');
        $path = $file->store('elders/documents', 'public');

        $elder->documents()->create([
            'type' => $request->input('type', 'other'),
            'label' => $request->input('label') ?: $file->getClientOriginalName(),
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => $request->user()?->id,
            'uploaded_at' => now(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function download(Elder $elder, ElderDocument $document)
    {
        abort_unless($document->elder_id === $elder->id, 404);

        if (! Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }

        $filename = Str::slug($document->label ?? $document->file_name);

        return Storage::disk('public')->download(
            $document->file_path,
            $filename ?: $document->file_name
        );
    }

    public function destroy(Request $request, Elder $elder, ElderDocument $document)
    {
        abort_unless($document->elder_id === $elder->id, 404);
        Gate::authorize('elders.manage');

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Document removed.');
    }
}
