<?php

namespace CardinalCollections\Wrappers;

class CircularIterableWrapper extends IterableWrapper
{
    /**
     * @return void
     */
    public function next(): void
    {
        parent::next();
        if (!$this->valid()) {
            $this->rewind();
        }
    }
}
