<?php

declare(strict_types=1);

namespace App\Decoder;

use JsonException;

class JsonLinesDecoder
{
    /**
     * @throws JsonException
     */
    public function decodeJsonLinesToArray(string $serializedContent): array
    {
        $serializedLines = explode(PHP_EOL, $serializedContent);

        $decodedLines = [];

        foreach ($serializedLines as $line) {
            if (true === empty($line)) {
                continue;
            }

            $decodedLines[] = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        }

        return $decodedLines;
    }
}
