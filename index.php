<?php

class Entity
{

    private $name;
    protected $inherited = 7;

    public function __construct($str)
    {
        $this->name = $str;
    }

}

class Person extends Entity
{

    protected $age;

    public function __construct($str, $age)
    {
        parent::__construct($str);
        $this->age = $age;
    }

}

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
    $export = ['@class' => $scope];
    do {
        $dump = \Closure::bind($flatten, $obj, $scope);
        $dump($scope, $export);
    } while ($scope = get_parent_class($scope));

    return $export;
}

$obj = new Person('toto', 13);

print_r(flattenObj($obj));