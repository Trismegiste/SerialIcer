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

    public function getFqcn()
    {
        return 'ArrayObject';
    }

    public function extract($object)
    {
        $exported = [];
        /** @var $object ArrayObject */
        foreach ($object as $key => $value) {
            $exported['content'][$key] = $value;
        }

        return $exported;
    }

}
