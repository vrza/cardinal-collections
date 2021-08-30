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
    public function testLastKey($iteratorClass): void
    {
        $map = new Map();
        $this->assertNull($map->keyLast());
        $map->put('foo', true);
        $map->put('bar', true);
        $map->put('baz', true);
        $this->assertEquals('baz', $map->keyLast());
        $map->delete('baz');
        $this->assertEquals('bar', $map->keyLast());
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

        $map2 = new Map([5 => 3, 7 => 1, 9 => -2], $iteratorClass);
        $this->assertTrue($map2->nonEmpty());
        $result = $map2->reduce(function ($acc, $k, $v) {
            return [$acc[0] + $k, $acc[1] + $v];
        }, [0,0]);
        $this->assertEquals(21, $result[0]);
        $this->assertEquals(2, $result[1]);

        $map3 = new Map([5 => 3, 7 => 1, 9 => -2], $iteratorClass);
        $sumOfKeys = $map3->reduce(function ($acc, $k) {
            return $acc + $k;
        }, 0);
        $this->assertEquals(21, $sumOfKeys);
        $sumOfValues = $map3->reduce(function ($acc, $_k, $v) {
            return $acc + $v;
        });
        $this->assertEquals(2, $sumOfValues);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testReduceTuples($iteratorClass): void
    {
        $map = new Map([], $iteratorClass);
        $this->assertTrue($map->isEmpty());
        $result = $map->reduceTuples(function ($acc, $value) {
            return $acc + $value;
        });
        $this->assertNull($result);
        $map2 = new Map([5 => 3, 7 => 1], $iteratorClass);
        $this->assertTrue($map2->nonEmpty());
        $result = $map2->reduceTuples(function ($acc, $k, $v) {
            return [$acc[0] + $k, $acc[1] + $v];
        });
        $this->assertEquals(12, $result[0]);
        $this->assertEquals(4, $result[1]);
        $result = $map2->reduceTuples(function ($acc, $k) {
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

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testEvery($iteratorClass): void
    {
        $map = new Map(['one' => 'one', 'two' => 'two', 'three' => 'three'], $iteratorClass);
        $result1 = $map->every(function ($k, $v) {
            return $k === $v;
        });
        $this->assertTrue($result1);

        $map['three'] = 'four';
        $result2 = $map->every(function ($k, $v) {
            return $k === $v;
        });
        $this->assertFalse($result2);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testSome($iteratorClass): void
    {
        $map = new Map(['one' => 'two', 'three' => 'four'], $iteratorClass);
        $result1 = $map->some(function ($k, $v) {
            return $k === $v;
        });
        $this->assertFalse($result1);

        $map['five'] = 'five';
        $result2 = $map->some(function ($k, $v) {
            return $k === $v;
        });
        $this->assertTrue($result2);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testForeach($iteratorClass): void
    {
        $map = new Map([
            1 => 2,
            3 => 4,
            5 => 6
        ], $iteratorClass);
        $sum = 0;
        $map->foreach(function($k, $v) use (&$sum) {
            $sum += $k + $v;
        });
        $this->assertEquals(21, $sum);
    }

    public function testPrettyPrint(): void
    {
        $map = new Map();
        $map->add([1,2], [3,4]);
        $map->add([5,6], [7,8]);
        $this->assertEquals(
'( 
  [1,2] -> [3,4]
  [5,6] -> [7,8]
)', $map->__toString());
    }
}
