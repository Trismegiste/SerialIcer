<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer\Transformer;

/**
 * DateTime is an exporter for DateTime
 */
class DateTime implements ClassExporter, ClassFactory
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

    public function create(array $exported)
    {
        $newdate = \DateTime::createFromFormat(\DateTime::ISO8601, $exported['date']);
        $newdate->setTimezone(new \DateTimeZone($exported['tz']));

        return $newdate;
    }

}
