# Wright Dependency Injection

Wright uses its own container to manage dependency injection. Here is how it's used. First a new Container is created.

```php
use Wright\DI\Container;

$container = new Container;
```

From there the Container provides 7 methods used to handle dependency injection.

## Methods

### bind($abstract, $concrete = null, array $args = []) // @return \Wright\DI\Definition

Bind is used to bind an abstract, which will usually be an interface but could be any string, to a concrete class, and the arguments to use when instantiating that class. If you use an interface the Container will be able to resolve that interface to the concrete class anywhere it is typehinted. Other strings will only be useful if you use the get or after methods (see below).

```php
$container->bind(FooInterface::class, SomethingFoo::class);
```

Note that $args needs to be an associative array where the keys are the names of the parameters in the class's constructor. Although this does require you to learn the names of parameters in sometimes third party code, the advantage is that you don't have to specify every argument. Those that have typehints that have been bound into the Container can be skipped.

Also a note about $args, they are resolved out of the Container if they are definitions, meaning that each item in the $args array is looked at and if is an object of a class that implements \Wright\DI\DefinitionInterface then it will be resolved out of the Container. This is why the methods get, definition, and resolvable are useful. They're covered below. Multi-dimensional arrays are handled as well.


### get($abstract) // @return \Wright\DI\Definition

Get is used to grab a definition and pass it in as an argument to another defintion. Definitions can be used before they are bound. The order that you define stuff does not matter. The only thing that matters is that you define everything before you ultimately call resolve (another method covered below).

```php
$container->bind(FooInterface::class, SomethingFoo::class);

$container->bind(BarInterface::class, SomethingBar::class, [
    'fooParam' => $container->get(FooInterface::class)
]);
```

### definition($concrete = null, array $args = []) // @return \Wright\DI\Definition

Definition is a convenience method. It creates a new Definition and returns it, but that definition is not stored in the Container. As with bind, any arguments are resolved recursively to any depth.

```php
$container->bind(BarInterface::class, SomethingBar::class, [
    'fooParam' => $container->definition(SomethingFoo::class)
]);
```

### resolvable(\Closure $concrete) // @return \Wright\DI\Resolvable

Resolvable is another convenience method. You pass it a closure that should return a dependency. It returns an instance of \Wright\DI\Resolvable that implements \Wright\DI\DefinitionInterface. The closure that you give it will not be called until resolve is called.

```php
$container->bind(BarInterface::class, SomethingBar::class, [
    'fooParam' => $container->resolvable(function(){

        return new SomethingFoo;
    }]
]);
```

### alias($abstract, $alias) // @return \Wright\DI\Definition

Alias allows you to give an interface a shorter name. This is useful for creating APIs. For instance \Wright\Data\StandardData is bound to \Wright\Data\DataInterface, but then DataInterface is aliased to data so that outside the core code users can call after (another method below) using that more readable name.

```php
$container->alias(BarInterface::class, 'bar');
```

### after($abstract, \Closure $after) // @return \Wright\DI\Definition

After is used for calling setters.

```php
$container->after('bar', function($bar) {

    $bar->setFoo(new SomethingFoo);
});
```

### resolve($abstract) // @return mixed

When all dependencies have been defined resolve is called. Resolve should really only be called once, but it may also need to be called in closures passed into resolvable and after. All dependencies should absolutely be defined before resolve is called.
