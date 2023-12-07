<?php

namespace CardinalCollections\Iterators;

/**
 * Extension of PredefinedKeyPositionIterator to implement an "infinite"
 * iterator where the last element is followed by the first one.
 */
class CircularIterator extends PredefinedKeyPositionIterator implements CardinalIterator
{
    /**
     * @return void
     */
    public function next(): void
    {
        if ($this->skipNext) {
            $this->skipNext = false;
        } else {
            next($this->keyToPosition);
            if (!$this->valid()) {
                $this->rewind();
            }
        }
    }
}
