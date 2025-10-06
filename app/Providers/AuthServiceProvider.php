<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();

        // Define role-based gates
        Gate::define('isOrganiser', function (User $user) {
            return $user->role === 'organiser';
        });

        Gate::define('isAttendee', function (User $user) {
            return $user->role === 'attendee';
        });
    }
}
