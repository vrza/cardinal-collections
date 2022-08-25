<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Iterators\CircularIterator;
use CardinalCollections\Mutable\Set;

/**
 *  Tests for wrap around / infinite iterator.
 */
class CircularIteratorTest extends TestCase
{
    public function testCircularIterator(): void
    {
        $initialArray = ['foo', 'bar', 'baz'];
        $a = new Set($initialArray, CircularIterator::class);
        $this->assertEquals('foo', $a->current());
        $a->next();
        $this->assertEquals('bar', $a->current());
        $a->next();
        $this->assertEquals('baz', $a->current());
        $a->next();
        $this->assertEquals('foo', $a->current());
        $a->next();
        $this->assertEquals('bar', $a->current());
        $a->rewind();
        $this->assertEquals('foo', $a->current());
    }

    public function testCircularIteratorWhileMutating(): void
    {
        $initialArray = ['foo', 'bar', 'baz'];
        $a = new Set($initialArray, CircularIterator::class);
        $this->assertEquals('foo', $a->current());
        $a->remove('foo');
        $this->assertEquals('bar', $a->current());
        $a->next();
        $this->assertEquals('bar', $a->current());
        $a->next();
        $this->assertEquals('baz', $a->current());
        $a->next();
        $this->assertEquals('bar', $a->current());
        $a->rewind();
        $this->assertEquals('bar', $a->current());
    }
}
