<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Set;

class SizeTest extends TestCase
{
    public function testSize(): void
    {
        $set = new Set();
        $offset = 13;
        $count = 16000;
        for ($i = $offset; $i < $offset + $count; $i++) {
            $set->add($i);
            if ($i % 2 === 0) {
                $set->remove($i - 1);
            }
        }
        $this->assertCount($count / 2, $set);
    }

    public function testCompactionPreservesIterator(): void
    {
        $set = new Set([ 1, 2, 3, 4, 5]);
        $current = -1;
        foreach ($set as $x) {
            if ($x == 3) {
                $offset = 13;
                $count = 16000;
                for ($i = $offset; $i < $offset + $count; $i++) {
                    $set->add($i);
                    if ($i % 2 === 1) {
                        $set->remove($i - 1);
                    }
                }
                $current = $set->current();
                echo "CURRENT = $current\n";
                break;
            }
        }
        $this->assertEquals(3, $current);
    }
}
