<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        \App\Models\Conversation::class => \App\Policies\ConversationPolicy::class,
        \App\Models\Task::class => \App\Policies\TaskPolicy::class,
    \App\Models\TaskComment::class => \App\Policies\TaskCommentPolicy::class,
    \App\Models\Product::class => \App\Policies\ProductPolicy::class,
    ];
   


    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('is-admin', function (User $user) {
            return (bool) $user->is_admin;
            Gate::before(fn ($user) => $user->is_admin ? true : null);
        });
    }
}
