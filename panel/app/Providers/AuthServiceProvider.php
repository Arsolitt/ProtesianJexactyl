<?php

namespace Jexactyl\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Jexactyl\Models\ApiKey;
use Jexactyl\Models\Server;
use Jexactyl\Policies\ServerPolicy;
use Laravel\Sanctum\Sanctum;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        Server::class => ServerPolicy::class,
    ];

    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(ApiKey::class);

        $this->registerPolicies();
    }

    public function register(): void
    {
        Sanctum::ignoreMigrations();
    }
}
