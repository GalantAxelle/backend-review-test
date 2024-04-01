<?php

declare(strict_types=1);

namespace App\Tests\Unit\Decoder;

use App\Decoder\JsonLinesDecoder;
use PHPUnit\Framework\TestCase;

class JsonLinesDecoderTest extends TestCase
{
    private JsonLinesDecoder $decoder;

    public function setUp(): void
    {
        parent::setUp();

        $this->decoder = new JsonLinesDecoder();
    }

    /**
     * @dataProvider decodeJsonLinesToArrayProvider
     */
    public function testDecodeJsonLinesToArray(string $serializedJsonLines, array $expectedDecodedLines): void
    {
        $actual = $this->decoder->decodeJsonLinesToArray($serializedJsonLines);

        self::assertSame($expectedDecodedLines, $actual);
    }

    public function decodeJsonLinesToArrayProvider(): array
    {
        return [
            [
                '{"id": 1, "label": "test"}
{"id": 2, "label": "toto"}',
                [
                    [
                        "id"    => 1,
                        "label" => "test",
                    ],
                    [
                        "id"    => 2,
                        "label" => "toto",
                    ],
                ],
            ],
        ];
    }
}
