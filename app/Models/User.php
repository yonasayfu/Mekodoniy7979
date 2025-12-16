<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Lab404\Impersonate\Models\Impersonate;
use Lab404\Impersonate\Services\ImpersonateManager;
use App\Scopes\BranchScope; // Import BranchScope
use App\Models\Warning; // Import Warning model

use Illuminate\Support\Carbon; // Import Carbon for date manipulation

class User extends Authenticatable
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    public const TYPE_EXTERNAL = 'external';
    public const TYPE_INTERNAL = 'internal';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Impersonate;
    use Notifiable;
    use RecordsActivity;
    use TwoFactorAuthenticatable;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Removed BranchScope to avoid recursion during authentication
        // static::addGlobalScope(new BranchScope);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'branch_id', // Add branch_id here
        'name',
        'email',
        'password',
        'recovery_email',
        'phone_number',
        'account_status',
        'account_type',
        'approved_at',
        'approved_by',
        'address',
        'city',
        'country',
        'date_of_birth',
        'gender',
        'kicked_at', // Added for kick system
        'kicked_until', // Added for kick system
        'kick_reason', // Added for kick system
        'banned_at', // Added for ban system
        'banned_until', // Added for ban system
        'ban_reason', // Added for ban system
        'muted_at', // Added for mute system
        'muted_until', // Added for mute system
        'mute_reason', // Added for mute system
    ];

    /**
     * Get the branch that owns the user.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the donations for the user.
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Get the pledges made by the user.
     */
    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    /**
     * Get the warnings issued to the user.
     */
    public function warnings(): HasMany
    {
        return $this->hasMany(Warning::class);
    }



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $appends = [
        'two_factor_secret',
        'two_factor_recovery_codes',
        'is_impersonating',
        'impersonated_by_name',
    ];
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_email_recovery_codes',
        'remember_token',
    ];

    protected string $activityLogLabel = 'User';

    protected array $activityLogAttributes = [
        'name',
        'email',
        'account_status',
        'account_type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'approved_at' => 'datetime',
            'date_of_birth' => 'date',
            'gender' => 'string',
            'kicked_at' => 'datetime', // Cast for kick system
            'kicked_until' => 'datetime', // Cast for kick system
            'banned_at' => 'datetime', // Cast for ban system
            'banned_until' => 'datetime', // Cast for ban system
            'muted_at' => 'datetime', // Cast for mute system
            'muted_until' => 'datetime', // Cast for mute system
        ];
    }

    public function getIsImpersonatingAttribute(): bool
    {
        /** @var ImpersonateManager $manager */
        $manager = app(ImpersonateManager::class);

        return $manager->isImpersonating();
    }

    public function getImpersonatedByNameAttribute(): ?string
    {
        /** @var ImpersonateManager $manager */
        $manager = app(ImpersonateManager::class);

        if (!$manager->isImpersonating() || auth()->id() !== $this->getKey()) {
            return null;
        }

        $impersonatorId = $manager->getImpersonatorId();

        if (!$impersonatorId) {
            return null;
        }

        static $impersonatorName;

        if ($impersonatorName === null) {
            $impersonatorName = static::query()
                ->select('name')
                ->find($impersonatorId)
                ?->name;
        }

        return $impersonatorName;
    }

    /**
     * Get the staff profile associated with the user.
     */
    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class);
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(UserNotificationPreference::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(self::class, 'approved_by');
    }

    public function getTwoFactorEmailRecoveryCodesAttribute(?string $value): array
    {
        if (! $value) {
            return [];
        }

        try {
            $decoded = json_decode(decrypt($value), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            return [];
        }

        if (! is_array($decoded)) {
            return [];
        }

        return array_values(
            array_filter($decoded, fn ($code) => is_string($code) && $code !== '')
        );
    }

    public function setTwoFactorEmailRecoveryCodesAttribute(?array $codes): void
    {
        if (empty($codes)) {
            $this->attributes['two_factor_email_recovery_codes'] = null;

            return;
        }

        $normalized = array_values(
            array_filter($codes, fn ($code) => is_string($code) && $code !== '')
        );

        if (! $normalized) {
            $this->attributes['two_factor_email_recovery_codes'] = null;

            return;
        }

        try {
            $encoded = json_encode($normalized, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $encoded = json_encode($normalized);
        }

        $this->attributes['two_factor_email_recovery_codes'] = encrypt($encoded);
    }

    /**
     * Check if the user is currently kicked.
     */
    public function isKicked(): bool
    {
        return $this->kicked_at !== null && ($this->kicked_until === null || $this->kicked_until->isFuture());
    }

    /**
     * Kick the user for a specified reason and duration.
     */
    public function kick(string $reason, ?Carbon $until = null): void
    {
        $this->update([
            'kicked_at' => now(),
            'kicked_until' => $until,
            'kick_reason' => $reason,
        ]);
    }

    /**
     * Unkick the user.
     */
    public function unkick(): void
    {
        $this->update([
            'kicked_at' => null,
            'kicked_until' => null,
            'kick_reason' => null,
        ]);
    }

    /**
     * Check if the user is currently banned.
     */
    public function isBanned(): bool
    {
        return $this->banned_at !== null && ($this->banned_until === null || $this->banned_until->isFuture());
    }

    /**
     * Ban the user for a specified reason and duration.
     */
    public function ban(string $reason, ?Carbon $until = null): void
    {
        $this->update([
            'banned_at' => now(),
            'banned_until' => $until,
            'ban_reason' => $reason,
        ]);
    }

    /**
     * Unban the user.
     */
    public function unban(): void
    {
        $this->update([
            'banned_at' => null,
            'banned_until' => null,
            'ban_reason' => null,
        ]);
    }

    /**
     * Check if the user is currently muted.
     */
    public function isMuted(): bool
    {
        return $this->muted_at !== null && ($this->muted_until === null || $this->muted_until->isFuture());
    }

    /**
     * Mute the user for a specified reason and duration.
     */
    public function mute(string $reason, ?Carbon $until = null): void
    {
        $this->update([
            'muted_at' => now(),
            'muted_until' => $until,
            'mute_reason' => $reason,
        ]);
    }

    /**
     * Unmute the user.
     */
    public function unmute(): void
    {
        $this->update([
            'muted_at' => null,
            'muted_until' => null,
            'mute_reason' => null,
        ]);
    }
}

