<?php

namespace CardinalCollections;

trait Collection
{

    public function reduce($callbackFn, $initialValue = null)
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

    public function map($callbackFn)
    {
        $class = get_class($this);
        $result = new $class;

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

    public function filter($callbackFn)
    {
        $class = get_class($this);
        $result = new $class;

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
}
