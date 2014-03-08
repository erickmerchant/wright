<?php namespace Wright\Test\DI\Stub;

class Qux
{
    protected $bar;

    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }
}
