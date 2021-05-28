<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Map;

class KeyTypesTest extends TestCase
{
    public function testArrayAsKey(): void
    {
        $map = new Map();
        $map[['k' => 'v']] = 'document';
        self::assertTrue(isset($map[['k' => 'v']]));
        self::assertEquals('document', $map[['k' => 'v']]);
        $values = $map->values();
        self::assertCount(1, $values);
        self::assertEquals('document', $values[0]);
    }

    public function testBooleanAsKey(): void
    {
        $map = new Map();
        $map[false] = 'document';
        self::assertTrue(isset($map[false]));
        self::assertEquals('document', $map[false]);
        $values = $map->values();
        self::assertCount(1, $values);
        self::assertEquals('document', $values[0]);
    }

    public function testObjectAsKey(): void
    {
        $map = new Map();
        $object = new DateTime('1978-11-13');
        $map[$object] = 'document';
        self::assertEquals('document', $map[$object]);
    }

    public function testPutResourceAsKeyInvalidArgumentException(): void
    {
        $map = new Map();
        $this->expectException(InvalidArgumentException::class);
        $fp = tmpfile();
        $map[$fp] = 'document';
        fclose($fp);
    }

}
