<?php

namespace CardinalCollections;

use Iterator;

class IterableWrapper implements Iterator
{
    private $iterable;

    public function __construct(iterable $iterable = [])
    {
        $this->iterable = $iterable;
    }

    public function current(): mixed
    {
        return current($this->iterable);
    }

    public function key(): mixed
    {
        return key($this->iterable);
    }

    public function next(): void
    {
        next($this->iterable);
    }

    public function rewind(): void
    {
        reset($this->iterable);
    }

    public function valid(): bool
    {
        return current($this->iterable) !== false;
    }

}
