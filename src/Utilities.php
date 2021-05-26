<?php

namespace CardinalCollections;

use InvalidArgumentException;

class Utilities {

    /**
     * Return true if the argument is of integer or string type
     *
     * @param mixed $x
     * @return bool
     */
    public static function isValidArrayKey($x): bool
    {
        return is_int($x) || is_string($x);
    }

    public static function hashAny($value, string $algo = 'sha256')
    {
        $type = gettype($value);
        switch ($type) {
            case 'NULL':
                return null;
            case 'integer':
            case 'double':
            case 'string':
            case 'boolean':
                return hash($algo, $value);
            case 'array':
                return self::hashArrayByContent($value, $algo);
            case 'object':
                return self::hashObject($value, $algo);
            default:
                throw new InvalidArgumentException("Don't know how to hash unknown type $type");
        }
    }

    /**
     * Returns a sha256 of an object.
     *
     * Considers contents and class of object.
     *
     * @param object $object
     * @param string $algo
     * @return false|string
     */
    public static function hashObject($object, string $algo = 'sha256')
    {
        $contents = json_encode(static::sortRecursive((array) $object));
        $class = get_class($object);
        return hash($algo, "$class: $contents");
    }

    /**
     * Returns a sha256 of an array.
     *
     * @param array $array
     * @param string $algo
     * @return false|string
     */
    public static function hashArrayByContent(array $array, string $algo = 'sha256')
    {
        return hash($algo, json_encode(static::sortRecursive($array)));
    }

    /**
     * Recursively sort an array by keys and values.
     *
     * NOTE: borrowed from https://github.com/illuminate/support/blob/6.x/Arr.php#L606
     * needs to be replaced with original one, once laravel gets upgraded.
     *
     * @param  array  $array
     * @return array
     */
    public static function sortRecursive(array $array): array
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = static::sortRecursive($value);
            }
        }

        if (static::isAssoc($array)) {
            ksort($array);
        } /*else {
            sort($array);
        }*/ /* we do care about the order of values in numeric arrays */

        return $array;
    }

    /**
     * Determines if an array is associative.
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     * NOTE: borrowed from https://github.com/illuminate/support/blob/6.x/Arr.php#L385
     * needs to be replaced with original one, once laravel gets upgraded.
     *
     * @param  array  $array
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }
}
