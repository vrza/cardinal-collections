<?php

require_once (__DIR__ . '/../vendor/autoload.php');

use CardinalCollections\Mutable\Set;

function stressIterator(string $iteratorClass, int $count): void
{
    $set = new Set([], $iteratorClass);
    $offset = 13;
    for ($i = $offset; $i < $offset + $count; $i++) {
        $set->add($i);
        if ($i % 2 === 0) {
            $set->remove($i - 1);
        }
    }
}

function runBenchmark(string $iteratorClass, int $count) {
    echo "$iteratorClass, $count elements" . PHP_EOL;
    $start = microtime(true);
    stressIterator($iteratorClass, $count);
    $duration = microtime(true) - $start;
    echo "$duration seconds" . PHP_EOL;
}

const K = 1000;
$count = 100 * K;

runBenchmark('DecrementPositionIterator', $count);

runBenchmark('FastRemovalIterator', $count);
