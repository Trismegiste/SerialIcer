<?php

require_once 'fixtures.php';

function flattenObj($obj)
{
    $flatten = function ($scope, array& $export) {
                $refl = new \ReflectionClass($scope);

                foreach ($refl->getProperties() as $prop) {
                    if ($prop->class === $scope) {
                        $key = $prop->name;
                        $value = $this->$key;
                        if (is_object($value)) {
                            $value = flattenObj($value);
                        }
                        $export[$scope . '::' . $key] = $value;
                    }
                }
            };

    $scope = get_class($obj);
    $export = ['@class' => $scope, '@uuid' => spl_object_hash($obj)];
    do {
        $dump = \Closure::bind($flatten, $obj, $scope);
        $dump($scope, $export);
    } while ($scope = get_parent_class($scope));

    return $export;
}

$obj = new Company(new Employee('toto', 13));

print_r(flattenObj($obj));