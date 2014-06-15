<?php namespace Wright\Hooks;

interface HooksManagerInterface
{
    public function add($hook, callable $callable);

    public function call($hook, array $args = []);
}
