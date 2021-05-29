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
    private $position = 0;

    public function __construct($hashmap)
    {
        foreach ($hashmap as $key => $_value) {
            $this->addNew($key);
        }
    }

    private function forwardToValidPosition() {
        while (current($this->keyToPosition) === self::REMOVED) {
            next($this->keyToPosition);
            ++$this->position;
        }
    }

    public function dump()
    {
        var_dump($this->position);
        var_dump($this->keyToPosition);
    }

    public function rewind()
    {
        $this->position = 0;
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
        ++$this->position;
        $this->forwardToValidPosition();
    }

    public function valid()
    {
        return $this->position < count($this->keyToPosition)
            && current($this->keyToPosition) !== self::REMOVED;
    }

    public function add($key)
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
