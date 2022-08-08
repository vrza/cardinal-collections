<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Map;
use CardinalCollections\Mutable\Set;

class IteratorTest extends TestCase
{
    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testIterator(string $iteratorClass): void
    {
        $initialArray = [
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
        ];
        $a = new Map($initialArray, $iteratorClass);
        $keys = '';
        $names = '';
        foreach ($a as $key => $value) {
            $keys .= $key;
            $names .= $value['name'];
        }
        $this->assertEquals('peramikalazo', $keys);
        $this->assertEquals('PeraMikaLazo', $names);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testIteratorAfterRemoveAndAddSameElement($iteratorClass): void
    {
        $repeated = 42;
        $set = new Set([], $iteratorClass);
        $set->add(22);
        $set->add(32);
        $set->add($repeated);
        foreach ($set as $elem) {
            $set->remove($elem);
        }
        $set->add($repeated);
        $this->assertCount(1, $set);
        $result = 0;
        foreach ($set as $elem) {
            $result = $elem;
        }
        $this->assertEquals($repeated, $result);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testIterationsCountAfterFirstElementRemoval($iteratorClass): void
    {
        $map = new Map([90 => 0, 501 => 0, 502 => 0], $iteratorClass);
        self::assertCount(3, $map);
        $map->remove(90);
        self::assertCount(2, $map);
        $i = 0;
        $result = $map->map(function ($pid, $exitCode) use (&$i) {
            $i++;
            return [$pid, $exitCode];
        });
        self::assertCount(2, $result);
        self::assertEquals(2, $i);
    }
}
