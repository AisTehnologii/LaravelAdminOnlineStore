<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponPolicy
{
    use HandlesAuthorization;

    /**
     * Если у тебя уже есть Gate::before для SUPER_ADMIN (is_admin=1),
     * то этот блок можно не писать. Но он не мешает.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ((int)($user->is_admin ?? 0) === 1) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_coupon');
    }

    public function view(User $user, Coupon $coupon): bool
    {
        return $user->can('view_coupon');
    }

    public function create(User $user): bool
    {
        return $user->can('create_coupon');
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return $user->can('update_coupon');
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->can('delete_coupon');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_coupon');
    }

    public function restore(User $user, Coupon $coupon): bool
    {
        return $user->can('restore_coupon');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_coupon');
    }

    public function forceDelete(User $user, Coupon $coupon): bool
    {
        return $user->can('force_delete_coupon');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_coupon');
    }
}
