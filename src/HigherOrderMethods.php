<?php

namespace CardinalCollections;

trait HigherOrderMethods
{
    public function reduce(callable $callback, $initalValue = null)
    {
        return IterableUtils::iterable_reduce($this, $callback, $initalValue);
    }

    public function reduceTuples(callable $callbackFn, $initialValue = null)
    {
        if ($this->isEmpty()) {
            return $initialValue;
        }

        $this->rewind();

        if (is_null($initialValue)) {
            $c = $this->currentTuple();
            $initialValue = count($c) == 1 ? $c[0] : $c;
            $this->next();
        }

        $acc = $initialValue;

        while ($this->valid()) {
            $currentTuple = $this->currentTuple();
            $acc = $callbackFn($acc, ...$currentTuple);
            $this->next();
        }

        return $acc;
    }

    public function map(callable $callback)
    {
        return $this->mapTuples($callback);
    }

    public function mapTuples(callable $callbackFn)
    {
        $result = self::createEmptyInstanceOf($this);

        if ($this->isEmpty()) {
            return $result;
        }

        $this->rewind();

        while ($this->valid()) {
            $currentTuple = $this->currentTuple();
            $mapped = $callbackFn(...$currentTuple);
            if (is_array($mapped)) {
                $result->add(...$mapped);
            } else {
                $result->add($mapped);
            }
            $this->next();
        }

        return $result;
    }

    public function filter(callable $callback)
    {
        return $this->filterTuples($callback);
    }

    public function filterTuples(callable $callbackFn)
    {
        $result = self::createEmptyInstanceOf($this);

        if ($this->isEmpty()) {
            return $result;
        }

        $this->rewind();

        while ($this->valid()) {
            $currentTuple = $this->currentTuple();
            $shouldAdd = $callbackFn(...$currentTuple);
            if ($shouldAdd) {
                $result->add(...$currentTuple);
            }
            $this->next();
        }

        return $result;
    }

    public function foreach(callable $callback): void
    {
        IterableUtils::iterable_foreach($this, $callback);
    }

    public function every(callable $callback): bool
    {
        return IterableUtils::iterable_every($this, $callback);
    }

    public function some(callable $callback): bool
    {
        return IterableUtils::iterable_some($this, $callback);
    }

    private static function createEmptyInstanceOf($collection)
    {
        $rc = new \ReflectionClass($collection);
        $class = $rc->getName();
        return (
            $rc->hasMethod('getIteratorClass') &&
            $rc->getConstructor()->getNumberOfParameters() >= 2
        )
            ? new $class([], $collection->getIteratorClass())
            : new $class;
    }
}
