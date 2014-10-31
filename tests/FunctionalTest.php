<?php

/*
 * SerialEaser
 */

namespace tests\Trismegiste\SerialIcer;

use Trismegiste\SerialIcer\Exporter;
use Trismegiste\SerialIcer\Factory;

require_once __DIR__ . '/fixtures.php';

/**
 * Functional tests the complete process with real fixtures
 */
class FunctionalTest extends \PHPUnit_Framework_TestCase
{

    protected $exporter;
    protected $factory;

    protected function setUp()
    {
        $this->exporter = new Exporter();
        $this->factory = new Factory();
    }

    public function testComplete()
    {
        $obj = new Company(new Employee('toto', 13));
        $export = $this->exporter->export($obj);
        $newObj = $this->factory->create($export);
        $this->assertEquals($obj, $newObj);
    }

}