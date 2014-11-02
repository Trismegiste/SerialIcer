<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer\Exporter;

/**
 * ClassExporter is a contract for exporting one class type
 */
interface ClassExporter
{

    public function extract($object);

    public function getFqcn();
}
