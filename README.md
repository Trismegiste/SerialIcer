# SerialIcer

A recursive un/serializer object &lt;-> array

## What
It is a serializer / unserializer for object. There are many, except this one :

* does not require any mapping information or convoluted annotation
* does not require any inheritance
* does not require any getter/setter
* does not require any special constructor signature
* does not require any method to implement
* in fact it requires nothing, did you get it ? :smile:
* it deals with private properties inherited from parent class
* it deals with recursion (a class' property referencing to himself or a lower object in the tree)
* it deals with DateTime, SplObjectStorage and ArrayObject
* it deals with references across a hierarchy of complex tree structure of objects

It cannot and will not :

* deal with references to scalar (because references to scalar in class properties are a trap)
* deal with static, because static, as you know, is Evil
* deal with injected properties like $obj->myUndeclaredProperty because OOP is not free porn

Furthermore, it is less than 120 NCLOC

Currently it has some issues with :

* classes extending internal concrete PHP class

## Why

Primary goal : because we can !
Secondary goal : JSON communication with a javascript client

## How

There is one class, a facade, named Facade.

```php
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
```
prints
```
Array
(
    [@class] => Ouroboros
    [@uuid] => 000000006aca58a00000000031882fa2
    [Ouroboros::tail] => Array
        (
            [@ref] => 000000006aca58a00000000031882fa2
        )

)
```
Unlike the Universe' entropy, you can reverse the process :
```php
$newObj = $convert->create($export);
if ($newObj->tail === $newObj) {
    echo "I think we have an infinite loop, sir\n";
}
print_r($newObj);
```
prints
```
I think we have an infinite loop, sir
Ouroboros Object
(
    [tail] => Ouroboros Object
 *RECURSION*
)
```

Another example :
```php
/** example of private properties coming from parent class */
class Zecret
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
print_r($convert->export($person));
```
prints
```
Array
(
    [@class] => Person
    [@uuid] => 0000000030f7e15b000000002381f056
    [Zecret::name] => Sheldon
)
```

See the file named `fixtures.php` in the test directory.

Read the unit tests, 100% of code coverage by the way.

If you need to inject this service into another service of yours,
use the interface named `SerialIcer` for type-hinting information, as you know
Liskov, NEVAR type-hint your method parameters with the concrete class Facade
or you will fail at life and people will laugh at you when walking in the street.

## When

* rapidly freeze a state of a complex model (persistence) during a development,
of course, there is serialize() function, but not really readable for humans.
* import/export with a client in json format (in progress)
* comparison of objects (for tests and assertions)

### Finally

And Winter is coming, so be prepared to freeze your objects.