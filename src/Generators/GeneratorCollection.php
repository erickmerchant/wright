<?php namespace Wright\Generators;

class GeneratorCollection
{
    protected $generators = [];

    public function add(callable $callable)
    {
        $this->generators[] = $callable;
    }

    public function run()
    {
        foreach($this->generators as $generator) {

            call_user_func($generator);
        }
    }
}
