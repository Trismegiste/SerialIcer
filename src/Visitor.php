<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer;

/**
 * Visitor is a visitor with closure strategies
 */
abstract class Visitor implements Serialization
{

    private $specialExporter = [];

    public function isSpecialClass($fqcn)
    {
        return array_key_exists($fqcn, $this->specialExporter);
    }

    public function addStrategy($fqcn, \Closure $exp)
    {
        $this->specialExporter[$fqcn] = $exp;
    }

    protected function getStrategy($fqcn)
    {
        return $this->specialExporter[$fqcn];
    }

    abstract protected function getDefaultStrategy();
}
