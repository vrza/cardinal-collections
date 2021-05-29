<?php

namespace CardinalCollections\Iterators;

/*
 *  An Iterator that decrements the position whenever a key
 *  at or before the cursor is removed.
 *
 *  This design has O(n) removal time, as all elements are
 *  re-enumerated on each removal.
 */
class DecrementPositionIterator
{
    private $keyToPosition = [];
    private $position = 0;

    public function __construct($hashmap)
    {
        foreach ($hashmap as $key => $_value) {
            $this->addNew($key);
        }
    }

    private function enumerateKeyToPosition() {
        $i = 0;
        foreach ($this->keyToPosition as $key => $position) {
            $this->keyToPosition[$key] = $i++;
        }
    }

    public function dump()
    {
        var_dump($this->position);
        var_dump($this->keyToPosition);
    }

    private static function handleInvalidPosition($position) {
        fwrite(STDERR, 'Unexpected iterator position: ' . $position . PHP_EOL);
        throw new \Exception("Unexpected iterator position: " . position);
    }

    public function rewind()
    {
        $this->position = 0;
        reset($this->keyToPosition);
    }

    public function key()
    {
        return $this->valid() ? key($this->keyToPosition) : null;
    }

    public function next()
    {
        if ($this->position >= 0) {
            next($this->keyToPosition);
        } elseif ($this->position == -1) {
            reset($this->keyToPosition);
        } else {
            self::handleInvalidPosition($this->position . PHP_EOL);
        }
        ++$this->position;
    }

    public function valid()
    {
        return $this->position < count($this->keyToPosition);
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
            $keyPosition = $this->keyToPosition[$key];
            unset($this->keyToPosition[$key]);
            $this->enumerateKeyToPosition();
            if ($keyPosition <= $this->position) {
                --$this->position;
                if ($this->position < -1) {
                    self::handleInvalidPosition($this->position . PHP_EOL);
                }
            }
        }
    }

}
