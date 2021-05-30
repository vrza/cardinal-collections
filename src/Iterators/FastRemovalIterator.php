<?php

namespace CardinalCollections\Iterators;

/*
 *  Iterator implementation that allows for element removal
 *  in O(1) time.
 *
 *  Removed elements are assigned a special value REMOVED,
 *  used by rewind() and next() skip over removed elements.
 *
 *  The tradeoff is that space usage and rewind()/next() time
 *  may suffer after many element removals.
 */
class FastRemovalIterator implements CardinalIterator
{
    // perform compaction when
    // total allocated is at least:
    const COMPACTION_MIN = 4096;
    // actually used / total allocated ratio is below:
    const COMPACTION_MAX_UTIL = 0.7;

    // "magic" value for removed (unset) entries
    const REMOVED = -1;

    // table of entries
    private $keyToPosition = [];
    // number of actually used entries
    private $numUsed = 0;

    public function __construct($hashmap)
    {
        foreach ($hashmap as $key => $_value) {
            $this->addNew($key);
        }
    }

    /*
     * In PHP 7, once nNumUsed reaches nTableSize PHP will try to
     * compact the arData array, by dropping any UNDEF entries that
     * have been added along the way. Only if all buckets really
     * contain a value the arData will be reallocated to twice the
     * size.
     *
     * https://www.npopov.com/2014/12/22/PHPs-new-hashtable-implementation.html
     *
     * We can prevent PHP from doubling our size by performing
     * compaction ourselves -- moving data to a compacted array.
     */
    private function compact(): void
    {
        $currentKey = $this->valid() ? key($this->keyToPosition) : null;
        $compacted = [];
        foreach ($this->keyToPosition as $key => $value) {
            if ($value !== self::REMOVED) {
                $compacted[$key] = $value;
                // preserve the iterator position
                if (!is_null($currentKey)) {
                    next($compacted);
                    if ($currentKey === key($compacted)) {
                        $currentKey = null;
                    }
                }
            }
        }
        $this->keyToPosition = $compacted;
    }

    /*
     * Starting at COMPACTION_MINIMUM elements,
     * compact whenever $n approaches a power of two
     * and compaction would result in size under
     * COMPACTION_THRESHOLD ratio of original size
     */
    private function shouldCompact(): bool
    {
        $n = count($this->keyToPosition);
        return ($n + 2 > self::COMPACTION_MIN)
            && (($n & ($n + 1)) === 0)
            && ($this->numUsed / $n < self::COMPACTION_MAX_UTIL);
    }

    private function forwardToValidPosition(): void
    {
        while (current($this->keyToPosition) === self::REMOVED) {
            next($this->keyToPosition);
        }
    }

    public function dump()
    {
        var_dump($this->position);
        var_dump($this->keyToPosition);
    }

    public function rewind(): void
    {
        reset($this->keyToPosition);
        $this->forwardToValidPosition();
    }

    public function key()
    {
        return $this->valid() ? key($this->keyToPosition) : null;
    }

    public function next(): void
    {
        next($this->keyToPosition);
        $this->forwardToValidPosition();
    }

    public function valid(): bool
    {
        return !is_null(key($this->keyToPosition))
            && current($this->keyToPosition) !== self::REMOVED;
    }

    public function addIfAbsent($key): void
    {
        if (!array_key_exists($key, $this->keyToPosition)) {
            $this->addNew($key);
        }
    }

    private function addNew($key): void
    {
        if ($this->shouldCompact()) {
            $this->compact();
        }
        $count = count($this->keyToPosition);
        $this->keyToPosition[$key] = $count;
        ++$this->numUsed;
    }

    public function remove($key): void
    {
        if (array_key_exists($key, $this->keyToPosition)) {
            $this->keyToPosition[$key] = self::REMOVED;
            --$this->numUsed;
        }
    }

}
