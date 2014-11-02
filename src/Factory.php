<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer;

/**
 * Factory is the bijection of Exporter
 */
class Factory extends Visitor
{

    public function __construct()
    {
        $that = $this;

        $this->addStrategy('DateTime', function($scope, array $data, array& $ref) {
            $this->modify($data['date']);
            $this->setTimezone(new \DateTimeZone($data['tz']));
        });
    }

    /**
     * Creates a object tree with the exported array
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
            // creation of the object :
            $scope = $import[self::CLASS_KEY];
            $obj = $this->instantiate($scope);
            $ref[$import[self::UUID_KEY]] = $obj;
            unset($import[self::CLASS_KEY]);
            unset($import[self::UUID_KEY]);

            // choose the right closure to inject properties
            if ($this->isSpecialClass($scope)) {
                $hydrate = $this->getStrategy($scope);
            } else {
                $hydrate = $this->getImportClosure();
            }

            // iterate over inheritance tree
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
        if ($this->isSpecialClass($cls)) {
            return $refl->newInstance();
        } else {
            return $refl->newInstanceWithoutConstructor();
        }
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
