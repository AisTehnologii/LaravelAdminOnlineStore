<?php

namespace App\Http\Controllers\OneC;

use App\Http\Controllers\Controller;
use App\Services\OneC\CommerceMlImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OneCExchangeController extends Controller
{
    public function __construct(
        private CommerceMlImportService $importer,
        private OrdersExportController $ordersExporter, // ✅ добавили
    ) {}

   public function handle(Request $request, ?string $tail = null)
{
    // 1) tail может быть "getorders" / "getgoods" / что угодно
    $tail = trim((string)$tail);

    // 2) читаем type/mode/filename обычным способом
    $type     = (string) ($request->query('type') ?? $request->input('type') ?? '');
    $mode     = (string) ($request->query('mode') ?? $request->input('mode') ?? '');
    $filename = (string) ($request->query('filename') ?? $request->input('filename') ?? '');

    // 3) если type/mode пустые — попробуем вытащить из tail (частый кейс)
    // примеры tail:
    // "getorders?mode=query&type=sale" => tail="getorders"
    // "sale/query" => tail="sale/query"
    // "type=sale&mode=query" - редкость
    if ($type === '' || $mode === '') {
        [$type2, $mode2, $file2] = $this->parseFromTail($tail);
        $type     = $type     ?: $type2;
        $mode     = $mode     ?: $mode2;
        $filename = $filename ?: $file2;
    }

    // 4) если tail начинается с getorders — это всё равно sale
    if ($type === '' && str_starts_with($tail, 'getorders')) {
        $type = 'sale';
    }

    Log::channel('onec')->info('[1C] REQUEST', [
        'tail' => $tail,
        'type' => $type,
        'mode' => $mode,
        'filename' => $filename,
        'ip' => $request->ip(),
        'url' => $request->fullUrl(),
        'method' => $request->method(),
    ]);

    try {
        // ✅ sale (выгрузка/загрузка документов)
        if ($type === 'sale') {
            return match ($mode) {
                'checkauth' => $this->checkauth(),
                'init'      => $this->init(),
                'query'     => $this->saleQuery(),
                'file'      => $this->saleFile($request, $filename),
                'import'    => $this->import('sale', $filename),   // ✅ ВАЖНО: import для sale
                'success'   => $this->success(),
                default     => $this->fail("Unknown mode: {$mode}"),
            };
        }

        // ✅ catalog (товары/цены/остатки)
        return match ($mode) {
            'checkauth' => $this->checkauth(),
            'init'      => $this->init(),
            'file'      => $this->fileUpload($request, $type, $filename),
            'import'    => $this->import($type, $filename),
            default     => $this->fail("Unknown mode: {$mode}"),
        };

    } catch (\Throwable $e) {
        Log::channel('onec')->error('[1C] EXCEPTION', ['msg' => $e->getMessage()]);
        return $this->fail($e->getMessage());
    }
}


   private function checkauth()
{
    $cookieName = config('session.cookie', 'laravel_session');
    $sid = session()->getId();

    return response("success\n{$cookieName}\n{$sid}\n", 200)
        ->header('Content-Type', 'text/plain; charset=utf-8');
}


    private function init()
    {
        $limit = (int) config('onec.file_limit', 20000000);
        return response("zip=no\r\nfile_limit={$limit}\r\n", 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    /**
     * ✅ Импорт: 1С -> сайт (upload)
     */
    private function fileUpload(Request $request, string $type, string $filename)
    {
        if ($filename === '') return $this->fail('filename is empty');

        $bytes = $request->getContent();
        if ($bytes === '') return $this->fail('empty body');

        $stored = $this->importer->storeIncomingFile($type, $filename, $bytes);

        Log::channel('onec')->info('[1C] FILE_STORED', [
            'type' => $type,
            'filename' => $filename,
            'stored_as' => $stored,
            'bytes' => strlen($bytes),
        ]);

        return response("success\r\n", 200)->header('Content-Type', 'text/plain; charset=utf-8');
    }

    private function import(string $type, string $filename)
    {
        $this->importer->importByType($type);

        return response("success\r\n", 200)->header('Content-Type', 'text/plain; charset=utf-8');
    }

    /**
     * ✅ Sale: query — сообщаем имя outgoing файла
     */
    // private function saleQuery()
    // {
    //     $name = $this->ordersExporter->buildFile();

    //     Log::channel('onec')->info('[1C] SALE_QUERY_OK', [
    //         'filename' => $name,
    //     ]);

    //     return response("success\r\n{$name}\r\n", 200)
    //         ->header('Content-Type', 'text/plain; charset=utf-8');
    // }
   private function saleQuery()
{
    // ✅ Если в 1c/outgoing нет orders_*.xml — вернём пустой XML, чтобы 1С сразу вышла из цикла шага 3
    // ✅ Если файл есть — отдадим один файл и удалим его (чтобы не зациклиться)
    $xml = $this->ordersExporter->takeOutgoingXml(true);

    Log::channel('onec')->info('[1C] SALE_QUERY_XML', [
        'has_file' => $this->ordersExporter->nextOutgoingFilename() !== null,
        'size' => strlen($xml),
        'head' => substr($xml, 0, 120),
    ]);

    return response($xml, 200, [
        'Content-Type' => 'text/xml; charset=UTF-8',
        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
    ]);
}



    /**
     * ✅ Sale: file — либо download (GET), либо upload (POST)
     */
    private function saleFile(Request $request, string $filename)
    {
        $filename = basename($filename);

        // 1) Если 1С скачивает файл (обычно GET, body пустой) — отдаём outgoing
        if ($request->isMethod('GET') || $request->getContent() === '') {
            return $this->ordersExporter->downloadFile($filename);
        }

        // 2) Если 1С загружает документы на сайт (POST с body) — сохраняем как incoming
        return $this->fileUpload($request, 'sale', $filename);
    }

    private function success()
    {
        return response("success\r\n", 200)->header('Content-Type', 'text/plain; charset=utf-8');
    }

    private function fail(string $message)
    {
        return response("failure\r\n{$message}\r\n", 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}

