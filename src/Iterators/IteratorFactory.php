<?php

namespace CardinalCollections\Iterators;

use Exception;

class IteratorFactory
{
    public static function create(string $iteratorClass = null) {
        $prefix = 'CardinalCollections\\Iterators\\';
        if ($iteratorClass === null) {
            $iteratorClass = 'PredefinedKeyPositionIterator';
        }
        $class = preg_match('/^'.preg_quote($prefix).'/', $iteratorClass)
            ? $iteratorClass
            : 'CardinalCollections\\Iterators\\' . $iteratorClass;
        if (class_exists($class)) {
            return new $class();
        } else {
            throw new Exception("Class $class does not exist");
        }
    }
}
