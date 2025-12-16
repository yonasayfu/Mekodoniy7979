<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use App\Models\Branch; // Import Branch model
use App\Models\Staff;
use App\Models\User;
use App\Models\Warning;
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use App\Support\Users\SyncsStaffAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    use HandlesDataExport;
    use SyncsStaffAssignment;
    public function index(Request $request): Response
    {
        $this->ensureCanManageUsers();

        $search = trim((string) $request->query('search', ''));
        $allowedPerPage = [5, 10, 25, 50, 100];
        $perPage = $request->integer('per_page', 5);
        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 5;
        }
        $sort = $this->resolveSort($request);
        $direction = $request->query('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $query = User::query()->with([
            'roles:id,name',
            'permissions:id,name',
            'staff:id,first_name,last_name,email,status,user_id',
            'approver:id,name',
            'branch:id,name', // Eager load branch
        ]);

        $this->applySearch($query, $search);
        $this->applySorting($query, $request);

        $users = $query
            ->paginate($perPage)
            ->withQueryString()
            ->through(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'branch' => $user->branch ? ['id' => $user->branch->id, 'name' => $user->branch->name] : null, // Include branch
                    'account_status' => $user->account_status,
                    'account_type' => $user->account_type,
                    'approved_at' => optional($user->approved_at)->toIso8601String(),
                    'approved_by' => $user->approver?->name,
                    'is_pending' => $user->account_status === User::STATUS_PENDING,
                    'roles' => $user->roles->pluck('name')->values(),
                    'permissions' => $user->getAllPermissions()->pluck('name')->values(),
                    'has_two_factor' => ! is_null($user->two_factor_secret),
                    'staff' => $user->staff ? [
                        'id' => $user->staff->id,
                        'full_name' => $user->staff->full_name,
                        'status' => $user->staff->status,
                    ] : null,
                ];
            });

        $staffLinkedCount = Staff::whereNotNull('user_id')->count();
        $pendingCount = User::where('account_status', User::STATUS_PENDING)->count();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'stats' => [
                [
                    'label' => 'Total Users',
                    'value' => $users->total(),
                    'tone' => 'primary',
                ],
                [
                    'label' => 'Staff Linked',
                    'value' => $staffLinkedCount,
                    'tone' => 'success',
                ],
                [
                    'label' => 'Roles',
                    'value' => Role::count(),
                    'tone' => 'muted',
                ],
                [
                    'label' => 'Pending Approval',
                    'value' => $pendingCount,
                    'tone' => 'primary',
                ],
            ],
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'can' => [
                'create' => $request->user()->can('users.manage'),
                'edit' => $request->user()->can('users.manage'),
                'delete' => $request->user()->can('users.manage'),
                'impersonate' => $request->user()->can('users.impersonate'),
            ],
            'breadcrumbs' => [
                ['title' => 'Users', 'href' => route('users.index')],
            ],
            'print' => (bool) $request->boolean('print'),
        ]);
    }

    public function export(Request $request)
    {
        $this->ensureCanManageUsers();

        return $this->handleExport($request, User::class, ExportConfig::users(), [
            'label' => 'User Roster',
            'type' => 'users',
        ]);
    }

    public function create(): Response
    {
        $this->ensureCanManageUsers();

        $branches = Branch::all(['id', 'name']); // Fetch branches

        return Inertia::render('Users/Create', [
            'roles' => $this->availableRoles(),
            'permissions' => $this->availablePermissions(),
            'staff' => $this->availableStaff(),
            'branches' => $branches, // Pass branches to the view
            'breadcrumbs' => [
                ['title' => 'Users', 'href' => route('users.index')],
                ['title' => 'Create', 'href' => route('users.create')],
            ],
        ]);
    }

    public function show(Request $request, User $user): Response
    {
        $this->ensureCanManageUsers();

        $user->load([
            'roles:id,name',
            'permissions:id,name',
            'staff:id,first_name,last_name,status,user_id',
            'approver:id,name',
            'branch:id,name', // Eager load branch
        ]);

        $activity = $user->activityLogs()
            ->with('causer')
            ->latest()
            ->take(20)
            ->get();

        return Inertia::render('Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'branch' => $user->branch ? ['id' => $user->branch->id, 'name' => $user->branch->name] : null, // Include branch
                'account_status' => $user->account_status,
                'account_type' => $user->account_type,
                'approved_at' => optional($user->approved_at)->toIso8601String(),
                'approved_by' => $user->approver?->name,
                'roles' => $user->roles->pluck('name')->values(),
                'permissions' => $user->getAllPermissions()->pluck('name')->values(),
                'has_two_factor' => ! is_null($user->two_factor_secret),
                'staff' => $user->staff ? [
                    'id' => $user->staff->id,
                    'full_name' => $user->staff->full_name,
                    'status' => $user->staff->status,
                ] : null,
                'is_kicked' => $user->isKicked(), // Include kick status
                'kicked_at' => optional($user->kicked_at)->toIso8601String(),
                'kicked_until' => optional($user->kicked_until)->toIso8601String(),
                'kick_reason' => $user->kick_reason,
                'is_banned' => $user->isBanned(), // Include ban status
                'banned_at' => optional($user->banned_at)->toIso8601String(),
                'banned_until' => optional($user->banned_until)->toIso8601String(),
                'ban_reason' => $user->ban_reason,
                'is_muted' => $user->isMuted(), // Include mute status
                'muted_at' => optional($user->muted_at)->toIso8601String(),
                'muted_until' => optional($user->muted_until)->toIso8601String(),
                'mute_reason' => $user->mute_reason,
            ],
            'activity' => ActivityLogResource::collection($activity),
            'breadcrumbs' => [
                ['title' => 'Users', 'href' => route('users.index')],
                ['title' => $user->name, 'href' => route('users.show', $user)],
            ],
            'print' => (bool) $request->boolean('print'),
        ]);
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $this->ensureCanManageUsers();

        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $status = $data['account_status'];
            $accountType = $data['account_type'];
            $isActive = $status === User::STATUS_ACTIVE;

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
                'account_status' => $status,
                'account_type' => $accountType,
                'branch_id' => $data['branch_id'], // Save branch_id
                'approved_at' => $isActive ? now() : null,
                'approved_by' => $request->user()->id,
            ]);

            $user->syncRoles($data['roles'] ?? []);
            $user->syncPermissions($data['permissions'] ?? []);

            $this->syncStaffAssignment($user, $data['staff_id'] ?? null);
        });

        return redirect()
            ->route('users.index')
            ->with('bannerStyle', 'success')
            ->with('banner', 'User created successfully.');
    }

    public function edit(User $user): Response
    {
        $this->ensureCanManageUsers();

        $user->load(['roles:id,name', 'permissions:id,name', 'staff:id', 'approver:id,name', 'branch:id,name']); // Eager load branch

        $activity = $user->activityLogs()
            ->with('causer')
            ->latest()
            ->take(20)
            ->get();

        $branches = Branch::all(['id', 'name']); // Fetch branches

        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'branch_id' => $user->branch_id, // Include branch_id
                'branch' => $user->branch ? ['id' => $user->branch->id, 'name' => $user->branch->name] : null, // Include branch
                'account_status' => $user->account_status,
                'account_type' => $user->account_type,
                'approved_at' => optional($user->approved_at)->toIso8601String(),
                'approved_by' => $user->approver?->name,
                'roles' => $user->roles->pluck('name')->values(),
                'permissions' => $user->getAllPermissions()->pluck('name')->values(),
                'staff_id' => $user->staff?->id,
                'is_kicked' => $user->isKicked(), // Include kick status
                'kicked_at' => optional($user->kicked_at)->toIso8601String(),
                'kicked_until' => optional($user->kicked_until)->toIso8601String(),
                'kick_reason' => $user->kick_reason,
                'is_banned' => $user->isBanned(), // Include ban status
                'banned_at' => optional($user->banned_at)->toIso8601String(),
                'banned_until' => optional($user->banned_until)->toIso8601String(),
                'ban_reason' => $user->ban_reason,
                'is_muted' => $user->isMuted(), // Include mute status
                'muted_at' => optional($user->muted_at)->toIso8601String(),
                'muted_until' => optional($user->muted_until)->toIso8601String(),
                'mute_reason' => $user->mute_reason,
            ],
            'roles' => $this->availableRoles(),
            'permissions' => $this->availablePermissions(),
            'staff' => $this->availableStaff($user),
            'activity' => ActivityLogResource::collection($activity),
            'breadcrumbs' => [
                ['title' => 'Users', 'href' => route('users.index')],
                ['title' => $user->name, 'href' => route('users.edit', $user)],
            ],
        ]);
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $this->ensureCanManageUsers();

        $oldRoles = $user->roles->pluck('name')->sort()->values()->toArray();
        $oldPermissions = $user->getAllPermissions()->pluck('name')->sort()->values()->toArray();
        $oldStatus = $user->account_status;
        $oldType = $user->account_type;
        $oldApprovedAt = $user->approved_at;
        $oldBranchId = $user->branch_id; // Get old branch_id

        $newRoles = $oldRoles;
        $newPermissions = $oldPermissions;
        $newStatus = $oldStatus;
        $newType = $oldType;
        $newApprovedAt = $oldApprovedAt;
        $newBranchId = $oldBranchId; // Initialize new branch_id

        DB::transaction(function () use ($request, $user, &$newRoles, &$newPermissions, &$newStatus, &$newType, &$newApprovedAt, &$newBranchId) {
            $data = $request->validated();
            $status = $data['account_status'];
            $accountType = $data['account_type'];
            $branchId = $data['branch_id']; // Get new branch_id

            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'account_status' => $status,
                'account_type' => $accountType,
                'branch_id' => $branchId, // Update branch_id
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            if ($user->account_status !== $status) {
                if ($status === User::STATUS_ACTIVE) {
                    $payload['approved_at'] = now();
                    $payload['approved_by'] = $request->user()->id;
                } else {
                    $payload['approved_at'] = null;
                    $payload['approved_by'] = null;
                }
            }

            $user->update($payload);

            $user->syncRoles($data['roles'] ?? []);
            $user->syncPermissions($data['permissions'] ?? []);

            $this->syncStaffAssignment($user, $data['staff_id'] ?? null);

            $user->refresh()->load('roles');
            $newRoles = $user->roles->pluck('name')->sort()->values()->toArray();
            $newPermissions = $user->getAllPermissions()->pluck('name')->sort()->values()->toArray();
            $newStatus = $user->account_status;
            $newType = $user->account_type;
            $newApprovedAt = $user->approved_at;
            $newBranchId = $user->branch_id; // Get updated branch_id
        });

        if ($oldRoles !== $newRoles || $oldPermissions !== $newPermissions) {
            ActivityLog::record(
                auth()->id(),
                $user->fresh(),
                'roles.updated',
                'Roles or permissions updated',
                [
                    'before' => [
                        'roles' => $oldRoles,
                        'permissions' => $oldPermissions,
                    ],
                    'after' => [
                        'roles' => $newRoles,
                        'permissions' => $newPermissions,
                    ],
                ]
            );
        }

        if ($oldStatus !== $newStatus || $oldType !== $newType || $oldBranchId !== $newBranchId) { // Check for branch_id change
            ActivityLog::record(
                auth()->id(),
                $user->fresh(),
                'user.account_status.updated',
                'Account status updated',
                [
                    'before' => [
                        'status' => $oldStatus,
                        'type' => $oldType,
                        'approved_at' => optional($oldApprovedAt)->toDateTimeString(),
                        'branch_id' => $oldBranchId, // Include old branch_id
                    ],
                    'after' => [
                        'status' => $newStatus,
                        'type' => $newType,
                        'approved_at' => optional($newApprovedAt)->toDateTimeString(),
                        'branch_id' => $newBranchId, // Include new branch_id
                    ],
                ]
            );
        }

        return redirect()
            ->route('users.index')
            ->with('bannerStyle', 'success')
            ->with('banner', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->ensureCanManageUsers();

        if ($request->user()->is($user)) {
            return back()
                ->with('bannerStyle', 'warning')
                ->with('banner', 'You cannot delete your own account.');
        }

        DB::transaction(function () use ($user) {
            $this->syncStaffAssignment($user, null);
            $user->delete();
        });

        return redirect()
            ->route('users.index')
            ->with('bannerStyle', 'info')
            ->with('banner', 'User removed.');
    }

    /**
     * Kick a user from the system.
     */
    public function kickUser(Request $request, User $user): RedirectResponse
    {
        $this->ensureCanManageUsers();

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'kicked_until' => ['nullable', 'date', 'after:now'],
        ]);

        $user->kick($request->input('reason'), $request->input('kicked_until'));

        return back()
            ->with('bannerStyle', 'warning')
            ->with('banner', "User {$user->name} has been kicked.");
    }

    /**
     * Unkick a user from the system.
     */
    public function unkickUser(User $user): RedirectResponse
    {
        $this->ensureCanManageUsers();

        $user->unkick();

        return back()
            ->with('bannerStyle', 'success')
            ->with('banner', "User {$user->name} has been unkicked.");
    }

    /**
     * Ban a user from the system.
     */
    public function banUser(Request $request, User $user): RedirectResponse
    {
        $this->ensureCanManageUsers();

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'banned_until' => ['nullable', 'date', 'after:now'],
        ]);

        $user->ban($request->input('reason'), $request->input('banned_until'));

        return back()
            ->with('bannerStyle', 'danger')
            ->with('banner', "User {$user->name} has been banned.");
    }

    /**
     * Unban a user from the system.
     */
    public function unbanUser(User $user): RedirectResponse
    {
        $this->ensureCanManageUsers();

        $user->unban();

        return back()
            ->with('bannerStyle', 'success')
            ->with('banner', "User {$user->name} has been unbanned.");
    }

    /**
     * Mute a user in the system.
     */
    public function muteUser(Request $request, User $user): RedirectResponse
    {
        $this->ensureCanManageUsers();

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'muted_until' => ['nullable', 'date', 'after:now'],
        ]);

        $user->mute($request->input('reason'), $request->input('muted_until'));

        return back()
            ->with('bannerStyle', 'warning')
            ->with('banner', "User {$user->name} has been muted.");
    }

    /**
     * Unmute a user in the system.
     */
    public function unmuteUser(User $user): RedirectResponse
    {
        $this->ensureCanManageUsers();

        $user->unmute();

        return back()
            ->with('bannerStyle', 'success')
            ->with('banner', "User {$user->name} has been unmuted.");
    }

    /**
     * Issue a warning to a user.
     */
    public function issueWarning(Request $request, User $user): RedirectResponse
    {
        $this->ensureCanManageUsers(); // Or a more specific permission like 'warnings.issue'

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $user->warnings()->create([
            'moderator_id' => $request->user()->id,
            'reason' => $validated['reason'],
            'expires_at' => $validated['expires_at'],
        ]);

        return back()
            ->with('bannerStyle', 'warning')
            ->with('banner', "Warning issued to user {$user->name}.");
    }

    /**
     * Display a paginated list of warnings for a specific user.
     */
    public function showWarnings(Request $request, User $user): Response
    {
        $this->ensureCanManageUsers(); // Or a more specific permission like 'warnings.view'

        $warnings = $user->warnings()
            ->with('moderator:id,name') // Eager load the moderator who issued the warning
            ->latest()
            ->paginate(10); // Paginate with 10 warnings per page

        return Inertia::render('Users/WarningsIndex', [ // Assuming a new Vue component for warnings
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'warnings' => $warnings->through(fn ($warning) => [
                'id' => $warning->id,
                'reason' => $warning->reason,
                'issued_at' => optional($warning->issued_at)->toIso8601String(),
                'expires_at' => optional($warning->expires_at)->toIso8601String(),
                'moderator' => $warning->moderator ? [
                    'id' => $warning->moderator->id,
                    'name' => $warning->moderator->name,
                ] : null,
            ]),
            'breadcrumbs' => [
                ['title' => 'Users', 'href' => route('users.index')],
                ['title' => $user->name, 'href' => route('users.show', $user)],
                ['title' => 'Warnings', 'href' => route('users.warnings.index', $user)],
            ],
        ]);
    }

    protected function applySearch(Builder $query, ?string $search): void
    {
        $term = trim((string) $search);

        if ($term === '') {
            return;
        }

        $query->where(function (Builder $builder) use ($term) {
            $builder
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        });
    }

    protected function applySorting(Builder $query, Request $request): void
    {
        $sort = $this->resolveSort($request);
        $direction = $request->query('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        if ($sort) {
            $query->orderBy($sort, $direction);

            return;
        }

        $query->orderBy('name');
    }

    protected function resolveSort(Request $request): ?string
    {
        $sort = $request->query('sort');
        $sortable = ['name', 'email', 'created_at'];

        return in_array($sort, $sortable, true) ? $sort : null;
    }

    protected function availableRoles(): array
    {
        return Role::orderBy('name')
            ->get()
            ->map(fn (Role $role) => $role->name)
            ->values()
            ->toArray();
    }

    protected function availablePermissions(): array
    {
        return Permission::orderBy('name')
            ->get()
            ->map(fn (Permission $permission) => $permission->name)
            ->values()
            ->toArray();
    }

    protected function availableStaff(?User $user = null): array
    {
        return Staff::orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function (Staff $staff) use ($user) {
                return [
                    'id' => $staff->id,
                    'label' => $staff->full_name,
                    'status' => $staff->status,
                    'linked_user_id' => $staff->user_id,
                    'linked_to_current_user' => $user ? $staff->user_id === $user->id : false,
                ];
            })
            ->values()
            ->toArray();
    }

    protected function ensureCanManageUsers(): void
    {
        abort_unless(auth()->user()?->can('users.manage'), 403);
    }
}
