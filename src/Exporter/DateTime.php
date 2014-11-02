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

    public function export($obj, array& $exported)
    {
        $exported['tz'] = $obj->getTimezone()->getName();
        $exported['date'] = $obj->format(\DateTime::ISO8601);
    }

    public function getFqcn()
    {
        return 'DateTime';
    }

}
