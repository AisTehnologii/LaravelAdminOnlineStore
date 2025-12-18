<?php

namespace App\Policies;

use App\Models\TaskComment;
use App\Models\User;

class TaskCommentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TaskComment $comment): bool
    {
        if ($user->can('chat.view_all')) {
            return true;
        }

        $task = $comment->task;

        return (int) $task->created_by === (int) $user->id
            || (int) $task->assigned_to === (int) $user->id;
    }

    public function create(User $user): bool
    {
        // Проверку "участник задачи или нет" делаем в RelationManager через query / record,
        // но базово true, а Shield решает через permission task_comment.create
        return true;
    }

    public function update(User $user, TaskComment $comment): bool
    {
        if ($user->can('chat.view_all')) {
            return true;
        }

        // Редактировать только свой комментарий
        return (int) $comment->user_id === (int) $user->id;
    }

    public function delete(User $user, TaskComment $comment): bool
    {
        if ($user->can('chat.view_all')) {
            return true;
        }

        // Удалять только свой комментарий
        return (int) $comment->user_id === (int) $user->id;
    }

    public function restore(User $user, TaskComment $comment): bool
    {
        return $user->can('chat.view_all');
    }

    public function forceDelete(User $user, TaskComment $comment): bool
    {
        return $user->can('chat.view_all');
    }
}
