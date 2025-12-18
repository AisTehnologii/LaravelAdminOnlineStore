<?php

namespace App\Support;

use Illuminate\Support\Collection;

class TableExport
{
    public static function toXml(string $rootName, string $itemName, array $columns, Collection $rows): string
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= "<{$rootName}>\n";

        foreach ($rows as $row) {
            $xml .= "  <{$itemName}>\n";

            foreach ($columns as $key => $label) {
                $value = data_get($row, $key);

                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }

                $value = htmlspecialchars((string) ($value ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8');

                // делаем безопасный тег из ключа (section.title -> section_title)
                $tag = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key);

                $xml .= "    <{$tag}>{$value}</{$tag}>\n";
            }

            $xml .= "  </{$itemName}>\n";
        }

        $xml .= "</{$rootName}>\n";

        return $xml;
    }
}
