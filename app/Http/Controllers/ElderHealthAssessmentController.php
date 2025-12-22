<?php

namespace App\Http\Controllers;

use App\Models\Elder;
use App\Models\ElderHealthAssessment;
use Illuminate\Http\Request;

class ElderHealthAssessmentController extends Controller
{
    public function store(Request $request, Elder $elder)
    {
        $data = $request->validate([
            'assessment_date' => ['required', 'date'],
            'summary' => ['required', 'string', 'max:2000'],
            'mobility_level' => ['nullable', 'string', 'max:255'],
            'risk_level' => ['nullable', 'string', 'max:255'],
        ]);

        $elder->healthAssessments()->create([
            ...$data,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Assessment added successfully.');
    }

    public function update(Request $request, Elder $elder, ElderHealthAssessment $assessment)
    {
        if ($assessment->elder_id !== $elder->id) {
            abort(404);
        }

        $data = $request->validate([
            'assessment_date' => ['required', 'date'],
            'summary' => ['required', 'string', 'max:2000'],
            'mobility_level' => ['nullable', 'string', 'max:255'],
            'risk_level' => ['nullable', 'string', 'max:255'],
        ]);

        $assessment->update($data);

        return back()->with('success', 'Assessment updated successfully.');
    }

    public function destroy(Elder $elder, ElderHealthAssessment $assessment)
    {
        if ($assessment->elder_id !== $elder->id) {
            abort(404);
        }

        $assessment->delete();

        return back()->with('success', 'Assessment deleted successfully.');
    }
}
