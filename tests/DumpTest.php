<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Iterators\IteratorFactory;
use CardinalCollections\Mutable\Map;
use CardinalCollections\Mutable\Set;

final class DumpTest extends TestCase
{
    public function testDumpMap(): void
    {
        $map = new Map();
        $map->put(
            22,
            [
                'name' => 'twenty-two',
                'state' => [
                    'pid' => 12345
                ]
            ]
        );
        ob_start();
        $map->dump();
        $dump = ob_get_clean();
        $this->assertIsInt(
            strpos($dump, 'int(12345)')
        );
        $this->assertIsInt(
            strpos($dump, 'int(22)')
        );
    }

    public function testDumpSet(): void
    {
        $set = new Set();
        $set->add(
            [
                'name' => 'twenty-two',
                'state' => [
                    'pid' => 12345
                ]
            ]
        );
        ob_start();
        $set->dump();
        $dump = ob_get_clean();
        $this->assertIsInt(
            strpos($dump, 'int(12345)')
        );
    }

    /**
     * @dataProvider CardinalCollections\Tests\DataProviders\IteratorImplementationProvider::iteratorClassName
     */
    public function testDumpIterator(string $iteratorClass): void
    {
        $iterator = IteratorFactory::create($iteratorClass);
        $iterator->addIfAbsent('foo');
        ob_start();
        $iterator->dump();
        $dump = ob_get_clean();
        $this->assertIsInt(
            strpos($dump, 'foo')
        );
    }
}
