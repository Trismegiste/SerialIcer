<?php

/*
 * SerialEaser
 */

namespace tests\Trismegiste\SerialIcer;

use Trismegiste\SerialIcer\Exporter;
use Trismegiste\SerialIcer\Factory;

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

    public function testWithoutInternal()
    {
        $obj = new Company(new Employee('Li', 10));
        $export = $this->exporter->export($obj);
        $newObj = $this->factory->create($export);
        $this->assertEquals($obj, $newObj);
    }

    public function testWithInternal()
    {
        $obj = new InternalCompil();
        $export = $this->exporter->export($obj);
        $newObj = $this->factory->create($export);

        // date ok :
        $this->assertEquals($obj->oneDate->format(\DateTime::ISO8601), $newObj->oneDate->format(\DateTime::ISO8601));
        // ref to this in ArrayObject ok :
        $this->assertEquals($newObj, $newObj->oneArray[3]);
        // ref to this in SplObjectStorage ok :
        $this->assertEquals(123, $newObj->storage[$newObj]);
    }

}
