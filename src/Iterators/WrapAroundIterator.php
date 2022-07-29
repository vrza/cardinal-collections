<?php

namespace CardinalCollections\Iterators;

/**
 * Extension of PredefinedKeyPositionIterator to implement an "infinite"
 * iterator where the last element is followed by the first one.
 */
class WrapAroundIterator extends PredefinedKeyPositionIterator implements CardinalIterator
{
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
