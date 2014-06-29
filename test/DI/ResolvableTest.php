<?php namespace Wright\Test\DI;

use Wright\DI\Container;
use Wright\DI\Resolvable;

/**
 * @coversDefaultClass Wright\DI\Resolvable
 */
class ResolvableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructWithClass()
    {
        $container = new Container;

        $closure = function () {};

        $definition = new Resolvable($container, $closure);

        $this->assertAttributeEquals($closure, 'closure', $definition);
    }

    /**
     * @covers ::resolve
     */
    public function testResolve()
    {
        $container = new Container;

        $closure = function () { return new Stub\Foo; };

        $definition = new Resolvable($container, $closure);

        $foo = $definition->resolve();

        $this->assertInstanceOf(Stub\Foo::class, $foo);
    }
}
