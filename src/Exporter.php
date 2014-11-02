<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer;

/**
 * Exporter is a recursive exporter of object to array
 */
class Exporter implements Serialization
{

    protected $specialExporter = [];

    public function __construct()
    {
        $this->addStrategy(new Exporter\DateTime());
        $this->addStrategy(new Exporter\ArrayObject());
    }

    public function addStrategy(Exporter\ClassExporter $exp)
    {
        $this->specialExporter[$exp->getFqcn()] = $exp;
    }

    /**
     * Get the closure to export an object with a class scope
     *
     * @return \Closure
     */
    private function getExportClosure()
    {
        $that = $this; // smells like javascript...

        return function ($scope, array& $export, array& $ref) use ($that) {
            $refl = new \ReflectionClass($scope);

            foreach ($refl->getProperties() as $prop) {
                if ($prop->class === $scope) {
                    $key = $prop->name;
                    $export[$scope . '::' . $key] = $that->export($this->$key, $ref);  // '$this' is not what you think :)
                }
            }
        };
    }

    public function exportObj($obj, array& $ref)
    {
        $addr = spl_object_hash($obj);
        if (array_key_exists($addr, $ref)) {
            return [self::REF_KEY => $addr];
        }
        $scope = get_class($obj);
        $export = [self::CLASS_KEY => $scope, self::UUID_KEY => $addr];
        $ref[$addr] = true;

        if ($this->isSpecialClass($scope)) {
            $prop = $this->specialExporter[$scope]->extract($obj);
            $export = array_merge($export, $this->export($prop, $ref));
        } else {
            $flatten = $this->getExportClosure();
            do {
                $dump = \Closure::bind($flatten, $obj, $scope);
                $dump($scope, $export, $ref);
            } while ($scope = get_parent_class($scope));
        }

        return $export;
    }

    public function export($mixed, array& $ref = [])
    {
        if (is_scalar($mixed)) {
            return $mixed;
        } elseif (is_array($mixed)) {
            $arr = [];
            foreach ($mixed as $key => $value) {
                $arr[$key] = $this->export($value, $ref);
            }

            return $arr;
        } elseif (is_object($mixed)) {
            return $this->exportObj($mixed, $ref);
        }

        return null;
    }

    public function isSpecialClass($fqcn)
    {
        return array_key_exists($fqcn, $this->specialExporter);
    }

}
