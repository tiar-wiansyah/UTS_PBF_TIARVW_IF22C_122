<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Guards\JWTGuard;
use App\Services\JWTService;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Auth::extend('jwt-driver', function ($app, $name, array $config) {
            $guard = new JWTGuard(
                userProvider: Auth::createUserProvider($config['provider']),
                request: $app->make(Request::class),
                jwtService: $app->make(JWTService::class)
            );
            $app->refresh('request', $guard, 'setRequest');
            return $guard;
        });
    }
}
