<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    use HandlesDataExport;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::paginate(10);
        return Inertia::render('Branches/Index', [
            'branches' => $branches,
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
        return Inertia::render('Branches/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBranchRequest $request)
    {
        Branch::create($request->validated());
        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        $branch->load('activityLogs.causer');

        return Inertia::render('Branches/Show', [
            'branch' => $branch,
            'activity' => $branch->activityLogs,
            'breadcrumbs' => [
                [
                    'label' => 'Branches',
                    'url' => route('branches.index'),
                ],
                [
                    'label' => $branch->name,
                ],
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        $branch->load('activityLogs.causer');

        return Inertia::render('Branches/Edit', [
            'branch' => $branch,
            'activity' => $branch->activityLogs,
            'breadcrumbs' => [
                [
                    'label' => 'Branches',
                    'url' => route('branches.index'),
                ],
                [
                    'label' => $branch->name,
                    'url' => route('branches.show', $branch),
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
    public function update(UpdateBranchRequest $request, Branch $branch)
    {
        $branch->update($request->validated());
        return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Branch deleted successfully.');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, Branch::class, ExportConfig::branches(), [
            'label' => 'Branch Directory',
            'type' => 'branches',
        ]);
    }
}