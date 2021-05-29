<?php


namespace CardinalCollections\Mutable;

use Countable;
use Iterator;

use CardinalCollections\Collection;
use CardinalCollections\Utilities;

class Set implements Countable, Iterator
{
    use Collection;

    private $map;
    private $dummyValue = true; 

    public function __construct(array $array = [])
    {
        $this->map = new Map();
        foreach ($array as $_key => $value) {
            $this->map[$value] = $this->dummyValue;
        }
    }

    public function dump()
    {
        $this->map->dump();
    }

    // Iterator interface
    public function rewind()
    {
        return $this->map->rewind();
    }

    public function current()
    {
        $key = $this->map->key();
        return $this->map->getOriginalKey($key);
    }

    public function key()
    {
        return $this->map->key();
    }

    public function next()
    {
        return $this->map->next();
    }

    public function valid(): bool
    {
        //return $this->map->key() !== null;
        return $this->map->valid();
    }

    // Countable interface
    public function count(): int
    {
        return count($this->map);
    }

    public function currentTuple(): array
    {
        return [$this->current()];
    }

    public function add($element): Set
    {
        $this->map[$element] = $this->dummyValue;
        return $this;
    }

    public function has($element): bool
    {
        return $this->map->has($element);
    }

    public function remove($element)
    {
        $this->map->remove($element);
    }

    public function delete($element)
    {
        return $this->remove($element);
    }

    public function clone(Set $that): Set
    {
        return clone($that);
    }

    public function asArray(): array
    {
        return $this->map->keys();
    }

    public function isEmpty(): bool
    {
        return $this->map->isEmpty();
    }

    public function nonEmpty(): bool
    {
        return $this->map->nonEmpty();
    }

    // set operations
    public function equals(Set $that): bool
    {
        $_this = &$this;
        return
            $_this->reduce(function($acc, $elem) use ($that) {
                return $acc && $that->has($elem);
            }, true)
            &&
            $that->reduce(function($acc, $elem) use ($_this) {
                return $acc && $_this->has($elem);
            }, true);
    }

    public function union(Set $that): Set
    {
        return $this->reduce(function($acc, $elem) {
            return $acc->add($elem);
        }, $that->clone($that));
    }

    public function __toString(): string
    {
        $acc = '{ ';
        $sep = '';
        foreach ($this as $elem) {
            $acc .= $sep . Utilities::stringRepresentation($elem);
            $sep = ', ';
        }
        $acc .= ' }';
        return $acc;
    }

}
