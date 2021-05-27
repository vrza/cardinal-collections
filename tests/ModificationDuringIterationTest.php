<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Map;
use CardinalCollections\Mutable\Set;

/*
 * Test to help improve iterator sanity.
 * Good writeup on PHP foreach:
 * https://stackoverflow.com/questions/10057671/how-does-php-foreach-actually-work
 */
class ModificationDuringIterationTest extends TestCase
{
    public function testNestedLoops(): void
    {
        $initialArray = [
            1, 2, 3, 4, 5
        ];
        $set = new Set($initialArray);
        foreach ($set as $v1) {
            foreach ($set as $v2) {
                if ($v1 == 1 && $v2 == 1) {
                    $set->remove(1);
                }
                //echo "($v1, $v2)\n";
                /* TODO
                 * PHP 5 output: (1, 1) (1, 3) (1, 4) (1, 5)
                 * PHP 7 output: (1, 1) (1, 3) (1, 4) (1, 5)
                 *               (3, 1) (3, 3) (3, 4) (3, 5)
                 *               (4, 1) (4, 3) (4, 4) (4, 5)
                 *               (5, 1) (5, 3) (5, 4) (5, 5)
                 * our output:   (1, 1) (1, 2) (1, 3) (1, 4) (1, 5)
                 */
            }
        }
        $this->assertCount(4, $set);
    }

    public function testRemoveAddSameKey(): void
    {
        $array = ['EzEz' => 1, 'EzFY' => 2, 'FYEz' => 3];
        $map = new Map($array);
        foreach ($map as $value) {
            unset($map['EzFY']);
            $map['FYFY'] = 4;
            //var_dump($value);
            /* TODO
             * PHP 5 output: 1, 4   -- we have this behavior now
             * PHP 7 output : 1, 3, 4
             *
             * Previously the HashPointer restore mechanism jumped
             * right to the new element because it "looked" like
             * it's the same as the removed element (due to colliding
             * hash and pointer). As we no longer rely on the element
             * hash for anything, this is no longer an issue.
             */
        }
        $this->assertCount(3, $map);
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
