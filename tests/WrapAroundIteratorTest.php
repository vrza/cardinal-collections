<?php

use CardinalCollections\Iterators\WrapAroundIterator;
use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Set;

/**
 *  Tests for wrap around / infinite iterator.
 */
class WrapAroundIteratorTest extends TestCase
{
    public function testWrapAroundIterator(): void
    {
        $initialArray = ['foo', 'bar', 'baz'];
        $a = new Set($initialArray, WrapAroundIterator::class);
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

    public function testWrapAroundIteratorWhileMutating(): void
    {
        $initialArray = ['foo', 'bar', 'baz'];
        $a = new Set($initialArray, WrapAroundIterator::class);
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
