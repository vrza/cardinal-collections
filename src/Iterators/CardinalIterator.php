<?php

namespace CardinalCollections\Iterators;

/*
 * Interface between collection and iterator implementations
 */
interface CardinalIterator
{
    public function key();
    public function next(): void;
    public function rewind(): void;
    public function valid(): bool;
    public function addIfAbsent($key): void;
    public function remove($key): void;
}
