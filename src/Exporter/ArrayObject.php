<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer\Exporter;

/**
 * ArrayObject is a ...
 */
class ArrayObject implements ClassExporter
{

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
