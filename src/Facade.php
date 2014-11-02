<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer;

/**
 * Facade is a facade for this serializer/unserializer library
 */
class Facade implements SerialIcer
{

    protected $exporter;
    protected $importer;

    public function __construct()
    {
        $this->exporter = new Exporter();
        $this->importer = new Factory();
    }

    /**
     * @inheritdoc
     */
    public function export($mixed)
    {
        return $this->exporter->export($mixed);
    }

    /**
     * @inheritdoc
     */
    public function create($mixed)
    {
        return $this->importer->create($mixed);
    }

}
