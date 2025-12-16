<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreElderRequest;
use App\Http\Requests\UpdateElderRequest;
use App\Models\Elder;
use App\Models\Branch; // Import Branch model
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage; // Import Storage facade

class ElderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $elders = Elder::with('branch')->paginate(10);
        return Inertia::render('Elders/Index', [
            'elders' => $elders,
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
        return Inertia::render('Elders/Show', [
            'elder' => $elder->load('branch'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Elder $elder)
    {
        $branches = Branch::all(['id', 'name']); // Get all branches for dropdown
        return Inertia::render('Elders/Edit', [
            'elder' => $elder->load('branch'),
            'branches' => $branches,
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
}