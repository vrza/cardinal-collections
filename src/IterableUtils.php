<?php

namespace CardinalCollections;

class IterableUtils
{
    public static function lastKey(iterable $iterable)
    {
        return (is_array($iterable) && function_exists('array_key_last'))
            ? array_key_last($iterable)
            : self::iterable_keys_reduce($iterable, function ($acc, $key) {
                 return $key;
              });
    }

    public static function keys(iterable $iterable)
    {
        return (is_array($iterable))
            ? array_keys($iterable)
            : self::iterable_keys_reduce($iterable, function ($acc, $key) {
                $acc[] = $key;
                return $acc;
            }, []);
    }

    public static function values(iterable $iterable)
    {
        return (is_array($iterable))
            ? array_values($iterable)
            : self::iterable_values_reduce($iterable, function ($acc, $value) {
                $acc[] = $value;
                return $acc;
            }, []);
    }

    public static function iterable_keys_reduce(iterable $iterable, callable $callback, $acc = null)
    {
        $first = true;
        foreach ($iterable as $key => $_value) {
            if ($first && $acc === null) {
                $acc = $key;
                $first = false;
            } else {
                $acc = $callback($acc, $key);
            }
        }
        return $acc;
    }

    public static function iterable_values_reduce(iterable $iterable, callable $callback, $acc = null)
    {
        $first = true;
        foreach ($iterable as $value) {
            if ($first && $acc === null) {
                $acc = $value;
                $first = false;
            } else {
                $acc = $callback($acc, $value);
            }
        }
        return $acc;
    }

    public static function iterable_reduce(iterable $iterable, callable $callback, $acc = null)
    {
        $first = true;
        foreach ($iterable as $key => $value) {
            if ($first && $acc === null) {
                $acc = $value;
                $first = false;
            } else {
                $acc = $callback($acc, $key, $value);
            }
        }
        return $acc;
    }

    public static function iterable_foreach(iterable $iterable, callable $callback): void
    {
        foreach ($iterable as $key => $value) {
            $callback($key, $value);
        }
    }

    public static function iterable_keys_foreach(iterable $iterable, callable $callback): void
    {
        foreach ($iterable as $key => $_value) {
            $callback($key);
        }
    }

    public static function iterable_values_foreach(iterable $iterable, callable $callback): void
    {
        foreach ($iterable as $value) {
            $callback($value);
        }
    }

    public static function iterable_every(iterable $iterable, callable $callback): bool
    {
        return self::iterable_reduce($iterable, function ($acc, $key, $value) use ($callback) {
            return $acc && $callback($key, $value);
        }, true);
    }

    public static function iterable_some(iterable $iterable, callable $callback): bool
    {
        return self::iterable_reduce($iterable, function ($acc, $key, $value) use ($callback) {
            return $acc || $callback($key, $value);
        }, false);
    }
}
