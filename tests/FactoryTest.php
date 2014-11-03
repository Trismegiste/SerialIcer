<?php

/*
 * SerialIcer
 */

namespace tests\Trismegiste\SerialIcer;

use Trismegiste\SerialIcer\Factory;

/**
 * FactoryTest tests Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{

    /** @var Factory */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new Factory();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage 149
     */
    public function testMissingReference()
    {
        $fail = ['@class' => 'stdClass',
            '@uuid' => '0000000071f0b30a00000000207a2a14',
            'stdClass::loop' => ['@ref' => 149]
        ];

        $this->sut->create($fail);
    }

    public function getSimpleType()
    {
        return [
            [2001],
            [[1, 4, 9]],
            ['clavius']
        ];
    }

    /**
     * @dataProvider getSimpleType
     */
    public function testSimpleVar($var)
    {
        $this->assertEquals($var, $this->sut->create($var));
    }

    public function getSeriousType()
    {
        return [
            ['stdClass'],
            [__NAMESPACE__ . '\Entity'],
            [__NAMESPACE__ . '\Person']
        ];
    }

    /**
     * @dataProvider getSeriousType
     */
    public function testValidClassDuringCreation($fqcn)
    {
        $exported = [
            Factory::CLASS_KEY => $fqcn,
            Factory::UUID_KEY => 123
        ];

        $newObj = $this->sut->create($exported);
        $this->assertInstanceOf($fqcn, $newObj);
    }

    public function testDateTime()
    {
        $date = [
            '@class' => 'DateTime',
            '@uuid' => '0000000071f0b30a00000000207a2a14',
            'tz' => 'Europe/Berlin',
            'date' => '2014-11-02T18:21:53+0100'
        ];

        $obj = $this->sut->create($date);
        $this->assertEquals('Europe/Berlin', $obj->getTimezone()->getName());
        $this->assertEquals('2014-11-02T18:21:53+0100', $obj->format(\DateTime::ISO8601));
    }

    public function testArrayObject()
    {
        $arr = [
            '@class' => 'ArrayObject',
            '@uuid' => '0000000071f0b30a00000000207a2a14',
            'content' => [1, 4, 9]
        ];

        $obj = $this->sut->create($arr);
        $this->assertCount(3, $obj);
    }

    public function testSplObjectStorage()
    {
        $arr = [
            '@class' => 'SplObjectStorage',
            '@uuid' => '0000000071f0b30a00000000207a2a14',
            'content' => []
        ];

        $obj = $this->sut->create($arr);
        $this->assertEquals(0, $obj->count());
    }

}
