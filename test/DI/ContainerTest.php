<?php namespace Wright\Test\DI;

use Wright\DI\Container;
use Wright\DI\Definition;
use Wright\DI\Resolvable;
use Wright\DI\ResolveException;

/**
 * @coversDefaultClass Wright\DI\Container
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::definition
     */
    public function testDefinitionWithoutConcrete()
    {
        $container = new Container;

        $definition = $container->definition();

        $this->assertInstanceOf(Definition::class, $definition);

        $this->assertAttributeEmpty('concrete', $definition);
    }

    /**
     * @covers ::definition
     */
    public function testDefinitionWithConcrete()
    {
        $container = new Container;

        $definition = $container->definition(Stub\Foo::class);

        $this->assertInstanceOf(Definition::class, $definition);

        $this->assertAttributeEquals(Stub\Foo::class, 'concrete', $definition);
    }

    /**
     * @covers ::resolvable
     */
    public function testResolvable()
    {
        $container = new Container;

        $definition = $container->resolvable(function () {});

        $this->assertInstanceOf(Resolvable::class, $definition);
    }

    /**
     * @covers ::get
     */
    public function testGetBindFirst()
    {
        $container = new Container;

        $definition = $container->bind(Stub\FooInterface::class, Stub\Foo::class);

        $definition2 = $container->get(Stub\FooInterface::class);

        $this->assertSame($definition, $definition2);
    }

    /**
     * @covers ::get
     */
    public function testGetBindLast()
    {
        $container = new Container;

        $definition = $container->get(Stub\FooInterface::class);

        $definition2 = $container->bind(Stub\FooInterface::class, Stub\Foo::class);

        $this->assertSame($definition, $definition2);
    }

    /**
     * @covers ::alias
     */
    public function testAlias()
    {
        $container = new Container;

        $definition = $container->bind(Stub\FooInterface::class, Stub\Foo::class);

        $container->alias(Stub\FooInterface::class, 'foo');

        $this->assertAttributeEquals([
            Stub\FooInterface::class => $definition,
            'foo' => $definition
        ], 'definitions', $container);
    }

    /**
     * @covers ::after
     */
    public function testAfter()
    {
        $container = new Container;

        $definition = $container->bind(Stub\FooInterface::class, Stub\Foo::class);

        $closure = function () {};

        $container->after(Stub\FooInterface::class, $closure);

        $this->assertAttributeEquals([$closure], 'afters', $definition);
    }

    /**
     * @covers ::resolve
     */
    public function testResolve()
    {
        $container = new Container;

        $definition = $container->bind(Stub\Foo::class);

        $instance = $container->resolve(Stub\Foo::class);

        $this->assertInstanceOf(Stub\Foo::class, $instance);
    }

    /**
     * @covers ::resolve
     * @expectedException \Wright\DI\ResolveException
     */
    public function testResolveException()
    {
        $container = new Container;

        $instance = $container->resolve(Stub\Foo::class);
    }

    /**
     * @covers ::bind
     */
    public function testBindAfterGet()
    {
        $container = new Container;

        $definition = $container->get(Stub\Foo::class);

        $definition = $container->bind(Stub\Foo::class);

        $this->assertInstanceOf(Definition::class, $definition);

        $this->assertAttributeEquals(Stub\Foo::class, 'concrete', $definition);
    }

    /**
     * @covers ::bind
     */
    public function testBind()
    {
        $container = new Container;

        $definition = $container->bind(Stub\Foo::class);

        $this->assertInstanceOf(Definition::class, $definition);

        $this->assertAttributeEquals(Stub\Foo::class, 'concrete', $definition);
    }
}
