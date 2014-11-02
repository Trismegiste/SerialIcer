<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer\Exporter;

/**
 * DateTime is an exporter for DateTime
 */
class DateTime implements ClassExporter
{

    public function extract($obj)
    {
        return [
            'tz' => $obj->getTimezone()->getName(),
            'date' => $obj->format(\DateTime::ISO8601)
        ];
    }

    public function getFqcn()
    {
        return 'DateTime';
    }

}
