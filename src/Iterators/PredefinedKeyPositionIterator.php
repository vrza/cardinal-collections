<?php

namespace CardinalCollections\Iterators;

/**
 * Simple iterator that relies on IAP in order to iterate through the array.
 * 
 * Iterator uses flags in order to prevent unwanted 'next' pointer movement
 * (like the extra 'next' when we use unset operation on the array)
 * 
 * Iterator uses positionIncrement integer property in order to define key
 * position, regardless of when new value has been added.
 */
class PredefinedKeyPositionIterator implements CardinalIterator
{
    // holds each key and its position
    protected $keyToPosition = [];

    // key position increment holder, each new entry will increment it by 1
    protected $positionIncrement = 0;

    // flag that will prevent 'next' method from moving the $keyToPosition IAP
    protected $skipNext = false;

    public function dump()
    {
        var_dump($this->keyToPosition);
        var_dump($this->positionIncrement);
    }

    public function rewind(): void
    {
        $this->skipNext = false;
        reset($this->keyToPosition);
    }

    public function key()
    {
        return $this->valid() ? key($this->keyToPosition) : null;
    }

    public function next(): void
    {
        if ($this->skipNext) {
            $this->skipNext = false;
        } else {
            next($this->keyToPosition);
        }
    }

    public function valid(): bool
    {
        return !is_null(key($this->keyToPosition));
    }

    public function addIfAbsent($key): bool
    {
        $absent = !array_key_exists($key, $this->keyToPosition);
        if ($absent) {
            $this->addNew($key);
        }
        return $absent;
    }

    protected function addNew($key)
    {
        $this->keyToPosition[$key] = $this->positionIncrement;
        $this->positionIncrement++;
    }

    public function remove($key): void
    {
        $keyExists = array_key_exists($key, $this->keyToPosition);
        if ($keyExists) {
            $keyPositionRequested = $this->keyToPosition[$key];
            $keyPositionCurrent = current($this->keyToPosition);
            unset($this->keyToPosition[$key]);
            if ($keyPositionRequested === $keyPositionCurrent) {
                $this->skipNext = true;
            }
        }
    }
}
