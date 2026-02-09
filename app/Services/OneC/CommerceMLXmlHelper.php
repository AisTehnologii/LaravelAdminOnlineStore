<?php

namespace App\Services\OneC;

use SimpleXMLElement;

class CommerceMLXmlHelper
{
    public static function load(string $path): SimpleXMLElement
    {
        $xml = simplexml_load_file($path);
        if (!$xml) {
            throw new \RuntimeException("Cannot parse XML: {$path}");
        }
        return $xml;
    }

    public static function str(?SimpleXMLElement $node): ?string
    {
        if ($node === null) return null;
        $s = trim((string)$node);
        return $s === '' ? null : $s;
    }

    public static function float(?SimpleXMLElement $node): float
    {
        $s = self::str($node);
        if ($s === null) return 0.0;
        return (float) str_replace(',', '.', $s);
    }
}
