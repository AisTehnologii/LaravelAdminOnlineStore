<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    private function isSuper(User $user): bool
    {
        return ($user->is_admin ?? 0) == 1
            || $user->hasAnyRole(['admin', 'SUPER_ADMIN']);
    }

    public function view(User $user, Conversation $conversation): bool
    {
        if ($this->isSuper($user)) return true;
        if ($user->can('chat.view_all')) return true;

        return $conversation->participants()
            ->where('users.id', $user->id)
            ->exists();
    }

    public function sendMessage(User $user, Conversation $conversation): bool
{
    return $this->view($user, $conversation);
}


    public function createGroup(User $user): bool
    {
        if ($this->isSuper($user)) return true;

        return $user->can('chat.create_group');
    }

    public function manageParticipants(User $user, Conversation $conversation): bool
    {
        if ($this->isSuper($user)) return true;
        if ($user->can('chat.view_all')) return true;

        if (! $user->can('chat.manage_participants')) return false;

        return $conversation->participants()
            ->where('users.id', $user->id)
            ->where('conversation_participants.role', 'owner')
            ->exists();
    }
}
