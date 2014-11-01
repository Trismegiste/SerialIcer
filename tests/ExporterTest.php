<?php

/*
 * SerialIcer
 */

namespace tests\Trismegiste\SerialIcer;

use Trismegiste\SerialIcer\Exporter;

/**
 * ExporterTest tests Exporter
 */
class ExporterTest extends \PHPUnit_Framework_TestCase
{

    /** @var Exporter */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new Exporter();
    }

    public function getSimpleType()
    {
        return [
            [2001],
            [[1, 4, 9]],
            ['clavius']
        ];
    }

    public function getSeriousType()
    {
        return [
            [new \stdClass()],
            [new Entity('hal9000')],
            [new \DateTime()],
            [new \ArrayObject()]
        ];
    }

    /**
     * @dataProvider getSimpleType
     */
    public function testSimpleVar($var)
    {
        $this->assertEquals($var, $this->sut->export($var));
    }

    /**
     * @dataProvider getSeriousType
     */
    public function testValidExportedClass($obj)
    {
        $this->assertEquals(get_class($obj), $this->sut->export($obj)[Exporter::CLASS_KEY]);
    }

}
