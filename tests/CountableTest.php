<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Map;

class CountableTest extends TestCase
{
    public function testCountable(): void
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
        $a = new Map($initialArray);
        $this->assertEquals(count($a), 3);
    }
}
