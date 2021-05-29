<?php

namespace CardinalCollections\Iterators;

/*
 *  Iterator implementation with element removal in O(1) time.
 *
 *  Removed elements are assigned a special value REMOVED,
 *  used by rewind() and next() skip over removed elements.
 */
class FastRemovalIterator
{
    const REMOVED = -1;

    private $keyToPosition = [];

    public function __construct($hashmap)
    {
        foreach ($hashmap as $key => $_value) {
            $this->addNew($key);
        }
    }

    private function forwardToValidPosition() {
        while (current($this->keyToPosition) === self::REMOVED) {
            next($this->keyToPosition);
        }
    }

    public function dump()
    {
        var_dump($this->position);
        var_dump($this->keyToPosition);
    }

    public function rewind()
    {
        reset($this->keyToPosition);
        $this->forwardToValidPosition();
    }

    public function key()
    {
        return $this->valid() ? key($this->keyToPosition) : null;
    }

    public function next()
    {
        next($this->keyToPosition);
        $this->forwardToValidPosition();
    }

    public function valid()
    {
        return !is_null(key($this->keyToPosition))
            && current($this->keyToPosition) !== self::REMOVED;
    }

    public function addIfAbsent($key)
    {
        if (!array_key_exists($key, $this->keyToPosition)) {
            $this->addNew($key);
        }
    }

    private function addNew($key)
    {
        $count = count($this->keyToPosition);
        $this->keyToPosition[$key] = $count;
    }

    public function remove($key)
    {
        if (array_key_exists($key, $this->keyToPosition)) {
            $this->keyToPosition[$key] = self::REMOVED;
        }
    }

}
