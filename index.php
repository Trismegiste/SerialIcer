<?php

require_once 'fixtures.php';

class SerialIcer
{

    const CLASS_KEY = '@class';
    const UUID_KEY = '@uuid';
    const REF_KEY = '@ref';

    protected $reference = [];

    /**
     * Get the closure to export an object with a class scope
     * 
     * @return \Closure
     */
    private function getExportClosure()
    {
        $that = $this; // smells like javascript...

        return function ($scope, array& $export) use ($that) {
                    $refl = new \ReflectionClass($scope);

                    foreach ($refl->getProperties() as $prop) {
                        if ($prop->class === $scope) {
                            $key = $prop->name;
                            $export[$scope . '::' . $key] = $that->export($this->$key);
                        }
                    }
                };
    }

    public function exportObj($obj)
    {
        $addr = spl_object_hash($obj);
        if (array_key_exists($addr, $this->reference)) {
            return [self::REF_KEY => $addr];
        }
        $scope = get_class($obj);
        $export = [self::CLASS_KEY => $scope, self::UUID_KEY => $addr];
        $this->reference[$addr] = true;

        $flatten = $this->getExportClosure();
        do {
            $dump = \Closure::bind($flatten, $obj, $scope);
            $dump($scope, $export);
        } while ($scope = get_parent_class($scope));

        return $export;
    }

    public function export($mixed)
    {
        if (is_scalar($mixed)) {
            return $mixed;
        } elseif (is_array($mixed)) {
            $arr = [];
            foreach ($mixed as $key => $value) {
                $arr[$key] = $this->export($value);
            }

            return $arr;
        } elseif (is_object($mixed)) {
            return $this->exportObj($mixed);
        }

        //throw new \RuntimeException("wtf");
        return null;
    }

    /**
     * 
     * @param array $import
     * 
     * @return object
     * 
     * @throws \RuntimeException
     */
    public function create(array $import)
    {
        if (array_key_exists(self::REF_KEY, $import)) {
            if (array_key_exists($import[self::REF_KEY], $this->reference)) {
                return $this->reference[$import[self::REF_KEY]];
            } else {
                throw new \RuntimeException($import[self::REF_KEY] . ' ref is unknown');
            }
        }

        if (array_key_exists(self::CLASS_KEY, $import)) {
            $scope = $import[self::CLASS_KEY];
            $obj = $this->instantiate($scope);
            $this->reference[$import[self::UUID_KEY]] = $obj;
            unset($import[self::CLASS_KEY]);
            unset($import[self::UUID_KEY]);
            $hydrate = $this->getImportClosure();
            do {
                $dump = \Closure::bind($hydrate, $obj, $scope);
                $dump($scope, $import);
            } while ($scope = get_parent_class($scope));

            return $obj;
        } else {
            $arr = [];
            foreach ($import as $key => $value) {
                if (is_array($value)) {
                    $value = $this->create($value);
                }
                $arr[$key] = $value;
            }

            return $arr;
        }

        throw new \RuntimeException('fail');
    }

    protected function instantiate($cls)
    {
        $refl = new \ReflectionClass($cls);

        return $refl->newInstanceWithoutConstructor();
    }

    private function getImportClosure()
    {
        $that = $this;

        return function($scope, array $data) use ($that) {
                    foreach ($data as $key => $value) {
                        list($fqcn, $prop) = explode('::', $key);
                        if ($fqcn === $scope) {
                            if (is_array($value)) {
                                $value = $that->create($value);
                            }
                            $this->$prop = $value;
                        }
                    }
                };
    }

    public function reset()
    {
        $this->reference = [];
    }

}

$service = new SerialIcer();

$obj = new Company(new Employee('toto', 13));

$export = $service->export($obj);
print_r($export);
$service->reset();
$newObj = $service->create($export);
print_r($obj);
print_r($newObj);
