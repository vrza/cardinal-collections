<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Map;

class ArrayAccessTest extends TestCase
{
    public function testBasicArrayAccess(): void
    {
        $initialArray = [
            'pera' => [
                'name' => 'Pera',
                'email' => 'pera@ddr.ex'
            ]
        ];
        $powerMap = new Map($initialArray);
        $this->assertTrue(isset($powerMap['pera']));
        $this->assertFalse(isset($powerMap['mika']));
        $this->assertCount(1, $powerMap);
        $powerMap['mika'] = [
            'name' => 'Mika',
            'email' => 'mika@frg.ex'
        ];
        $this->assertCount(2, $powerMap);
        $this->assertTrue(isset($powerMap['mika']));
        $powerMap['lazo'] = [
            'name' => 'Lazo',
            'email' => 'lazo@sfrj.ex'
        ];
        $this->assertCount(3, $powerMap);
        unset($powerMap['mika']);
        $this->assertFalse(isset($powerMap['mika']));
        $this->assertCount(2, $powerMap);
        $l = $powerMap['lazo'];
        $this->assertEquals('Lazo', $l['name']);
    }

    public function testAppend(): void
    {
        $initialArray = [
            'pera' => [
                'name' => 'Pera',
                'email' => 'pera@ddr.ex'
            ]
        ];
        $powerMap = new Map($initialArray);
        $powerMap[] = [
            'name' => 'Mika',
            'email' => 'mika@frg.ex'
        ];
        $primaryKeys = $powerMap->keys();
        $this->assertCount(2, $primaryKeys);
        $this->assertEquals('pera', $primaryKeys[0]);
        $this->assertEquals(0, $primaryKeys[1]);
    }

    public function testNullOffset(): void
    {
        $powerMap = new Map(['one', 'two']);
        unset($powerMap[null]);
        $this->assertCount(2, $powerMap);
        $this->assertNull($powerMap[null]);
    }
}
