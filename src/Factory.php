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

        $this->addStrategy('ArrayObject', function($scope, array $data, array& $ref) use ($that) {
            foreach ($data['content'] as $key => $value) {
                $this[$key] = $that->create($value, $ref);
            }
        });

        $this->addStrategy('SplObjectStorage', function($scope, array $data, array& $ref) use ($that) {
            foreach ($data['content'] as $key => $assoc) {
                $this->attach($that->create($assoc['key'], $ref), $that->create($assoc['value'], $ref));
            }
        });
    }

    /**
     * Creates an (array of) object tree with the exported array
     *
     * @param mixed $import the value to "unserialize" (in a way)
     * @param array $ref (an array of reference)
     *
     * @return mixed a scalar, an array or an object, or null
     *
     * @throws \RuntimeException if a reference does not exist
     */
    public function create($import, array& $ref = [])
    {
        if (is_scalar($import) || is_null($import)) {
            return $import;
        }

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
            $arr[$key] = $this->create($value, $ref);
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
                    $this->$prop = $that->create($value, $ref);
                }
            }
        };
    }

}
