<?php

namespace CardinalCollections\Mutable;

use Countable;
use Iterator;

use CardinalCollections\HigherOrderMethods;
use CardinalCollections\Utilities;

class Set implements Countable, Iterator
{
    use HigherOrderMethods;

    private $map;
    private $dummyValue = true; 

    public function __construct(iterable $iterable = [], string $iteratorClass = null)
    {
        $this->map = new Map([], $iteratorClass);
        foreach ($iterable as $_key => $value) {
            $this->map[$value] = $this->dummyValue;
        }
    }

    public function getIteratorClass(): string
    {
        return $this->map->getIteratorClass();
    }

    public function dump(): void
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
        return $this->map->key();
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

    public function remove($element): Set
    {
        $this->map->remove($element);
        return $this;
    }

    public function delete($element): Set
    {
        return $this->remove($element);
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
    public function contains($element): bool
    {
        return $this->has($element);
    }

    public function subsetOf(Set $that): bool
    {
        return $this->reduce(function ($acc, $elem) use ($that) {
            return $acc && $that->contains($elem);
        }, true);
    }

    public function equals(Set $that): bool
    {
        return $this->subsetOf($that) && $that->subsetOf($this);
    }

    public function union(Set $that): Set
    {
        return $this->reduce(function ($acc, $elem) {
            return $acc->add($elem);
        }, clone($that));
    }

    public function intersect(Set $that)
    {
        return $this->filter(function ($elem) use ($that) {
            return $that->contains($elem);
        });
    }

    public function difference(set $that)
    {
        return $this->filter(function ($elem) use ($that) {
            return !$that->contains($elem);
        });
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
