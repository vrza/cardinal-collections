<?php

namespace CardinalCollections\Tests\DataProviders;

class IteratorImplementationProvider
{
    public static function iteratorClassName(): array
    {
        return [
            ['CardinalCollections\Iterators\PredefinedKeyPositionIterator'],
            ['CardinalCollections\Iterators\FastRemovalIterator']
        ];
    }
}
