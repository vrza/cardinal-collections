<?php

namespace CardinalCollections;

class Utilities
{
    public static function stringRepresentation($value): string
    {
        return json_encode($value);
    }
}
