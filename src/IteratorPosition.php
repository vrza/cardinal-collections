<?php

namespace CardinalCollections;

class IteratorPosition
{
    private $positionToKey = [];
    private $keyToPosition = [];
    private $position = 0;

    public function __construct($hashmap)
    {
        foreach ($hashmap as $key => $_value) {
            $this->addNew($key);
        }
    }

    public function dump()
    {
        var_dump($this->position);
        var_dump($this->positionToKey);
        var_dump($this->keyToPosition);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function getPositionByKey($key): int
    {
        return $this->keyToPosition[$key];
    }

    public function getKeyByPosition($position)
    {
        return $this->positionToKey[$position];
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function key()
    {
        return $this->positionToKey[$this->position];
    }

    public function next()
    {
        $this->position++;
    }

    public function valid()
    {
        return $this->position < count($this->positionToKey);
    }

    public function add($key)
    {
        $keyDoesNotExist = ! array_key_exists($key, $this->keyToPosition);
        if ($keyDoesNotExist) {
            $this->addNew($key);
        }
    }

    public function remove($key)
    {
        $keyExists = array_key_exists($key, $this->keyToPosition);
        if ($keyExists) {
            $keyPosition = $this->keyToPosition[$key];
            unset($this->keyToPosition[$key]);
            $this->positionToKey = array_keys($this->keyToPosition);
            if ($keyPosition <= $this->position) {
                $this->position--;
                if ($this->position < -1) {
                    fwrite(STDOUT, "Unexpected iterator position: " . $this->position . PHP_EOL);
                    throw new \Exception("Unexpected iterator position: " . $this->position);
                }
            }
        }
    }

    private function addNew($key)
    {
        $this->positionToKey[] = $key;
        $this->keyToPosition[$key] = $this->position++;
    }

}
