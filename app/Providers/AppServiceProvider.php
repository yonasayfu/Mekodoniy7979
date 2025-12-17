<?php

namespace App\Providers;

use App\Models\Staff;
use App\Policies\StaffPolicy;
use App\Models\ActivityLog;
use App\Policies\ActivityLogPolicy;
use App\Policies\ReportPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Staff::class, StaffPolicy::class);
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);

        Gate::define('reports.view', [ReportPolicy::class, 'viewAny']);
        Gate::define('reports.generate_impact_book', [ReportPolicy::class, 'generateImpactBook']);

        RateLimiter::for('mailpit', function (Request $request) {
            return [
                Limit::perMinute(30)->by('mailpit-'.$request->ip()),
            ];
        });
    }
}

