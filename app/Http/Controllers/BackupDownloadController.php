<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BackupDownloadController extends Controller
{
    public function __invoke(Backup $backup)
    {
        // ✅ ТОЛЬКО SUPER_ADMIN
        abort_unless(
            Auth::check() && (int) Auth::user()->is_admin === 1,
            403
        );

        $fullPath = Storage::disk($backup->disk)->path($backup->path);

        abort_unless(is_file($fullPath), 404);

        return response()->download(
            $fullPath,
            $backup->filename,
            ['Content-Type' => 'application/zip']
        );
    }
}
