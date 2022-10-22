<?php

use PHPUnit\Framework\TestCase;
use CardinalCollections\Mutable\Map;

class FunctionsAsKeysTest extends TestCase
{
    public function testFunctionsAsKeys(): void
    {
        $mod = new Map();
        $mod[function ($x) {
            return $x < 2;
        }] = function ($x) {
            return $x - 2;
        };
        $mod[function ($x) {
            return $x == 2;
        }] = function ($x) {
            return 100;
        };
        $mod[function ($x) {
            return $x > 2;
        }] = function ($x) {
            return $x + 2;
        };

        $this->assertEquals(44, $this->proc(42, $mod));
        $this->assertEquals(100, $this->proc(2, $mod));
        $this->assertEquals(-2, $this->proc(0, $mod));
    }

    private function proc($x, $mod) {
        foreach ($mod as $test => $f) {
            if ($test($x))
                return $f($x);
        }
        return null;
    }
}
