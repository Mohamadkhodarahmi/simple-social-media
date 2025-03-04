<?php

namespace App\Providers;

use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Post::class => PostPolicy::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
//        $this->registerPolicies();
//
//        Gate::define('viewApiDocs',function ($user){
//            return $user->email === 'admin@gmail.com';
//        });
    }
}
