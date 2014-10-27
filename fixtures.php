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

trait Additional
{

    protected $status = 'running';

}

class Employee extends Person
{

    use Additional;

    protected $name;
    protected $company;

    public function __construct($str, $age)
    {
        parent::__construct($str, $age);
        $this->name = 'acme';
    }

    public function setCompany(Company $c)
    {
        $this->company = $c;
    }

}

class Company
{

    protected $boss;

    public function __construct(Employee $boss)
    {
        $this->boss = $boss;
     //   $this->boss->setCompany($this);
    }

}
