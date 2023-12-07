<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Wrappers\IterableWrapper;
use CardinalCollections\Mutable\Map;

class IterableWrapperTest extends TestCase
{

    public function testBasicIterationWithArray(): void
    {
        $wrapper = new IterableWrapper([
            'foo' => 'bar',
            'baz' => 'boo'
        ]);
        $copy = new Map();
        foreach ($wrapper as $key => $value) {
            $copy[$key] = $value;
        }
        $this->assertCount(2, $copy);
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testBasicIterationWithTraversable($iteratorClass): void
    {
        $map = new Map([
            'foo' => 'bar',
            'baz' => 'boo'
        ], $iteratorClass);
        $wrapper = new IterableWrapper($map);
        $copy = new Map();
        foreach ($wrapper as $key => $value) {
            $copy[$key] = $value;
        }
        $this->assertCount(2, $copy);
    }

}
