<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer\Transformer;

/**
 * ClassExporter is a contract for exporting one class type
 */
interface ClassExporter
{

    public function export($object, array& $exported);

    public function getFqcn();
}
