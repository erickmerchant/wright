<?php namespace Wright\Test\DI\Stub;

class Baz
{
    protected $baz;

    public function __construct($baz = 'the default of baz')
    {
        $this->baz = $baz;
    }
}
