# dependency-injection
Descent Framework Dependency Injection Container

### The dependency builder

This component serves a general dependency builder implementation
implemented as `Descent\Services\DependencyBuilder`. The dependency
builder orchestrates dependencies by constructing them without
configuration. Optionally provided parameters and a list of enforced
parameters are priorized in the building process.

##### Building an object from a interface factory

```php
use Descent\Services\{
    DependencyBuilder,
    Entities\Factory
};

$builder = new DependencyBuilder();
$factory = new Factory(
    DateTimeInterface::class, 
    function(string $time, string $zone = 'europe/berlin') {
        return new DateTime($time, new DateTimeZone($zone);
    }
);

$object = $builder->build($factory, ['time' => 'now']);
```

##### Building an object from a interface

```php
use Descent\Services\{
    DependencyBuilder
};

class Foo {

}

class Bar {
    protected $foo;
    
    public function __constrct(Foo $foo)
    {
        $this->foo = $foo;
    }
}

$builder = new DependencyBuilder();

$object = $builder->make(Bar::class);
```

### The dependency injection container

This component serves a general dependency injection container
implementation implemented as `Descent\Services\DependencyInjectionContainer`.
The dependency injection container allows to register services as
interface concrete assignments (service) as well as interface 
callback assignments (factories). The dependency injection
container extends the dependency builder and alters the
interface resolving of the dependency builder to seek for 
registered interfaces first.

##### Building objects from registered services

```php
use Descent\Services\{
    DependencyInjectionContainer
};

class Foo {

}

class Bar {
    protected $foo;
    
    public function __constrct(Foo $foo)
    {
        $this->foo = $foo;
    }
}

$container = new DependencyInjectionContainer();

$container->bind(Foo::class)->singleton();
$container->bind(Bar::class);

$object = $container->make(Bar::class);
```

##### Building objects from registered factories

```php
use Descent\Services\{
    DependencyInjectionContainer
};

class Foo {

}

class Bar {
    protected $foo;
    protected $bar;
    
    public function __constrct(Foo $foo, Foo $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}

$container = new DependencyInjectionContainer();

$container->bind(Foo::class)->singleton();
$container->factory(
    Bar::class, 
    function(Foo $foo, Foo $bar = null) {
        return new Bar($foo, $bar ?? $foo);
    }
)->enforceParameters('bar');

$object = $container->make(Bar::class);
```