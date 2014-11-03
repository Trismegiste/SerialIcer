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
            [new \ArrayObject()],
            [new \SplObjectStorage()]
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
        $exported = $this->sut->export($obj);
        $this->assertEquals(get_class($obj), $exported[Exporter::CLASS_KEY]);
        $this->assertArrayHasKey(Exporter::UUID_KEY, $exported);
    }

    public function getSimpleObjProp()
    {
        $obj0 = new Entity('hal9000');
        $obj1 = new Person('Dave', 27);

        return [
            [$obj0, get_class($obj0) . '::name', 'hal9000'],
            [$obj1, get_class($obj1) . '::age', 27],
            [$obj1, get_class($obj0) . '::name', 'Dave'],
        ];
    }

    /**
     * @dataProvider getSimpleObjProp
     */
    public function testSimpleProp($obj, $prop, $value)
    {
        $exported = $this->sut->export($obj);
        $this->assertEquals($value, $exported[$prop], print_r($exported, true));
    }

    public function testTreeStructure()
    {
        $obj1 = new Person('Dave', 27);
        $exported = $this->sut->export($obj1);
        $this->assertEquals(9, $exported[get_class($obj1) . '::vector'][2]);
        $this->assertArrayHasKey(Exporter::UUID_KEY, $exported[get_class($obj1) . '::vector'][3]);
        $this->assertEquals('stdClass', $exported[get_class($obj1) . '::vector'][3][Exporter::CLASS_KEY]);
    }

    public function testBadType()
    {
        $ptr = fopen(__FILE__, 'r');
        $this->assertNull($this->sut->export($ptr));
    }

    public function testReference()
    {
        $obj = new Company(new Employee('Li', 10));
        $export = $this->sut->export($obj);
        $reference = $export[get_class($obj) . '::boss'][__NAMESPACE__ . '\Employee::company'];
        $this->assertArrayHasKey(Exporter::REF_KEY, $reference);
        $this->assertEquals($export[Exporter::UUID_KEY], $reference[Exporter::REF_KEY]);
    }

    public function testAutoReference()
    {
        $obj = new Ouroboros();
        $obj->ref = $obj; // how to crash most of serializers
        $export = $this->sut->export($obj);
        $this->assertEquals($export[Exporter::UUID_KEY], $export['tests\Trismegiste\SerialIcer\Ouroboros::ref'][Exporter::REF_KEY]);
    }

    public function testInjectedPropAreNotExportedBecauseOOPIsNotFreePorn()
    {
        $obj = new \stdClass();
        $obj->prop = 'arf';
        $this->assertArrayNotHasKey('stdClass::prop', $this->sut->export($obj));
    }

    public function testEmbeddedSpecialClass()
    {
        $obj = new InternalCompil();
        $exp = $this->sut->export($obj);

        $date = $exp[get_class($obj) . '::oneDate'];
        $this->assertEquals('DateTime', $date[Exporter::CLASS_KEY]);
        $this->assertArrayHasKey('tz', $date);
        $this->assertArrayHasKey('date', $date);

        $splArray = $exp[get_class($obj) . '::oneArray'];
        $this->assertEquals('ArrayObject', $splArray[Exporter::CLASS_KEY]);
        $this->assertArrayHasKey('content', $splArray);
        $this->assertCount(4, $splArray['content']);
        $this->assertEquals($exp[Exporter::UUID_KEY], $splArray['content'][3][Exporter::REF_KEY]);

        $splStorage = $exp[get_class($obj) . '::storage'];
        $this->assertEquals('SplObjectStorage', $splStorage[Exporter::CLASS_KEY]);
        $this->assertArrayHasKey('content', $splStorage);
        $this->assertCount(1, $splStorage['content']);
        $this->assertEquals($exp[Exporter::UUID_KEY], $splStorage['content'][0]['key'][Exporter::REF_KEY]);
        $this->assertEquals(123, $splStorage['content'][0]['value']);
    }

}
