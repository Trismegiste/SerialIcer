<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer;

/**
 * Facade is a facade for this serializer/unserializer library
 */
class Facade
{

    protected $exporter;
    protected $importer;

    public function __construct()
    {
        $this->exporter = new Exporter();
        $this->importer = new Factory();
    }

    /**
     * Transforms all objects into arrays
     *
     * @param null|scalar|array|object $mixed
     *
     * @return null|scalar|array
     */
    public function export($mixed)
    {
        return $this->exporter->export($mixed);
    }

    /**
     * Transforms all exported objects (which are tranformed into arrays) into new objects
     *
     * @param null|scalar|array $mixed
     *
     * @return null|scalar|array|object
     */
    public function create($mixed)
    {
        return $this->importer->create($mixed);
    }

}
