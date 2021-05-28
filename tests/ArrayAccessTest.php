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
        $map = new Map($initialArray);
        $this->assertTrue(isset($map['pera']));
        $this->assertFalse(isset($map['mika']));
        $this->assertCount(1, $map);
        $map['mika'] = [
            'name' => 'Mika',
            'email' => 'mika@frg.ex'
        ];
        $this->assertCount(2, $map);
        $this->assertTrue(isset($map['mika']));
        $map['lazo'] = [
            'name' => 'Lazo',
            'email' => 'lazo@sfrj.ex'
        ];
        $this->assertCount(3, $map);
        unset($map['mika']);
        $this->assertFalse(isset($map['mika']));
        $this->assertCount(2, $map);
        $l = $map['lazo'];
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
        $map = new Map($initialArray);
        $map[] = [
            'name' => 'Mika',
            'email' => 'mika@frg.ex'
        ];
        $primaryKeys = $map->keys();
        $this->assertCount(2, $primaryKeys);
        $this->assertEquals('pera', $primaryKeys[0]);
        $this->assertEquals(0, $primaryKeys[1]);
    }

    public function testNullOffset(): void
    {
        $map = new Map(['one', 'two']);
        unset($map[null]);
        $this->assertCount(2, $map);
        $this->assertNull($map[null]);
    }

    public function testIterator(): void
    {
        $list = new Map();
        $list[] = 'foo';
        $list[] = 'bar';
        $list[] = 'baz';
        $this->assertCount(3, $list);
        $string = '';
        foreach ($list as $element) {
            $string .= $element;
        }
        $this->assertEquals('foobarbaz', $string);
    }
}
