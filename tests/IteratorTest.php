<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Map;

class IteratorTest extends TestCase
{
    public function testIterator(): void
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
        $keys = '';
        $names = '';
        foreach ($a as $key => $value) {
            $keys .= $key;
            $names .= $value['name'];
        }
        $this->assertEquals('peramikalazo', $keys);
        $this->assertEquals('PeraMikaLazo', $names);
    }

    public function testRemove(): void
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
        $reachedLastElement = false;
        foreach ($a as $k => $v) {
            if ($k == 'mika') {
                $a->remove('pera');
            }
            if ($k == 'lazo') {
                $reachedLastElement = true;
                $a->remove($k);
            }
        }
        $this->assertTrue($reachedLastElement);
        $this->assertCount(1, $a);
    }
}
