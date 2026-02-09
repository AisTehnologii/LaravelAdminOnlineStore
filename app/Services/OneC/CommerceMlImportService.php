<?php

namespace App\Services\OneC;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommerceMlImportService
{
	private string $defaultLocale = 'ru';
	private bool $onlyChanges = false;

	public function __construct()
	{
		// private: storage/app/private
		Storage::disk('private')->makeDirectory('1c/incoming/catalog');
		Storage::disk('private')->makeDirectory('1c/incoming/sale');

		// public: storage/app/public
		Storage::disk('public')->makeDirectory('1c/import_files');
	}

	/**
	 * Сохраняет входящий файл от 1С.
	 * Картинки -> public/1c/...
	 * XML -> private/1c/incoming/{type}/...
	 */
	public function storeIncomingFile(string $type, string $filename, string $bytes): string
{
    $filename = str_replace('\\', '/', $filename);
    $filename = ltrim($filename, '/');

    $lower = Str::lower($filename);

    $isImage = Str::endsWith($lower, ['.jpg', '.jpeg', '.png', '.webp', '.gif', '.bmp', '.svg']);

    // Картинки — ВСЕГДА в public/1c/import_files/{basename}
    if (Str::contains($lower, 'import_files/') || $isImage) {
        $baseName = basename($filename); // <- выкидываем любые подпапки
        $publicPath = '1c/import_files/' . $baseName;

        Storage::disk('public')->put($publicPath, $bytes);

        Log::channel('onec')->info('[1C] STORE_PUBLIC_IMAGE', [
            'type'       => $type,
            'filename'   => $filename,
            'baseName'   => $baseName,
            'publicPath' => $publicPath,
            'bytes'      => strlen($bytes),
        ]);

        return 'public:' . $publicPath;
    }

    // XML — в private
    $path = "1c/incoming/{$type}/" . basename($filename);
    Storage::disk('private')->put($path, $bytes);

    Log::channel('onec')->info('[1C] STORE_PRIVATE', [
        'type'     => $type,
        'filename' => $filename,
        'path'     => $path,
        'bytes'    => strlen($bytes),
    ]);

    return 'private:' . $path;
}


	/**
	 * Импортируем весь тип (catalog/sale): берём XML из private/1c/incoming/{type}
	 * ВАЖНО: prices/rests/offers НЕ создают товары, а только обновляют price/qty.
	 */
	public function importByType(string $type): void
	{
		$dir = "1c/incoming/{$type}";
		$files = Storage::disk('private')->files($dir);

		// только xml
		$xmlFiles = array_values(array_filter(
			$files,
			fn ($f) => str_ends_with(mb_strtolower($f), '.xml')
		));

		// порядок важен: сначала import_, потом offers_, потом prices_, потом rests_
		$import = $offers = $prices = $rests = $other = [];

		foreach ($xmlFiles as $f) {
			$name = basename($f);

			if (str_starts_with($name, 'import_')) {
				$import[] = $f;
			} elseif (str_starts_with($name, 'offers_')) {
				$offers[] = $f;
			} elseif (str_starts_with($name, 'prices_')) {
				$prices[] = $f;
			} elseif (str_starts_with($name, 'rests_')) {
				$rests[] = $f;
			} else {
				$other[] = $f;
			}
		}

		$ordered = array_merge($import, $offers, $prices, $rests, $other);

		Log::channel('onec')->info('[1C] FILE_ORDER', [
			'type'   => $type,
			'import' => count($import),
			'offers' => count($offers),
			'prices' => count($prices),
			'rests'  => count($rests),
			'other'  => count($other),
			'total'  => count($ordered),
		]);

		foreach ($ordered as $file) {
			$fullPath = Storage::disk('private')->path($file);
			$name = basename($file);

			$localeFromName = $this->detectLocaleFromFilename($name);
			$xml = $this->loadXmlFile($fullPath);

			$this->onlyChanges = $this->detectOnlyChanges($xml);

			Log::channel('onec')->info('[1C] PROCESS', [
				'file'             => $name,
				'type'             => $type,
				'locale_from_name' => $localeFromName,
				'onlyChanges'      => $this->onlyChanges,
			]);

			if (str_starts_with($name, 'import_')) {
				// товары/группы
				$this->importCatalog($xml, $localeFromName);
				continue;
			}

			if (str_starts_with($name, 'offers_') || str_starts_with($name, 'prices_') || str_starts_with($name, 'rests_')) {
				// цены/остатки
				$this->importOffersPackage($xml, $name);
				continue;
			}

			// если это sale (заказы) — там есть <Документ>
			if ($type === 'sale') {
				$hasDocs = $xml->xpath('//*[local-name()="Документ"]') ?: [];

				if (!empty($hasDocs)) {
					(new \App\Services\OneC\OrderImportService())->import($xml, $name);
					continue;
				}
			}

			Log::channel('onec')->warning('[1C] SKIP_UNKNOWN_FILE', ['file' => $name]);
		}

		Log::channel('onec')->info('[1C] IMPORT_DONE', [
			'products_count' => DB::table('products')->count(),
		]);
	}

