<?php

use CardinalCollections\Wrappers\CircularIterableWrapper;
use PHPUnit\Framework\TestCase;

class CircularIterableWrapperTest extends TestCase
{
    public function testCircularIterator(): void
    {
        $initialArray = ['foo', 'bar', 'baz'];
        $wrapper = new CircularIterableWrapper($initialArray);
        $this->assertEquals('foo', $wrapper->current());
        $wrapper->next();
        $this->assertEquals('bar', $wrapper->current());
        $wrapper->next();
        $this->assertEquals('baz', $wrapper->current());
        $wrapper->next();
        $this->assertEquals('foo', $wrapper->current());
        $wrapper->next();
        $this->assertEquals('bar', $wrapper->current());
        $wrapper->rewind();
        $this->assertEquals('foo', $wrapper->current());
    }
}
