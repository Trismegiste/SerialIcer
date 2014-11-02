<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer;

/**
 * SerialIcer is a contract for serializing / unserializing "things" into arrays
 * and vice-versa
 */
interface SerialIcer
{

    /**
     * Transforms all objects into arrays
     *
     * @param null|scalar|array|object $mixed
     *
     * @return null|scalar|array
     */
    public function export($mixed);

    /**
     * Re-creates all exported objects (which are transformed into arrays) into new objects
     *
     * @param null|scalar|array $mixed
     *
     * @return null|scalar|array|object
     */
    public function create($mixed);
}
