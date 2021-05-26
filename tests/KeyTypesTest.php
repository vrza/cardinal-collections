<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Map;

class KeyTypesTest extends TestCase
{
    public function testArrayAsKey(): void
    {
        $powerMap = new Map();
        $powerMap[['k' => 'v']] = 'document';
        self::assertTrue(isset($powerMap[['k' => 'v']]));
        self::assertEquals('document', $powerMap[['k' => 'v']]);
        $values = $powerMap->values();
        self::assertCount(1, $values);
        self::assertEquals('document', $values[0]);
    }

    public function testBooleanAsKey(): void
    {
        $powerMap = new Map();
        $powerMap[false] = 'document';
        self::assertTrue(isset($powerMap[false]));
        self::assertEquals('document', $powerMap[false]);
        $values = $powerMap->values();
        self::assertCount(1, $values);
        self::assertEquals('document', $values[0]);
    }

    public function testOffsetGetInvalidArgumentException(): void
    {
        $powerMap = new Map();
        $object = new DateTime('1978-11-13');
        $powerMap[$object] = 'document';
        self::assertEquals('document', $powerMap[$object]);
    }

    public function testPutResourceAsKeyInvalidArgumentException(): void
    {
        $powerMap = new Map();
        $this->expectException(InvalidArgumentException::class);
        $fp = tmpfile();
        $powerMap[$fp] = 'document';
        fclose($fp);
    }

}
