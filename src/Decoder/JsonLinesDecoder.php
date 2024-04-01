<?php

declare(strict_types=1);

namespace App\Decoder;

class JsonLinesDecoder
{
    public function decodeJsonLinesToArray(string $serializedContent): array
    {
        $serializedLines = explode(PHP_EOL, $serializedContent);

        $decodedLines = [];

        foreach ($serializedLines as $line) {
            $decodedLines[] = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        }

        return $decodedLines;
    }
}
