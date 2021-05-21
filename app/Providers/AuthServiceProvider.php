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
        
        Gate::define('canRead', function (User $user, $method, $model) {
            return (($user->isAdmin === 1) || ($user->{"canRead$model"} === 1 || $user->{"canWrite$model"} === 1 || $user->{"canUpdate$model"} === 1));
        });

        Gate::define('canUpdate', function (User $user, $method, $model) {
            return (($user->isAdmin === 1) || ($user->{"canUpdate$model"} === 1));
        });

        Gate::define('canWrite', function (User $user, $method, $model) {
            return (($user->isAdmin === 1) || ($user->{"canWrite$model"} === 1));
        });
    }
}