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

    public function testDateTime()
    {
        $date = ['@class' => 'DateTime',
            '@uuid' => '0000000071f0b30a00000000207a2a14',
            'tz' => 'Europe/Berlin',
            'date' => '2014-11-02T18:21:53+0100'
        ];

        $obj = $this->sut->create($date);
        $this->assertEquals('Europe/Berlin', $obj->getTimezone()->getName());
        $this->assertEquals('2014-11-02T18:21:53+0100', $obj->format(\DateTime::ISO8601));
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

}
