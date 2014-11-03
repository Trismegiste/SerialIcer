<?php

require_once __DIR__ . '/../vendor/autoload.php';

/** here is a class which made every serializer mad : */
class Ouroboros
{

    public $tail;

}

$head = new Ouroboros();
$head->tail = $head;

// here is my converter :
$convert = new \Trismegiste\SerialIcer\Facade();

$export = $convert->export($head);

print_r($export);


$newObj = $convert->create($export);
if ($newObj->tail === $newObj) {
    echo "I think we have an infinite loop, sir\n";
}
print_r($newObj);

/** example of private properties coming from parent class */
abstract class Zecret
{

    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

}

class Person extends Zecret
{
}

$person = new Person('Sheldon');
$export = $convert->export($person);
print_r($export);
$newPerson = $convert->create($export);
echo $newPerson->getName() . "\n";
