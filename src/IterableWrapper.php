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

    /**
     * @return false|mixed
     */
    public function current()
    {
        return current($this->iterable);
    }

    /**
     * @return int|string|null
     */
    public function key()
    {
        return key($this->iterable);
    }

    /**
     * @return void
     */
    public function next(): void
    {
        next($this->iterable);
    }

    /**
     * @return void
     */
    public function rewind(): void
    {
        reset($this->iterable);
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return current($this->iterable) !== false;
    }

}
