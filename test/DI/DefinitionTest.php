<?php namespace Wright\Test\DI;

use Wright\DI\Container;
use Wright\DI\Definition;

/**
 * @coversDefaultClass Wright\DI\Definition
 */
class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructWithClass()
    {
        $container = new Container;

        $definition = new Definition($container, Stub\Foo::class);

        $this->assertAttributeEquals(Stub\Foo::class, 'concrete', $definition);
    }

    /**
     * @covers ::setClass
     */
    public function testSetClass()
    {
        $container = new Container;

        $definition = new Definition($container);

        $this->assertAttributeEmpty('concrete', $definition);

        $definition->setClass(Stub\Foo::class);

        $this->assertAttributeEquals(Stub\Foo::class, 'concrete', $definition);
    }

    /**
     * @covers ::withArgs
     */
    public function testWithArgs()
    {
        $container = new Container;

        $definition = new Definition($container);

        $this->assertAttributeEmpty('args', $definition);

        $bar = new Stub\Bar;

        $definition->withArgs([$bar]);

        $this->assertAttributeEquals([$bar], 'args', $definition);
    }

    /**
     * @covers ::resolve
     */
    public function testResolve()
    {
        $container = new Container;

        $definition = new Definition($container, Stub\Foo::class);

        $resolved = $definition->resolve();

        $this->assertInstanceOf(Stub\Foo::class, $resolved);
    }

    /**
     * @convers ::resolve
     * @expectedException \Wright\DI\ResolveException
     * @expectedExceptionMessage
     */
    public function testResolveResolveExceptionNoClass()
    {
        $container = new Container;

        $definition = new Definition($container);

        $resolved = $definition->resolve();
    }

    /**
     * @covers ::resolveArgs
     */
    public function testResolveArgsDefinitionDefined()
    {
        $container = new Container;

        $definition = new Definition($container, Stub\Foo::class);

        $container->bind(Stub\Bar::class);

        $definition->withArgs([
            'bar' => $container->get(Stub\Bar::class)
        ]);

        $resolved = $definition->resolve();

        $this->assertAttributeInstanceOf(Stub\Bar::class, 'bar', $resolved);
    }

    /**
     * @covers ::resolveArgs
     */
    public function testResolveArgsClassDefined()
    {
        $container = new Container;

        $definition = new Definition($container, Stub\Foo::class);

        $container->bind(Stub\Bar::class);

        $definition->withArgs([
            'bar' => Stub\Bar::class
        ]);

        $resolved = $definition->resolve();

        $this->assertAttributeInstanceOf(Stub\Bar::class, 'bar', $resolved);
    }

    /**
     * @covers ::resolveArgs
     */
    public function testResolveArgsArrayDefined()
    {
        $container = new Container;

        $definition = new Definition($container, Stub\Baz::class);

        $container->bind(Stub\Bar::class);

        $definition->withArgs([
            'baz' => [
                'a string',
                $container->get(Stub\Bar::class)
            ]
        ]);

        $resolved = $definition->resolve();

        $baz = $this->readAttribute($resolved, 'baz');

        $this->assertEquals('a string', $baz[0]);

        $this->assertInstanceOf(Stub\Bar::class, $baz[1]);
    }

    /**
     * @covers ::resolveArgs
     */
    public function testResolveArgsClassNull()
    {
        $container = new Container;

        $definition = new Definition($container, Stub\Foo::class);

        $resolved = $definition->resolve();

        $this->assertAttributeEquals(null, 'bar', $resolved);
    }

    /**
     * @covers ::resolveArgs
     */
    public function testResolveArgsClass()
    {
        $container = new Container;

        $definition = new Definition($container, Stub\Qux::class);

        $container->bind(Stub\Bar::class);

        $resolved = $definition->resolve();

        $this->assertAttributeInstanceOf(Stub\Bar::class, 'bar', $resolved);
    }

    /**
     * @covers ::resolveArgs
     */
    public function testResolveArgsOptional()
    {
        $container = new Container;

        $definition = new Definition($container, Stub\Baz::class);

        $resolved = $definition->resolve();

        $this->assertAttributeEquals('the default of baz', 'baz', $resolved);
    }
}
