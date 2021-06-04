<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Map;

class IteratorFactoryTest extends TestCase
{
    public function testInvalidIterator(): void
    {
        $this->expectException(Exception::class);
        $map = new Map([], 'does_not_exist');
    }
}
