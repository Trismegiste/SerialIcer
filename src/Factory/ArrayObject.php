<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer\Exporter;

/**
 * ArrayObject is a ...
 */
class ArrayObject implements ClassExporter, ClassFactory
{

    protected $globalExporter;

    public function __construct(\Trismegiste\SerialIcer\Exporter $exporter)
    {
        $this->globalExporter = $exporter;
    }

    public function create(array $exported)
    {

    }

    public function export($object, array &$exported)
    {
        /** @var $object ArrayObject */
        foreach ($object as $key => $value) {
            $exported['content'][$key] = $this->globalExporter->export($mixed);
        }
    }

    public function getFqcn()
    {
        return 'ArrayObject';
    }

}
