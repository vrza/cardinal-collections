<?php

namespace CardinalCollections\Wrappers;

use Iterator;

class IterableWrapper implements Iterator
{
    /**
     * @var array|iterable
     */
    private $iterable;

    /**
     * @param iterable $iterable
     */
    public function __construct(iterable $iterable = [])
    {
        $this->iterable = $iterable;
    }

    /**
     * @return false|mixed
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->isIterable()
            ? $this->iterable->current()
            : current($this->iterable);
    }

    /**
     * @return int|string|null
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->isIterable()
            ? $this->iterable->key()
            : key($this->iterable);
    }

    /**
     * @return void
     */
    public function next(): void
    {
        if ($this->isIterable()) {
            $this->iterable->next();
        } else {
            next($this->iterable);
        }
    }

    /**
     * @return void
     */
    public function rewind(): void
    {
        if ($this->isIterable()) {
            $this->iterable->rewind();
        } else {
            reset($this->iterable);
        }
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return $this->isIterable()
            ? $this->iterable->valid()
            : current($this->iterable) !== false;
    }

    /**
     * @return bool
     */
    protected function isIterable(): bool
    {
        return !is_array($this->iterable);
    }
}
