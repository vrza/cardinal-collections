<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Map;
use CardinalCollections\Mutable\Set;

/**
 *  Tests to help improve iterator sanity.
 *  Good writeup on PHP foreach:
 *  https://stackoverflow.com/questions/10057671/how-does-php-foreach-actually-work
 */
class ModificationDuringIterationTest extends TestCase
{
    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testNestedLoops($iteratorClass): void
    {
        $set = new Set([ 1, 2, 3, 4, 5 ], $iteratorClass);
        $verificationSet = new Set([], $iteratorClass);
        foreach ($set as $v1) {
            foreach ($set as $v2) {
                if ($v1 == 1 && $v2 == 1) {
                    $set->remove(1);
                }
                $verificationSet->add([$v1, $v2]);
            }
        }
        /* TODO Investigate if nested foreach loops can be supported
         * PHP 5 output: (1, 1) (1, 3) (1, 4) (1, 5)
         * PHP 7 output: (1, 1) (1, 3) (1, 4) (1, 5)
         *               (3, 1) (3, 3) (3, 4) (3, 5)
         *               (4, 1) (4, 3) (4, 4) (4, 5)
         *               (5, 1) (5, 3) (5, 4) (5, 5)
         * our output:   (1, 1) (1, 2) (1, 3) (1, 4) (1, 5)
         */
        $this->assertCount(4, $set);
        $this->assertCount(5, $verificationSet);
        $this->assertTrue($verificationSet->has([1, 1]));
        $this->assertTrue($verificationSet->has([1, 2]));
        $this->assertTrue($verificationSet->has([1, 3]));
        $this->assertTrue($verificationSet->has([1, 4]));
        $this->assertTrue($verificationSet->has([1, 5]));
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testRemoveAddSameKey($iteratorClass): void
    {
        $map = new Map(['EzEz' => 1, 'EzFY' => 2, 'FYEz' => 3], $iteratorClass);
        foreach ($map as $value) {
            unset($map['EzFY']);
            $map['FYFY'] = 4;
        }
        /*
         * PHP 5 output: 1, 4
         * PHP 7 output: 1, 3, 4 <- we have this behavior
         *
         * Previously the HashPointer restore mechanism jumped
         * right to the new element because it "looked" like
         * it's the same as the removed element (due to colliding
         * hash and pointer). As we no longer rely on the element
         * hash for anything, this is no longer an issue.
         */
        $this->assertCount(3, $map);
        $verificationSet = new Set();
        foreach ($map as $value) {
            $verificationSet->add($value);
        }
        $this->assertTrue($verificationSet->equals(new Set([1, 3, 4])));
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testRemove($iteratorClass): void
    {
        $map = new Map([
            'pera' => [
                'name' => 'Pera',
                'email' => 'pera@ddr.ex'
            ],
            'mika' => [
                'name' => 'Mika',
                'email' => 'mika@frg.ex'
            ],
            'lazo' => [
                'name' => 'Lazo',
                'email' => 'lazo@sfrj.ex'
            ]
        ], $iteratorClass);
        $reachedLastElement = false;
        foreach ($map as $k => $v) {
            if ($k == 'mika') {
                $map->remove('pera');
            }
            if ($k == 'lazo') {
                $reachedLastElement = true;
                $map->remove($k);
            }
        }
        $this->assertTrue($reachedLastElement);
        $this->assertCount(1, $map);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testRemovePrevious($iteratorClass): void
    {
        $set = new Set([ 1, 2, 3, 4, 5 ], $iteratorClass);
        $previous = false;
        foreach ($set as $elem) {
            if ($previous) $set->remove($previous);
            $previous = $elem;
        }
        $this->assertTrue($set->equals(new Set([ 5 ])));
    }

    /**
     *  Test that would break iteration in SplObjectStorage
     *  https://www.php.net/manual/en/splobjectstorage.detach.php
     *
     *  @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testRemovingCurrent($iteratorClass): void
    {
        $set = new Set([ 1, 2, 3, 4, 5 ], $iteratorClass);
        $set->rewind();
        $iterated = 0;
        $expected = $set->count();
        while ($set->valid()) {
            $iterated++;
            $set->remove($set->current());
            $set->next();
        }
        $this->assertEquals($expected, $iterated);
    }
}
