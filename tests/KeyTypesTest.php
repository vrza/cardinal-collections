<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Map;
use CardinalCollections\Utilities;

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

    public function testNestedArrayAsKey(): void
    {
        $initialArray = [
          'k' => [
              'k1' => 'v1',
              'k2' => 'v2'
          ]
        ];
        $structurallyEquivalentArray = [
          'k' => [
              'k2' => 'v2',
              'k1' => 'v1'
          ]
        ];
        $map = new Map();
        $map[$initialArray] = 'document';
        self::assertTrue(isset($map[$structurallyEquivalentArray]));
        self::assertEquals('document', $map[$structurallyEquivalentArray]);
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

    public function testHashInformationHiding(): void
    {
        $map = new Map();
        $array = [1, 2, 3];
        $hash = Utilities::hashAny($array);
        $map->add($array, 1);
        $this->assertFalse($map->has($hash));
    }

    public function testNoHashKeyClobber(): void
    {
        $map = new Map();
        $array = [1, 2, 3];
        $hash = Utilities::hashAny($array);
        $map->add($array, 1);
        $map->add($hash, 2);
        $this->assertTrue($map->get($array) === 1);
    }
}
