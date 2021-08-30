<?php

namespace CardinalCollections\Tests\DataProviders;

use CardinalCollections\Iterators\PredefinedKeyPositionIterator;
use CardinalCollections\Iterators\FastRemovalIterator;

class IteratorImplementationProvider
{
    public static function iteratorClassName(): array
    {
        return [
            [PredefinedKeyPositionIterator::class],
            [FastRemovalIterator::class]
        ];
    }
}
