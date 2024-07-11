<?php

namespace CardinalCollections\Iterators;

use Exception;

class IteratorFactory
{
    public static function create(?string $iteratorClass = null) {
        $class = $iteratorClass === null
            ? PredefinedKeyPositionIterator::class
            : $iteratorClass;
        if (class_exists($class)) {
            return new $class();
        } else {
            throw new Exception("Class $class does not exist");
        }
    }
}
