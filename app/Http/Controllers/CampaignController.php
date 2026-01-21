<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Elder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

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
            'short_description' => ['nullable', 'string', 'max:500'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'goal_amount' => ['nullable', 'numeric', 'min:0'],
            'goal_currency' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'string', 'in:draft,active,ended'],
            'cta_label' => ['nullable', 'string', 'max:120'],
            'cta_url' => ['nullable', 'url'],
            'accent_color' => ['nullable', 'string', 'max:20'],
            'featured_video_url' => ['nullable', 'url'],
            'hero_image' => ['nullable', 'image', 'max:3072'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['created_by'] = $request->user()?->id;
        $data['goal_currency'] = $data['goal_currency'] ?: 'ETB';

        if ($request->hasFile('hero_image')) {
            $data['hero_image_path'] = $request->file('hero_image')->store('campaigns/heroes', 'public');
        }

        $data['cta_label'] = $data['cta_label'] ?: 'Support this campaign';
        $data['accent_color'] = $data['accent_color'] ?: '#2563eb';
        unset($data['hero_image']);

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
            'short_description' => ['nullable', 'string', 'max:500'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'goal_amount' => ['nullable', 'numeric', 'min:0'],
            'goal_currency' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'string', 'in:draft,active,ended'],
            'cta_label' => ['nullable', 'string', 'max:120'],
            'cta_url' => ['nullable', 'url'],
            'accent_color' => ['nullable', 'string', 'max:20'],
            'featured_video_url' => ['nullable', 'url'],
            'hero_image' => ['nullable', 'image', 'max:3072'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if (empty($data['goal_currency'])) {
            $data['goal_currency'] = 'ETB';
        }

        if ($request->hasFile('hero_image')) {
            if ($campaign->hero_image_path) {
                Storage::disk('public')->delete($campaign->hero_image_path);
            }
            $data['hero_image_path'] = $request->file('hero_image')->store('campaigns/heroes', 'public');
        }

        $data['cta_label'] = $data['cta_label'] ?: 'Support this campaign';
        $data['accent_color'] = $data['accent_color'] ?: '#2563eb';
        unset($data['hero_image']);

        $campaign->update($data);

        return redirect()->route('campaigns.index')->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted successfully.');
    }

    public function landing(Campaign $campaign)
    {
        if ($campaign->status !== 'active') {
            abort(404);
        }

        $raisedAmount = $campaign->donations()
            ->where('status', 'completed')
            ->sum('amount');

        $donorCount = $campaign->donations()
            ->where('status', 'completed')
            ->count();

        $goalAmount = (float) ($campaign->goal_amount ?? 0);
        $progressPercent = $goalAmount > 0 ? min(100, round(($raisedAmount / $goalAmount) * 100)) : null;

        $urgentElders = Elder::query()
            ->with('branch')
            ->where('current_status', 'available')
            ->orderByRaw("FIELD(priority_level, 'high', 'medium', 'low')")
            ->take(3)
            ->get()
            ->map(fn ($elder) => [
                'id' => $elder->id,
                'name' => $elder->name,
                'priority_level' => $elder->priority_level,
                'profile_photo_url' => $elder->profile_photo_url,
                'branch' => $elder->branch?->name,
            ]);

        return Inertia::render('Campaigns/Landing', [
            'campaign' => [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'slug' => $campaign->slug,
                'description' => $campaign->description,
                'short_description' => $campaign->short_description,
                'cta_label' => $campaign->cta_label ?? 'Support this campaign',
                'cta_url' => $campaign->cta_url ?? route('guest.donation', ['campaign_id' => $campaign->id], false),
                'accent_color' => $campaign->accent_color ?? '#2563eb',
                'hero_image_url' => $campaign->hero_image_url,
                'goal_amount' => $campaign->goal_amount,
                'goal_currency' => $campaign->goal_currency,
                'raised_amount' => $raisedAmount,
                'donor_count' => $donorCount,
                'progress_percent' => $progressPercent,
                'featured_video_url' => $campaign->featured_video_url,
            ],
            'urgentElders' => $urgentElders,
            'share' => [
                'url' => route('campaigns.landing', ['campaign' => $campaign->slug], true),
            ],
        ]);
    }
}
