<?php

namespace CardinalCollections;

class CircularIterableWrapper extends IterableWrapper
{
    public function next(): void
    {
        parent::next();
        if (!$this->valid()) {
            $this->rewind();
        }
    }
}
