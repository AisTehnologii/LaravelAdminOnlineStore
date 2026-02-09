<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Conversation::class => \App\Policies\ConversationPolicy::class,
        \App\Models\Task::class => \App\Policies\TaskPolicy::class,
        \App\Models\TaskComment::class => \App\Policies\TaskCommentPolicy::class,
        \App\Models\Product::class => \App\Policies\ProductPolicy::class,
        \App\Models\Coupon::class => \App\Policies\CouponPolicy::class,
        // если есть RevisionPolicy — добавь тоже
        // \App\Models\Revision::class => \App\Policies\RevisionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // ✅ ВОТ ТУТ должен быть bypass, отдельно, не внутри define()
        Gate::before(function (User $user, string $ability) {
            return $user->is_admin ? true : null;
        });

        // (не обязательно) просто “флажок” способности
        Gate::define('is-admin', fn (User $user) => (bool) $user->is_admin);
    }
}
