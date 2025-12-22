<?php

namespace App\Http\Controllers;

use App\Models\Elder;
use App\Models\ElderMedicalCondition;
use Illuminate\Http\Request;

class ElderMedicalConditionController extends Controller
{
    public function store(Request $request, Elder $elder)
    {
        $data = $request->validate([
            'condition_name' => ['required', 'string', 'max:255'],
            'diagnosed_at' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $elder->medicalConditions()->create($data);

        return back()->with('success', 'Medical condition added successfully.');
    }

    public function update(Request $request, Elder $elder, ElderMedicalCondition $condition)
    {
        if ($condition->elder_id !== $elder->id) {
            abort(404);
        }

        $data = $request->validate([
            'condition_name' => ['required', 'string', 'max:255'],
            'diagnosed_at' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $condition->update($data);

        return back()->with('success', 'Medical condition updated successfully.');
    }

    public function destroy(Elder $elder, ElderMedicalCondition $condition)
    {
        if ($condition->elder_id !== $elder->id) {
            abort(404);
        }

        $condition->delete();

        return back()->with('success', 'Medical condition deleted successfully.');
    }
}
