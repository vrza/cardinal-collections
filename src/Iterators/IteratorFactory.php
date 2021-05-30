<?php

namespace CardinalCollections\Iterators;

use Exception;

class IteratorFactory
{
    public static function create(string $iteratorClass, array $array) {
        $class = 'CardinalCollections\\Iterators\\' . $iteratorClass;
        if (class_exists($class)) {
            return new $class($array);
        } else {
            throw new Exception("Class $class does not exist");
        }
    }
}
