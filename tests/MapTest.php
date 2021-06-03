<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Map;

class MapTest extends TestCase
{
    public function testBasicOperations(): void
    {
        $map = new Map();
        $map->put('foo', 1);
        $map->put('bar', 2);
        $map->put('baz', 3);
        $this->assertCount(3, $map);
        $map->remove('bar');
        $this->assertCount(2, $map);
    }

    public function testBasicOperationsAgain(): void
    {
        $map = new Map();
        $map->put('foo', true);
        $map->put('bar', true);
        $map->put('baz', true);
        $this->assertCount(3, $map);
        $map->remove('bar');
        $this->assertCount(2, $map);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testConstructFromAnother($iteratorClass): void
    {
        $mapFrom = new Map([], $iteratorClass);
        $mapFrom->put(['foo' => 'bar'], 'baz');
        $mapFrom->append(2);
        $mapTo = new Map($mapFrom);
        $this->assertTrue($mapTo->has(['foo' => 'bar']));
        $this->assertTrue($mapTo->has(0));
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testMapIterator($iteratorClass): void
    {
        $map = new Map([
            'foo' => 'bar',
            'baz' => 'boo'
        ], $iteratorClass);
        foreach ($map as $key => $value) {
            unset($map[$key]);
        }
        $this->assertCount(0, $map);
    }

    public function testPutIfAbsent(): void
    {
        $initialArray = [
            'pera' => [
                'name' => 'Pera',
                'email' => 'pera@ddr.ex'
            ]
        ];
        $map = new Map($initialArray);
        $new = $map->putIfAbsent(
            'mika',
            [
                'name' => 'Mika',
                'email' => 'mika@frg.ex'
            ]
        );
        $this->assertNull($new);
        $existing = $map->putIfAbsent(
            'pera',
            [
                'name' => 'Petar',
                'email' => 'petar@ddr.ex'
            ]
        );
        $this->assertEquals(
            'Pera',
            $existing['name']
        );
        $this->assertTrue($map->has('mika'));
        $this->assertEquals('Mika', $map->get('mika')['name']);
        $map->remove('mika');
        $this->assertFalse($map->has('mika'));
        $this->assertTrue($map->has('pera'));
        $map->remove('pera');
        $this->assertFalse($map->has('pera'));
        $this->assertTrue($map->isEmpty());
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testReduce($iteratorClass): void
    {
        $map = new Map([], $iteratorClass);
        $this->assertTrue($map->isEmpty());
        $result = $map->reduce(function ($acc, $value) {
            return $acc + $value;
        });
        $this->assertNull($result);
        $map2 = new Map([5 => 3, 7 => 1], $iteratorClass);
        $this->assertTrue($map2->nonEmpty());
        $result = $map2->reduce(function ($acc, $k, $v) {
            return [$acc[0] + $k, $acc[1] + $v];
        });
        $this->assertEquals(12, $result[0]);
        $result = $map2->reduce(function ($acc, $k) {
            return [$acc[0] + $k, $acc[1]];
        });
        $this->assertEquals(12, $result[0]);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testMap($iteratorClass): void
    {
        $map = new Map([], $iteratorClass);
        $this->assertTrue($map->isEmpty());
        $result = $map->map(function ($key, $value) {
            return [$key * $key, $value * $value];
        });
        $this->assertTrue($result->isEmpty());
        $map->put(3, 4);
        $result = $map->map(function ($key, $value) {
            return [$key * $key, $value * $value];
        });
        $this->assertInstanceOf(Map::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals(16, $result->get(9));
    }
}
