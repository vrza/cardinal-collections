<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Map;

final class DumpTest extends TestCase
{
    public function testDump(): void
    {
        $a = new Map();
        $a->put(
            22,
            [
                'name' => 'twenty-two',
                'state' => [
                    'pid' => 12345
                ]
            ]
        );
        ob_start();
        $a->dump();
        $dump = ob_get_clean();
        $this->assertIsInt(
            strpos($dump, 'int(12345)')
        );
        $this->assertIsInt(
            strpos($dump, 'int(22)')
        );
    }
}
