<?php

namespace CardinalCollections\Mutable;

use ArrayAccess;
use Countable;
use Iterator;

use CardinalCollections\Collection;
use CardinalCollections\HashUtils;
use CardinalCollections\IterableUtils;
use CardinalCollections\Utilities;
use CardinalCollections\Iterators\IteratorFactory;

class Map implements ArrayAccess, Countable, Iterator
{
    use Collection;

    private $hashmap = [];
    private $originalKeys = [];
    private $iterator;

    public function __construct(iterable $map = [], string $iteratorClass = 'PredefinedKeyPositionIterator')
    {
        $this->iterator = IteratorFactory::create($iteratorClass);
        foreach ($map as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    // ArrayAccess interface
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            return $this->append($value);
        } else {
            $internalKey = HashUtils::isDirectKey($offset) ? $offset : HashUtils::hashAny($offset);
            $this->originalKeys[$internalKey] = $offset;
            $this->iterator->addIfAbsent($internalKey);
            return $this->hashmap[$internalKey] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        $key = HashUtils::isDirectKey($offset) ? $offset : HashUtils::hashAny($offset);
        return isset($this->hashmap[$key]);
    }

    public function offsetUnset($offset)
    {
        $key = HashUtils::isDirectKey($offset) ? $offset : HashUtils::hashAny($offset);
        $existing = array_key_exists($key, $this->hashmap);
        if ($existing) {
            unset($this->originalKeys[$key]);
            unset($this->hashmap[$key]);
            $this->iterator->remove($key);
        }
    }

    public function offsetGet($offset)
    {
        if (is_null($offset)) {
            return null;
        }
        $key = HashUtils::isDirectKey($offset) ? $offset : HashUtils::hashAny($offset);
        return $this->hashmap[$key] ?? null;
    }

    // Iterator interface
    public function rewind()
    {
        $this->iterator->rewind();
        reset($this->originalKeys);
        return reset($this->hashmap);
    }

    public function current()
    {
        $key = $this->iterator->key();
        return $this->hashmap[$key];
    }

    public function key()
    {
        $key = $this->iterator->key();
        return $key === null ? null : $this->originalKeys[$key];
    }

    public function next()
    {
        $this->iterator->next();
        return next($this->hashmap);
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    // Countable interface
    public function count(): int
    {
        return count($this->hashmap);
    }

    public function currentTuple(): array
    {
        return [$this->key(), $this->current()];
    }

    public function dump()
    {
        var_dump($this->hashmap);
        var_dump($this->originalKeys);
    }

    public function isEmpty(): bool
    {
        return empty($this->hashmap);
    }

    public function nonEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    public function keyLast()
    {
        $lastInternalKey = IterableUtils::lastKey($this->hashmap);
        return $lastInternalKey === null
            ? null
            : $this->originalKeys[$lastInternalKey];
    }

    public function append($value): Map
    {
        $this->hashmap[] = $value;
        $key = IterableUtils::lastKey($this->hashmap);
        $this->originalKeys[$key] = $key;
        $this->iterator->addIfAbsent($key);
        return $this;
    }

    public function put($key, $value): Map
    {
        $this->offsetSet($key, $value);
        return $this;
    }

    public function add($key, $value): Map
    {
        return $this->put($key, $value);
    }

    public function get($key, $default = null)
    {
        return $this->offsetExists($key)
            ? $this->offsetGet($key)
            : $default;
    }

    public function has($key): bool
    {
        return $this->offsetExists($key);
    }

    public function remove($key): Map
    {
        $this->offsetUnset($key);
        return $this;
    }

    public function delete($key): Map
    {
        return $this->remove($key);
    }

    public function putIfAbsent($key, $value)
    {
        if (!$this->offsetExists($key)) {
            $this->offsetSet($key, $value);
            return null;
        } else {
            return $this->offsetGet($key);
        }
    }

    public function keys(): array
    {
        $result = [];
        foreach ($this->hashmap as $key => &$_value) {
            $result[] = $this->originalKeys[$key];
        }
        return $result;
    }

    public function values(): array
    {
        return array_values($this->hashmap);
    }

    public function __toString(): string
    {
        $acc = '( ';
        $sep = PHP_EOL;
        foreach ($this as $key => $value) {
            $acc .= $sep . '  ' .
                Utilities::stringRepresentation($key) .
                ' -> ' . Utilities::stringRepresentation($value);
        }
        $acc .= $sep . ')';
        return $acc;
    }
}
