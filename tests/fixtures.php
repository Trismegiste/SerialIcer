<?php

namespace tests\Trismegiste\SerialIcer;

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
    protected $vector;

    public function __construct($str, $age)
    {
        parent::__construct($str);
        $this->age = $age;
        $this->vector = [1, 4, 9, new \stdClass()];
    }

}

trait Additional
{

    protected $status = 'running';

}

class Employee extends Person
{

    use Additional;

    protected $name; // name collision check
    protected $company;

    public function __construct($str, $age)
    {
        parent::__construct($str, $age);
        $this->name = 'developer';
    }

    public function setCompany(Company $c)
    {
        $this->company = $c;
    }

}

class Company
{

    protected $boss;
    protected $created;
    protected $spl;

    public function __construct(Employee $boss)
    {
        $this->boss = $boss;
        $this->boss->setCompany($this);
    }

}

class Ouroboros
{

    public $ref;

}

class InternalCompil
{

    protected $oneDate;
    protected $oneArray;
    protected $storage;

    public function __construct()
    {
        $this->oneDate = new \DateTime();
        $this->oneArray = new \ArrayObject([1, 4, 9, $this]);
        $this->storage = new \SplObjectStorage();
        $this->storage->attach($this, 123);
    }

}
