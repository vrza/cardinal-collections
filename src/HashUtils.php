<?php

namespace CardinalCollections;

use ReflectionFunction;
use InvalidArgumentException;

class HashUtils
{
    const DEFAULT_ALGO = 'sha256';

    /**
     * Return true if the argument can be used as a key directly,
     * without the need to hash it first
     *
     * @param mixed $x
     * @return bool
     */
    public static function isDirectKey($x): bool
    {
        return is_int($x);
    }

    public static function hashAny($value, string $algo = self::DEFAULT_ALGO)
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
                return (is_callable($value)) ?
                    self::hashCallable($value, $algo) :
                    self::hashObject($value, $algo);
            default:
                throw new InvalidArgumentException("Don't know how to hash unknown type $type");
        }
    }

    /**
     * Returns a sha256 of an object.
     *
     * @param object $object
     * @param string $algo
     * @return false|string
     */
    public static function hashObject($object, string $algo = self::DEFAULT_ALGO)
    {
        $contents = json_encode(static::sortRecursive((array) $object));
        $class = get_class($object);
        return hash($algo, "$class: $contents");
    }

    /**
     * Returns a sha256 of a callable
     *
     * @param callable $callable
     * @param string $algo
     * @return false|string
     */
    public static function hashCallable(callable $callable, string $algo = self::DEFAULT_ALGO)
    {
        $rf = new ReflectionFunction($callable);
        return hash($algo, $rf->__toString());
    }

    /**
     * Returns a sha256 of an array.
     *
     * @param array $array
     * @param string $algo
     * @return false|string
     */
    public static function hashArrayByContent(array $array, string $algo = self::DEFAULT_ALGO)
    {
        return hash($algo, json_encode(static::sortRecursive($array)));
    }

    /**
     * Recursively sort an array by keys and values.
     *
     * Borrowed from https://github.com/illuminate/support/blob/6.x/Arr.php#L606
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
     * Borrowed from https://github.com/illuminate/support/blob/6.x/Arr.php#L385
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
