<?php namespace Wright\Test\DI\Stub;

class Foo implements FooInterface
{
    protected $bar;

    public function __construct(Bar $bar = null)
    {
        $this->bar = $bar;
    }

    public function setBar(Bar $bar)
    {
        $this->bar = $bar;
    }
}
