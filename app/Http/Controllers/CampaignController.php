<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = max(5, min($perPage, 50));

        $sort = (string) $request->input('sort', 'created_at');
        $direction = strtolower((string) $request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['title', 'status', 'starts_at', 'ends_at', 'created_at'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        $query = Campaign::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        $campaigns = $query
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
            'can' => [
                'create' => request()->user()?->can('campaigns.manage') ?? false,
                'edit' => request()->user()?->can('campaigns.manage') ?? false,
                'delete' => request()->user()?->can('campaigns.manage') ?? false,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Campaigns/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:campaigns,slug'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'goal_amount' => ['nullable', 'numeric', 'min:0'],
            'goal_currency' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'string', 'in:draft,active,ended'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['created_by'] = $request->user()?->id;
        $data['goal_currency'] = $data['goal_currency'] ?: 'ETB';

        Campaign::create($data);

        return redirect()->route('campaigns.index')->with('success', 'Campaign created successfully.');
    }

    public function show(Campaign $campaign)
    {
        return Inertia::render('Campaigns/Show', [
            'campaign' => $campaign,
            'breadcrumbs' => [
                ['title' => 'Campaigns', 'href' => route('campaigns.index')],
                ['title' => $campaign->title, 'href' => route('campaigns.show', $campaign)],
            ],
        ]);
    }

    public function edit(Campaign $campaign)
    {
        return Inertia::render('Campaigns/Edit', [
            'campaign' => $campaign,
            'breadcrumbs' => [
                ['title' => 'Campaigns', 'href' => route('campaigns.index')],
                ['title' => $campaign->title, 'href' => route('campaigns.show', $campaign)],
                ['title' => 'Edit', 'href' => route('campaigns.edit', $campaign)],
            ],
        ]);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:campaigns,slug,' . $campaign->id],
            'description' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'goal_amount' => ['nullable', 'numeric', 'min:0'],
            'goal_currency' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'string', 'in:draft,active,ended'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if (empty($data['goal_currency'])) {
            $data['goal_currency'] = 'ETB';
        }

        $campaign->update($data);

        return redirect()->route('campaigns.index')->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted successfully.');
    }
}