	// ------------------ IMPORT CATALOG (products create/update) ------------------

	private function importCatalog(\SimpleXMLElement $xml, string $localeFromFile): void
	{
		// ВАЖНО: чтобы не плодить дубли ru/ro — фиксируем locale товаров (один вариант хранения)
		$productLocale = config('onec.default_locale', $this->defaultLocale); // например ru

		$products = $xml->xpath('//*[local-name()="Каталог"]//*[local-name()="Товары"]//*[local-name()="Товар"]') ?: [];

		Log::channel('onec')->info('[1C] CATALOG_FOUND', [
			'products'     => count($products),
			'productLocale'=> $productLocale,
			'onlyChanges'  => $this->onlyChanges,
		]);

		DB::transaction(function () use ($products, $productLocale) {
			$created = 0;
			$updated = 0;
			$skipped = 0;

			foreach ($products as $p) {
				$externalId = $this->nodeText($p, './*[local-name()="Ид"]');

				if (!$externalId) {
					$skipped++;
					continue;
				}

				$title = $this->nodeText($p, './*[local-name()="Наименование"]') ?? 'Товар';
				$sku   = $this->nodeText($p, './*[local-name()="Артикул"]');
				$unit  = $this->nodeText($p, './*[local-name()="БазоваяЕдиница"]');

				// картинки
				$pictures = [];
$imgNodes = $p->xpath('./*[local-name()="Картинка"]') ?: [];

foreach ($imgNodes as $imgNode) {
    $val = trim((string) $imgNode);
    if ($val === '') continue;

    $val = ltrim(str_replace('\\', '/', $val), '/');

    // В БД храним строго: 1c/import_files/{basename}
    $pictures[] = '1c/import_files/' . basename($val);
}

				// СНАЧАЛА создаём модель, ПОТОМ isNew
				$model = Product::query()->firstOrNew(['external_id' => $externalId]);
				$isNew = !$model->exists;

				// фиксируем единый locale, чтобы не плодить дубли ru/ro
				$model->locale = $model->locale ?: $productLocale;

				// дефолтная секция каталога
if (empty($model->section_id) || (int)$model->section_id === 0) {
    $model->section_id = 1;
}

				// поля твоей таблицы
				$model->title = $title;

				if ($sku !== null) {
					$model->sku = $sku;
				}

				if ($unit !== null) {
					$model->unit = $unit;
				}

				// картинки:
				// - если partial и картинок нет — НЕ трогаем
				// - если есть — обновляем
				if (!empty($pictures)) {
					$model->announce_image_path = $pictures[0];
				} else {
					if (!$this->onlyChanges && $isNew) {
						$model->announce_image_path = null;
					}
				}

				$model->is_active = 1;

				// цену не трогаем в import_ (она придёт из offers/prices)
				if ($model->price === null) {
					$model->price = 0;
				}

				$model->onec_raw = array_merge($model->onec_raw ?? [], [
					'last_catalog_import_at' => now()->toDateTimeString(),
				]);

				$model->save();

				$isNew ? $created++ : $updated++;
			}

			Log::channel('onec')->info('[1C] CATALOG_SAVED', [
				'created' => $created,
				'updated' => $updated,
				'skipped' => $skipped,
			]);
		});
	}

	// ------------------ IMPORT OFFERS (update price/qty only) ------------------

	private function importOffersPackage(\SimpleXMLElement $xml, string $file): void
	{
		// Берём все "Предложение" — в CommerceML бывает по-разному вложено
		$offers = $xml->xpath('//*[local-name()="Предложение"]') ?: [];

		$priceUpdated = 0;
		$qtyUpdated = 0;
		$noMatchPrice = 0;
		$noMatchQty = 0;

		foreach ($offers as $offer) {
			$offerId = $this->nodeText($offer, './*[local-name()="Ид"]');

			if (!$offerId) {
				continue;
			}

			// ВАЖНО: у тебя НЕТ base_external_id,
			// поэтому обновляем только по baseId (до #)
			$baseId  = Str::before($offerId, '#');
			$matchId = $baseId !== '' ? $baseId : $offerId;

			// цена
			$price = $this->extractOfferPrice($offer);

			if ($price !== null) {
				$affected = Product::query()
					->where('external_id', $matchId)
					->update(['price' => $price]);

				if ($affected > 0) {
					$priceUpdated += $affected;
				} else {
					$noMatchPrice++;
				}
			}

			// количество (qty)
			$qty = $this->extractOfferQuantity($offer);

			if ($qty !== null) {
				$affected = Product::query()
					->where('external_id', $matchId)
					->update(['qty' => (int) $qty]);

				if ($affected > 0) {
					$qtyUpdated += $affected;
				} else {
					$noMatchQty++;
				}
			}
		}

		Log::channel('onec')->info('[1C] OFFERS_IMPORTED', [
			'file'              => $file,
			'offers_total'      => count($offers),
			'price_rows_updated'=> $priceUpdated,
			'qty_rows_updated'  => $qtyUpdated,
			'no_match_price'    => $noMatchPrice,
			'no_match_qty'      => $noMatchQty,
		]);
	}

