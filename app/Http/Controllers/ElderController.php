<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreElderRequest;
use App\Http\Requests\UpdateElderRequest;
use App\Models\Elder;
use App\Models\Branch; // Import Branch model
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage; // Import Storage facade

class ElderController extends Controller
{
    use HandlesDataExport;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $elders = Elder::with('branch')->paginate(10)
            ->through(fn ($elder) => [
                'id' => $elder->id,
                'first_name' => $elder->first_name,
                'last_name' => $elder->last_name,
                'date_of_birth' => $elder->date_of_birth,
                'gender' => $elder->gender,
                'priority_level' => $elder->priority_level,
                'monthly_expenses' => $elder->monthly_expenses,
                'branch_name' => $elder->branch->name,
            ]);

        return Inertia::render('Elders/Index', [
            'elders' => $elders,
            'can' => [
                'create' => true,
                'edit' => true,
                'delete' => true,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::all(['id', 'name']); // Get all branches for dropdown
        return Inertia::render('Elders/Create', [
            'branches' => $branches,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreElderRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('profile_picture')) {
            $validatedData['profile_picture_path'] = $request->file('profile_picture')->store('elders/profile_pictures', 'public');
        }

        if ($request->hasFile('video')) {
            $validatedData['video_url'] = $request->file('video')->store('elders/videos', 'public');
        }

        Elder::create($validatedData);
        return redirect()->route('elders.index')->with('success', 'Elder created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Elder $elder)
    {
        $elder->load([
            'branch',
            'activityLogs.causer',
            'statusEvents.creator',
            'healthAssessments.creator',
            'medicalConditions',
            'medications',
            'caseNotes' => function ($query) {
                $query->with('author')
                    ->latest()
                    ->paginate(10);
            },
        ]);

        $user = auth()->user();

        return Inertia::render('Elders/Show', [
            'elder' => $elder,
            'activity' => $elder->activityLogs,
            'statusEvents' => $elder->statusEvents()
                ->with('creator')
                ->latest('occurred_at')
                ->take(50)
                ->get(),
            'healthAssessments' => $elder->healthAssessments()
                ->with('creator')
                ->latest('assessment_date')
                ->take(50)
                ->get(),
            'medicalConditions' => $elder->medicalConditions()
                ->latest()
                ->take(50)
                ->get(),
            'medications' => $elder->medications()
                ->latest()
                ->take(50)
                ->get(),
            'caseNotes' => $elder->caseNotes()
                ->with('author')
                ->latest()
                ->paginate(10)
                ->appends(request()->query()),
            'can' => [
                'update' => auth()->user()->can('update', $elder),
                'delete' => auth()->user()->can('delete', $elder),
                'create_case_notes' => $user->can('create', [\App\Models\CaseNote::class, $elder]),
                'update_case_notes' => $user->can('create', \App\Models\CaseNote::class),
                'delete_case_notes' => $user->can('create', \App\Models\CaseNote::class),
            ],
            'breadcrumbs' => [
                [
                    'title' => 'Elders',
                    'href' => route('elders.index'),
                ],
                [
                    'title' => $elder->name,
                    'href' => route('elders.show', $elder),
                ],
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Elder $elder)
    {
        $elder->load('branch', 'activityLogs.causer');
        $branches = Branch::all(['id', 'name']); // Get all branches for dropdown
        return Inertia::render('Elders/Edit', [
            'elder' => $elder,
            'branches' => $branches,
            'activity' => $elder->activityLogs,
            'breadcrumbs' => [
                [
                    'label' => 'Elders',
                    'url' => route('elders.index'),
                ],
                [
                    'label' => $elder->name,
                    'url' => route('elders.show', $elder),
                ],
                [
                    'label' => 'Edit',
                ],
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateElderRequest $request, Elder $elder)
    {
        $validatedData = $request->validated();

        // Handle profile picture update
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($elder->profile_picture_path) {
                Storage::disk('public')->delete($elder->profile_picture_path);
            }
            $validatedData['profile_picture_path'] = $request->file('profile_picture')->store('elders/profile_pictures', 'public');
        } elseif ($request->boolean('remove_profile_picture') && $elder->profile_picture_path) {
            // Explicitly remove profile picture
            Storage::disk('public')->delete($elder->profile_picture_path);
            $validatedData['profile_picture_path'] = null;
        } else {
            // If no new file and not explicitly removed, retain existing path
            unset($validatedData['profile_picture']); // Remove file object from validated data
        }

        // Handle video update
        if ($request->hasFile('video')) {
            // Delete old video if exists
            if ($elder->video_url) {
                Storage::disk('public')->delete($elder->video_url);
            }
            $validatedData['video_url'] = $request->file('video')->store('elders/videos', 'public');
        } elseif ($request->boolean('remove_video') && $elder->video_url) {
            // Explicitly remove video
            Storage::disk('public')->delete($elder->video_url);
            $validatedData['video_url'] = null;
        } else {
            // If no new file and not explicitly removed, retain existing path
            unset($validatedData['video']); // Remove file object from validated data
        }

        $elder->update($validatedData);
        return redirect()->route('elders.index')->with('success', 'Elder updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Elder $elder)
    {
        if ($elder->profile_picture_path) {
            Storage::disk('public')->delete($elder->profile_picture_path);
        }
        if ($elder->video_url) { // Delete video file
            Storage::disk('public')->delete($elder->video_url);
        }
        $elder->delete();
        return redirect()->route('elders.index')->with('success', 'Elder deleted successfully.');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, Elder::class, ExportConfig::elders(), [
            'label' => 'Elders Directory',
            'type' => 'elders',
        ]);
    }

    /**
     * Display the specified resource for public view.
     */
    public function publicShow(Elder $elder)
    {
        $elder->load('branch');

        return Inertia::render('Elders/PublicShow', [
            'elder' => $elder,
        ]);
    }
}