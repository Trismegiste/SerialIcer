<?php

require_once 'fixtures.php';

class SerialIcer
{

    const CLASS_KEY = '@class';
    const UUID_KEY = '@uuid';
    const REF_KEY = '@ref';

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
                            $export[$scope . '::' . $key] = $that->export($this->$key, $ref);
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

        $flatten = $this->getExportClosure();
        do {
            $dump = \Closure::bind($flatten, $obj, $scope);
            $dump($scope, $export, $ref);
        } while ($scope = get_parent_class($scope));

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

    /**
     * 
     * @param array $import
     * 
     * @return object
     * 
     * @throws \RuntimeException
     */
    public function create(array $import, array& $ref = [])
    {
        if (array_key_exists(self::REF_KEY, $import)) {
            if (array_key_exists($import[self::REF_KEY], $ref)) {
                return $ref[$import[self::REF_KEY]];
            } else {
                throw new \RuntimeException($import[self::REF_KEY] . ' ref is unknown');
            }
        }

        if (array_key_exists(self::CLASS_KEY, $import)) {
            $scope = $import[self::CLASS_KEY];
            $obj = $this->instantiate($scope);
            $ref[$import[self::UUID_KEY]] = $obj;
            unset($import[self::CLASS_KEY]);
            unset($import[self::UUID_KEY]);
            $hydrate = $this->getImportClosure();
            do {
                $dump = \Closure::bind($hydrate, $obj, $scope);
                $dump($scope, $import, $ref);
            } while ($scope = get_parent_class($scope));

            return $obj;
        }

        $arr = [];
        foreach ($import as $key => $value) {
            if (is_array($value)) {
                $value = $this->create($value, $ref);
            }
            $arr[$key] = $value;
        }

        return $arr;
    }

    protected function instantiate($cls)
    {
        $refl = new \ReflectionClass($cls);

        return $refl->newInstanceWithoutConstructor();
    }

    private function getImportClosure()
    {
        $that = $this;

        return function($scope, array $data, array& $ref) use ($that) {
                    foreach ($data as $key => $value) {
                        list($fqcn, $prop) = explode('::', $key);
                        if ($fqcn === $scope) {
                            if (is_array($value)) {
                                $value = $that->create($value, $ref);
                            }
                            $this->$prop = $value;
                        }
                    }
                };
    }

}

$service = new SerialIcer();

$obj = new Company(new Employee('toto', 13));

$export = $service->export($obj);
print_r($export);
$newObj = $service->create($export);
print_r($obj);
print_r($newObj);