	// ------------------ HELPERS ------------------

	private function detectLocaleFromFilename(string $filename): string
	{
		$f = Str::lower($filename);

		if (Str::contains($f, '_ro_') || Str::contains($f, 'ro_')) {
			return 'ro';
		}

		if (Str::contains($f, '_en_') || Str::contains($f, 'en_')) {
			return 'en';
		}

		if (Str::contains($f, '_ru_') || Str::contains($f, 'ru_')) {
			return 'ru';
		}

		return config('onec.default_locale', $this->defaultLocale);
	}

	/**
	 * Читает XML и приводит кодировку в UTF-8 (иначе будут "РњРµ..." и т.п.)
	 */
	private function loadXmlFile(string $fullPath): \SimpleXMLElement
	{
		$content = file_get_contents($fullPath);

		if ($content === false) {
			throw new \RuntimeException("Cannot read xml: {$fullPath}");
		}

		// попытка определить encoding из заголовка
		$encoding = null;

		if (preg_match('~encoding=["\']([^"\']+)["\']~i', $content, $m)) {
			$encoding = strtoupper(trim($m[1]));
		}

		// конверт в UTF-8, если нужно
		if ($encoding && $encoding !== 'UTF-8') {
			$converted = @iconv($encoding, 'UTF-8//IGNORE', $content);

			if ($converted !== false) {
				$content = $converted;
			}
		}

		libxml_use_internal_errors(true);

		$xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);

		if ($xml === false) {
			$errs = array_map(fn ($e) => trim($e->message), libxml_get_errors());
			libxml_clear_errors();
			throw new \RuntimeException('Invalid XML: ' . implode('; ', $errs));
		}

		return $xml;
	}

	private function detectOnlyChanges(\SimpleXMLElement $xml): bool
	{
		// <КоммерческаяИнформация СодержитТолькоИзменения="true">
		$val = null;

		if (isset($xml['СодержитТолькоИзменения'])) {
			$val = (string) $xml['СодержитТолькоИзменения'];
		} else {
			$ci = $xml->xpath('/*[local-name()="КоммерческаяИнформация"]');

			if (is_array($ci) && isset($ci[0]) && isset($ci[0]['СодержитТолькоИзменения'])) {
				$val = (string) $ci[0]['СодержитТолькоИзменения'];
			}
		}

		$v = mb_strtolower(trim((string) $val));

		return in_array($v, ['true', '1', 'да', 'yes', 'y'], true);
	}

	private function nodeText(\SimpleXMLElement $xml, string $expr): ?string
	{
		$nodes = $xml->xpath($expr);

		if (!is_array($nodes) || !isset($nodes[0])) {
			return null;
		}

		$v = trim((string) $nodes[0]);

		return $v !== '' ? $v : null;
	}

	private function extractOfferPrice(\SimpleXMLElement $offer): ?float
	{
		$nodes = $offer->xpath('.//*[local-name()="ЦенаЗаЕдиницу"]');

		if (!is_array($nodes) || !isset($nodes[0])) {
			return null;
		}

		$raw = (string) $nodes[0];
		$raw = str_replace(["\xC2\xA0", ' ', "\t", "\n", "\r"], '', $raw);
		$raw = str_replace(',', '.', $raw);

		return ($raw !== '' && is_numeric($raw)) ? (float) $raw : null;
	}

	private function extractOfferQuantity(\SimpleXMLElement $offer): ?float
	{
		$nodes = $offer->xpath('.//*[local-name()="Количество"]');

		if (!is_array($nodes) || !isset($nodes[0])) {
			return null;
		}

		$raw = (string) $nodes[0];
		$raw = str_replace(["\xC2\xA0", ' ', "\t", "\n", "\r"], '', $raw);
		$raw = str_replace(',', '.', $raw);

		return ($raw !== '' && is_numeric($raw)) ? (float) $raw : null;
	}
}
