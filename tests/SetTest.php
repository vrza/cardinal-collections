<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Set;

class SetTest extends TestCase
{
    public function testEquality(): void
    {
        $a = new Set([1, 2, 3]);
        $b = new Set([3, 2, 1]);
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($a));
        $this->assertTrue($a->equals($a));
        $this->assertTrue($b->equals($b));
        $b->delete(2);
        $this->assertFalse($a->equals($b));
        $this->assertFalse($b->equals($a));
    }

    public function testAssocArrayElements(): void
    {
            $x1 = ['1' => 11, '2' => 22, '3' => 33];
            $x2 = ['3' => 33, '2' => 22, '1' => 11];
            $y = ['foo' => 'bar'];
            $a = new Set();
            $a->add($x1);
            $a->add($y);
            $b = new Set();
            $b->add($y);
            $b->add($x2);
            $this->assertTrue($a->equals($b));
            $this->assertTrue($b->equals($a));
    }

    public function testNumericArrayElements(): void
    {
        $x1 = [1, 2, 3];
        $x2 = [3, 2, 1];
        $y = ['foo' => 'bar'];
        $a = new Set();
        $a->add($x1);
        $a->add($y);
        $b = new Set();
        $b->add($y);
        $b->add($x2);
        $this->assertFalse($a->equals($b));
        $this->assertFalse($b->equals($a));
    }

    public function testSingleElement(): void
    {
        $a = new Set();
        $a->add([5]);
        $b = new Set();
        $b->add([5]);
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($a));
    }

    public function testObjectElement(): void
    {
        $dt_vv_1 = new DateTime('1978-11-13');
        $dt_vv_2 = new DateTime('1978-11-13');
        $dt_2021_1 = new DateTime('2021-05-24 20:47:00');
        $dt_2021_2 = new DateTime('2021-05-24 20:47:00');
        $a = new Set();
        $a->add($dt_vv_1);
        $a->add($dt_2021_1);
        $b = new Set();
        $b->add($dt_vv_2);
        $b->add($dt_2021_2);
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($a));
    }

    public function testAddRemove(): void
    {
        $set = new Set(['apple']);
        $set->add('orange')->add('banana');
        $this->assertCount(3, $set);
        $set->remove('apple');
        $this->assertCount(2, $set);
        $this->assertTrue($set->has('orange'));
        $this->assertFalse($set->has('apple'));
    }

    public function testArrayIterator(): void
    {
        $a = [
            'foo' => 'bar',
            'baz' => 'boo'
        ];
        foreach ($a as $key => $value) {
            unset($a[$key]);
        }
        $this->assertCount(0, $a);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testIterator($iteratorClass): void
    {
        $dt_vv_1 = new DateTime('1978-11-13');
        $dt_2021_1 = new DateTime('2021-05-24 20:47:00');
        $set = new Set([], $iteratorClass);
        $set->add($dt_vv_1);
        $set->add($dt_2021_1);
        $this->assertCount(2, $set);
        $this->assertTrue($set->nonEmpty());
        foreach ($set as $elem) {
            $set->remove($elem);
        }
        $this->assertCount(0, $set);
        $this->assertTrue($set->isEmpty());
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testKeyWhileIterating($iteratorClass): void
    {
        $dt_vv_1 = new DateTime('1978-11-13');
        $dt_2021_1 = new DateTime('2021-05-24 20:47:00');
        $set = new Set([], $iteratorClass);
        $set->add($dt_vv_1);
        $set->add($dt_2021_1);
        $this->assertCount(2, $set);
        $this->assertTrue($set->nonEmpty());
        foreach ($set as $elem) {
            $this->assertEquals($elem, $set->key());
            $this->assertEquals($elem, $set->current());
        }
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testSetUnion($iteratorClass): void
    {
        $set_1 = new Set(['apple', 'orange'], $iteratorClass);
        $set_2 = new Set(['orange', 'banana'], $iteratorClass);
        $set_1_2 = $set_1->union($set_2);
        $set_2_1 = $set_2->union($set_1);
        $this->assertTrue($set_1_2->has('apple'));
        $this->assertTrue($set_1_2->has('orange'));
        $this->assertTrue($set_1_2->has('banana'));
        $this->assertTrue($set_1_2->equals($set_2_1));
        $this->assertTrue($set_2_1->equals($set_1_2));
        $this->assertTrue($set_1_2->equals($set_1_2));
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testReduce($iteratorClass): void
    {
        $set = new Set([], $iteratorClass);
        $this->assertTrue($set->isEmpty());
        $value = $set->reduce(function ($acc, $x) {
            return $acc + $x;
        });
        $this->assertNull($value);
        $set = new Set([5, 7]);
        $this->assertTrue($set->nonEmpty());
        $value = $set->reduce(function ($acc, $x) {
            return $acc + $x;
        });
        $this->assertEquals(12, $value);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testMap($iteratorClass): void
    {
        $set = new Set([], $iteratorClass);
        $this->assertTrue($set->isEmpty());
        $result = $set->map(function ($x) {
            return $x * $x;
        });
        $this->assertEquals($set, $result);
        $this->assertTrue($result->isEmpty());
        $set = new Set([5, 7]);
        $this->assertTrue($set->nonEmpty());
        $result = $set->map(function ($x) {
            return $x * $x;
        });
        $this->assertInstanceOf(Set::class, $result);
        $this->assertTrue($result->has(25));
        $this->assertTrue($result->has(49));
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testFilter($iteratorClass): void
    {
        $set = new Set([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], $iteratorClass);
        $odd = $set->filter(function ($x) {
            return $x % 2 == 1;
        });
        $this->assertInstanceOf(Set::class, $odd);
        $this->assertCount(5, $odd);
        $this->assertEquals([1, 3, 5, 7, 9], $odd->asArray());
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testFilterEmptySet($iteratorClass): void
    {
        $set = new Set([], $iteratorClass);
        $empty = $set->filter(function ($x) {
            return $x % 2 == 1;
        });
        $this->assertInstanceOf(Set::class, $empty);
        $this->assertCount(0, $empty);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testSubsetOf($iteratorClass): void
    {
        $bigSet = new Set([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], $iteratorClass);

        $smallSubset = new Set([3, 5, 7]);
        $this->assertTrue($smallSubset->subsetOf($bigSet));

        $biggerSet = new Set([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $this->assertFalse($biggerSet->subsetOf($bigSet));
        $this->assertTrue($bigSet->subsetOf($biggerSet));

        $nonSubsetOfBigSet = new Set([10, 9]);
        $this->assertFalse($nonSubsetOfBigSet->subsetOf($bigSet));
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testIntersect($iteratorClass): void
    {
        $a = new Set([1, 2, 3, 4, 5], $iteratorClass);
        $b = new Set([2, 4, 6, 8], $iteratorClass);
        $c = new Set([2, 3, 4], $iteratorClass);
        $intersection = $a->intersect($b)->intersect($c);
        $this->assertTrue($intersection->equals(new Set([2, 4])));
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testDifference($iteratorClass): void
    {
        $a = new Set([1, 2, 3], $iteratorClass);
        $b = new Set([2, 3, 4], $iteratorClass);
        $diffAB = $a->difference($b);
        $this->assertTrue($diffAB->equals(new Set([1])));
        $diffBA = $b->difference($a);
        $this->assertTrue($diffBA->equals(new Set([4])));
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testEvery($iteratorClass): void
    {
        $a = new Set([1, 3, 5], $iteratorClass);
        $result = $a->every(function ($x) {
            return $x % 2 === 1;
        });
        $this->assertTrue($result);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testSome($iteratorClass): void
    {
        $a = new Set([1, 2, 3], $iteratorClass);
        $result = $a->some(function ($x) {
            return $x % 2 === 0;
        });
        $this->assertTrue($result);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testForeach($iteratorClass): void
    {
        $s = new Set([1, 2, 3], $iteratorClass);
        $sum = 0;
        $s->foreach(function ($x) use (&$sum) {
            $sum += $x;
        });
        $this->assertEquals(6, $sum);
    }

    public function testPrettyPrint(): void
    {
        $set = new Set();
        $set->add([1,2]);
        $this->assertEquals('{ [1,2] }', $set->__toString());
    }
}
