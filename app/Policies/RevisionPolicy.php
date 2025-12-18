<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Revision;
use Illuminate\Auth\Access\HandlesAuthorization;

class RevisionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_revision');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Revision $revision): bool
    {
        return $user->can('view_revision');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_revision');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Revision $revision): bool
    {
        return $user->can('update_revision');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Revision $revision): bool
    {
        return $user->can('delete_revision');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_revision');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Revision $revision): bool
    {
        return $user->can('force_delete_revision');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_revision');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Revision $revision): bool
    {
        return $user->can('restore_revision');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_revision');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Revision $revision): bool
    {
        return $user->can('replicate_revision');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_revision');
    }
}
