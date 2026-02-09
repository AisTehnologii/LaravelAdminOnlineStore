<?php

namespace App\Services\OneC;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OneCExchangeService
{
    public function __construct(
        private CommerceMlImportService $importer
    ) {}

    public function handle(Request $request, string $type, string $mode): Response
    {
        Log::channel('onec')->info('[1C] exchange', [
            'type'  => $type,
            'mode'  => $mode,
            'query' => $request->query(),
        ]);

        return match ($mode) {
            'checkauth' => $this->checkauth($request),
            'init'      => $this->init($request),
            'file'      => $this->file($request, $type),
            'import'    => $this->import($request, $type),
            'query'     => $this->exportQuery($request),
            'success'   => $this->exportSuccess($request),
            default     => response("failure\nUnknown mode", 400)
                ->header('Content-Type', 'text/plain; charset=utf-8'),
        };
    }

    private function checkauth(Request $request): Response
    {
        // Для Basic Auth / stateless: отдаем фикс.
        $cookieName  = 'laravel_session';
        $cookieValue = 'no_session';

        Log::channel('onec')->info('[1C] checkauth OK', [
            'cookieName' => $cookieName,
        ]);

        return response("success\n{$cookieName}\n{$cookieValue}", 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    private function init(Request $request): Response
    {
        $limit = (int) config('onec.file_limit', 200000000);

        Log::channel('onec')->info('[1C] init', [
            'file_limit' => $limit,
        ]);

        return response("zip=no\nfile_limit={$limit}", 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    private function file(Request $request, string $type): Response
    {
        $filename = (string) ($request->query('filename') ?? $request->input('filename') ?? '');

        if ($filename === '') {
            Log::channel('onec')->warning('[1C] file missing filename', [
                'type' => $type,
            ]);

            return response("failure\nMissing filename", 400)
                ->header('Content-Type', 'text/plain; charset=utf-8');
        }

        $raw  = $request->getContent();
        $path = "1c/incoming/{$type}/{$filename}";

        Storage::disk('private')->put($path, $raw);

        Log::channel('onec')->info('[1C] file saved', [
            'type'     => $type,
            'path'     => $path,
            'bytes'    => strlen($raw),
            'is_xml'   => str_ends_with(mb_strtolower($filename), '.xml'),
        ]);

        return response("success", 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    private function import(Request $request, string $type): Response
    {
        $dir = "1c/incoming/{$type}";
        $allFiles = Storage::disk('private')->files($dir);

        // ✅ важный момент: импортируем ТОЛЬКО XML, картинки игнорим
        $xmlFiles = array_values(array_filter($allFiles, function ($f) {
            return str_ends_with(mb_strtolower($f), '.xml');
        }));

        Log::channel('onec')->info('[1C] import called', [
            'type'        => $type,
            'dir'         => $dir,
            'all_count'   => count($allFiles),
            'xml_count'   => count($xmlFiles),
            'xml_files'   => array_slice($xmlFiles, 0, 20), // чтобы лог не раздувать
        ]);

        if (empty($xmlFiles)) {
            return response("failure\nNo XML files", 400)
                ->header('Content-Type', 'text/plain; charset=utf-8');
        }

        try {
            $this->importer->importByFiles($type, $xmlFiles);

            Log::channel('onec')->info('[1C] import finished', [
                'type' => $type,
            ]);

            return response("success", 200)
                ->header('Content-Type', 'text/plain; charset=utf-8');
        } catch (\Throwable $e) {
            Log::channel('onec')->error('[1C] import ERROR', [
                'type' => $type,
                'msg'  => $e->getMessage(),
            ]);

            return response("failure\n".$e->getMessage(), 500)
                ->header('Content-Type', 'text/plain; charset=utf-8');
        }
    }

    private function exportQuery(Request $request): Response
    {
        Log::channel('onec')->info('[1C] export query');
        return response("success\n", 200)->header('Content-Type', 'text/plain; charset=utf-8');
    }

    private function exportSuccess(Request $request): Response
    {
        Log::channel('onec')->info('[1C] export success');
        return response("success\n", 200)->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
