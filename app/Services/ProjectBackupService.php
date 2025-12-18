<?php

namespace App\Services;

use ZipArchive;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectBackupService
{
    public function create(string $disk = 'local'): array
    {
        $project = basename(base_path());
        $stamp = now()->format('Ymd_His');
        $filename = "{$project}_FULL_{$stamp}.zip";
        $relativePath = "backups/{$filename}";

        Storage::disk($disk)->makeDirectory('backups');
        $zipFullPath = Storage::disk($disk)->path($relativePath);

        $zip = new ZipArchive();
        if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create zip file: {$zipFullPath}");
        }

        $base = base_path();

        /**
         * ✅ Всегда сравниваем пути в одном формате: только "/"
         * Тогда исключения будут работать и на macOS/Linux, и на Windows.
         */
        $excludeDirPrefixes = [
            '.git/',
            'storage/app/private/backups/',     // ✅ главное
        ];

        $excludeExactFiles = [
            // '.env', // ✅ НЕ исключаем, раз ты хочешь включать
        ];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $full = $file->getPathname();

            // relative path от корня проекта
            $relative = Str::of($full)
                ->replaceFirst($base . DIRECTORY_SEPARATOR, '')
                ->toString();

            // ✅ нормализация слешей
            $relative = str_replace('\\', '/', $relative);

            // ✅ исключение по префиксу папок
            foreach ($excludeDirPrefixes as $prefix) {
                if (Str::startsWith($relative, $prefix)) {
                    continue 2;
                }
            }

            // ✅ исключение точечных файлов
            foreach ($excludeExactFiles as $bad) {
                if ($relative === $bad) {
                    continue 2;
                }
            }

            $zip->addFile($full, $relative);
        }

        $zip->close();

        $size = @filesize($zipFullPath) ?: 0;

        return [
            'project' => $project,
            'filename' => $filename,
            'disk' => $disk,
            'path' => $relativePath,
            'size_bytes' => $size,
        ];
    }
}
