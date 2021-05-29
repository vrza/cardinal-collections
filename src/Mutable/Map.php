<?php

namespace CardinalCollections\Mutable;

use ArrayAccess;
use Countable;
use Iterator;

use CardinalCollections\Collection;
use CardinalCollections\Utilities;
use CardinalCollections\Iterators\FastRemovalIterator;

class Map implements ArrayAccess, Countable, Iterator
{
    use Collection;

    private $hashmap;
    private $originalKeys;
    private $iterator;

    public function __construct(array $array = [])
    {
        $this->hashmap = $array;
        $this->originalKeys = [];
        foreach ($array as $key => $_value) {
            $this->originalKeys[$key] = $key;
        }
        $this->iterator = new FastRemovalIterator($this->hashmap);
    }

    // ArrayAccess interface
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            return $this->append($value);
        } else {
            $key = Utilities::isValidArrayKey($offset) ? $offset : Utilities::hashAny($offset);
            $this->originalKeys[$key] = $offset;
            $this->iterator->addIfAbsent($key);
            return $this->hashmap[$key] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        $key = Utilities::isValidArrayKey($offset) ? $offset : Utilities::hashAny($offset);
        return isset($this->hashmap[$key]);
    }

    public function offsetUnset($offset)
    {
        $key = Utilities::isValidArrayKey($offset) ? $offset : Utilities::hashAny($offset);
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
        $key = Utilities::isValidArrayKey($offset) ? $offset : Utilities::hashAny($offset);
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
        return $this->iterator->key();
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

    public function append($value)
    {
        $this->hashmap[] = $value;
        $key = array_keys($this->hashmap)[count($this->hashmap) - 1];
        $this->originalKeys[$key] = $key;
        $this->iterator->addIfAbsent($key);
    }

    public function put($key, $value)
    {
        return $this->offsetSet($key, $value);
    }

    public function add($key, $value)
    {
        return $this->put($key, $value);
    }

    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->hashmap)
            ? $this->offsetGet($key)
            : $default;
    }

    public function has($key): bool
    {
        return $this->offsetExists($key);
    }

    public function remove($key)
    {
        return $this->offsetUnset($key);
    }

    public function delete($key)
    {
        $this->remove($key);
    }

    public function putIfAbsent($key, $value)
    {
        if (!$this->offsetExists($key)) {
            $this->hashmap[$key] = $value;
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

    public function keyOriginal()
    {
        $key = $this->key();
        return is_null($key) ? null : $this->originalKeys[$key];
    }

    public function __toString(): string
    {
        $acc = '( ';
        $sep = PHP_EOL;
        foreach ($this as $key => $value) {
            $acc .= $sep . '  ' .
                Utilities::stringRepresentation($this->originalKeys[$key]) .
                ' -> ' . Utilities::stringRepresentation($value);
        }
        $acc .= $sep . ')';
        return $acc;
    }

}
