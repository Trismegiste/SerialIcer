<?php

/*
 * SerialIcer
 */

namespace tests\Trismegiste\SerialIcer;

/**
 * FacadeTest tests the complete process with real fixtures
 */
class FacadeTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;

    protected function setUp()
    {
        $this->sut = new \Trismegiste\SerialIcer\Facade();
    }

    public function testWithoutInternal()
    {
        $obj = new Company(new Employee('Li', 10));
        $export = $this->sut->export($obj);
        $newObj = $this->sut->create($export);
        $this->assertEquals($obj, $newObj);
    }

    public function testWithInternal()
    {
        $obj = new InternalCompil();
        $export = $this->sut->export($obj);
        $newObj = $this->sut->create($export);

        // date ok :
        $this->assertEquals($obj->oneDate->format(\DateTime::ISO8601), $newObj->oneDate->format(\DateTime::ISO8601));
        // ref to this in ArrayObject ok :
        $this->assertEquals($newObj, $newObj->oneArray[3]);
        // ref to this in SplObjectStorage ok :
        $this->assertEquals(123, $newObj->storage[$newObj]);
    }

}
