<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider {
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot() {
        $this->registerPolicies();
        
        Gate::define('canRead', function (User $user, $model) {
            return (($user->isAdmin) || ($user->{"canRead$model"} || $user->{"canWrite$model"} || $user->{"canUpdate$model"}));
        });

        Gate::define('canWrite', function (User $user, $model) {
            return (($user->isAdmin) || ($user->{"canWrite$model"}));
        });

        Gate::define('canUpdate', function (User $user, $model) {
            return (($user->isAdmin) || ($user->{"canUpdate$model"}));
        });
    }
}