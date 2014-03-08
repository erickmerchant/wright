<?php namespace Wright\Test\DI;

use Wright\DI\Container;
use Wright\DI\Definition;

/**
 * @coversDefaultClass Wright\DI\Container
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::definition
     */
    public function testDefinition()
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
     * @covers ::get
     */
    public function testgetDefinition()
    {
        $container = new Container;

        $definition = $container->bind(Stub\FooInterface::class, Stub\Foo::class);

        $definition2 = $container->get(Stub\FooInterface::class);

        $this->assertSame($definition, $definition2);
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
