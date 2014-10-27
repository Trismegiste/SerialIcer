<?php

require_once 'fixtures.php';

class SerialIcer
{

    const CLASS_KEY = '@class';
    const UUID_KEY = '@uuid';
    const REF_KEY = '@ref';

    protected $reference = [];

    private function getRecursionClosure()
    {
        $that = $this; // smells like javascript...

        return function ($scope, array& $export) use ($that) {
                    $refl = new \ReflectionClass($scope);

                    foreach ($refl->getProperties() as $prop) {
                        if ($prop->class === $scope) {
                            $key = $prop->name;
                            $value = $this->$key;
                            if (is_object($value)) {
                                $value = $that->export($value);
                            }
                            $export[$scope . '::' . $key] = $value;
                        }
                    }
                };
    }

    public function export($obj)
    {
        $addr = spl_object_hash($obj);
        if (array_key_exists($addr, $this->reference)) {
            return [self::REF_KEY => $addr];
        }
        $scope = get_class($obj);
        $export = [self::CLASS_KEY => $scope, self::UUID_KEY => $addr];
        $this->reference[$addr] = true;

        $flatten = $this->getRecursionClosure();
        do {
            $dump = \Closure::bind($flatten, $obj, $scope);
            $dump($scope, $export);
        } while ($scope = get_parent_class($scope));

        return $export;
    }

}

$service = new SerialIcer();

$obj = new Company(new Employee('toto', 13));

print_r($service->export($obj));