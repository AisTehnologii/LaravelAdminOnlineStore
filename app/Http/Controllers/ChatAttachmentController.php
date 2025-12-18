<?php

namespace App\Http\Controllers;

use App\Models\MessageAttachment;
use Illuminate\Support\Facades\Storage;

class ChatAttachmentController extends Controller
{
    public function download(MessageAttachment $attachment)
    {
        // ✅ доступ: либо участник диалога, либо chat.view_all
        $user = auth()->user();
        $conversation = $attachment->message->conversation;

        $isParticipant = $conversation->participants()
            ->where('users.id', $user->id)
            ->exists();

        abort_unless($user->can('chat.view_all') || $isParticipant, 403);

        // ✅ download с оригинальным именем
        return Storage::disk($attachment->disk)->download(
            $attachment->path,
            $attachment->original_name
        );
    }
}
