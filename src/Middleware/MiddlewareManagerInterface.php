<?php namespace Wright\Middleware;

use Wright\Model\NodeModel;

interface MiddlewareManagerInterface
{
    public function register($name, callable $callable);

    public function call($name, array $pages, array $arguments = []);
}
