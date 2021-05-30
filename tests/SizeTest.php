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
            if ($i % 2 === 1) {
                $set->remove($i - 1);
            }
        }
        $this->assertCount($count / 2 + 1, $set);
    }
}
