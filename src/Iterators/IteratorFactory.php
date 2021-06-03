<?php

namespace CardinalCollections\Iterators;

use Exception;

class IteratorFactory
{
    public static function create(string $iteratorClass, $map = []) {
        $class = 'CardinalCollections\\Iterators\\' . $iteratorClass;
        if (class_exists($class)) {
            return new $class($map);
        } else {
            throw new Exception("Class $class does not exist");
        }
    }
}
