<?php

namespace App\Http\Controllers;

use App\Models\Elder;
use App\Models\ElderMedication;
use Illuminate\Http\Request;

class ElderMedicationController extends Controller
{
    public function store(Request $request, Elder $elder)
    {
        $data = $request->validate([
            'medication_name' => ['required', 'string', 'max:255'],
            'dosage' => ['nullable', 'string', 'max:255'],
            'frequency' => ['nullable', 'string', 'max:255'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $elder->medications()->create($data);

        return back()->with('success', 'Medication added successfully.');
    }

    public function update(Request $request, Elder $elder, ElderMedication $medication)
    {
        if ($medication->elder_id !== $elder->id) {
            abort(404);
        }

        $data = $request->validate([
            'medication_name' => ['required', 'string', 'max:255'],
            'dosage' => ['nullable', 'string', 'max:255'],
            'frequency' => ['nullable', 'string', 'max:255'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $medication->update($data);

        return back()->with('success', 'Medication updated successfully.');
    }

    public function destroy(Elder $elder, ElderMedication $medication)
    {
        if ($medication->elder_id !== $elder->id) {
            abort(404);
        }

        $medication->delete();

        return back()->with('success', 'Medication deleted successfully.');
    }
}
