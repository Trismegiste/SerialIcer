<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer;

/**
 * Exporter is a recursive exporter of object to array
 */
class Exporter extends Visitor
{

    public function __construct()
    {
        $that = $this;

        $this->addStrategy('DateTime', function ($scope, array& $export, array& $ref) {
            $export['tz'] = $this->getTimezone()->getName();
            $export['date'] = $this->format(\DateTime::ISO8601);
        });

        $this->addStrategy('ArrayObject', function ($scope, array& $export, array& $ref) use ($that) {
            foreach ($this as $key => $value) {
                $export['content'][$key] = $that->export($value, $ref);
            }
        });

        $this->addStrategy('SplObjectStorage', function ($scope, array& $export, array& $ref) use ($that) {
            foreach ($this as $key => $value) {
                $export['content'][$key] = [
                    'key' => $that->export($value, $ref),
                    'value' => $that->export($this->getInfo(), $ref)
                ];
            }
        });
    }

    /**
     * Get the closure to export an object with a class scope
     *
     * @return \Closure
     */
    protected function getDefaultStrategy()
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

    /**
     * Do not call this method, call export() instead
     *
     * @param object $obj
     * @param array $ref
     * @return array
     */
    public function exportObj($obj, array& $ref)
    {
        $addr = spl_object_hash($obj);
        if (array_key_exists($addr, $ref)) {
            return [self::REF_KEY => $addr];
        }
        $scope = get_class($obj);
        $export = [self::CLASS_KEY => $scope, self::UUID_KEY => $addr];
        $ref[$addr] = true;

        $closure = $this->getStrategy($scope);

        do {
            $dump = \Closure::bind($closure, $obj, $scope);
            $dump($scope, $export, $ref);
        } while ($scope = get_parent_class($scope));

        return $export;
    }

    /**
     * Export anything (scalar, object...) without any object types,
     * only scalars and arrays :
     * null => null
     * '123' => '123'
     * [1,4,9] => [1,4,9]
     * new stdClass() => ['@uuid' => '...', '@class' => 'stdClass']
     *
     * Supports also SplObjectStorage, DateTime, ArrayObject
     *
     * @param mixed $mixed
     * @param array $ref
     *
     * @return mixed
     */
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

}
