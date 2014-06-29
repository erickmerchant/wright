<?php namespace Wright\Middleware;

use Wright\Model\NodeModel;

class MiddlewareManager implements MiddlewareManagerInterface
{
    protected $middleware = [];

    public function register($name, callable $callable)
    {
        $this->middleware[$name] = $callable;
    }

    public function call($name, NodeModel $node)
    {
        if (!isset($this->middleware[$name])) {

            throw new \OutOfBoundsException('The middleware '.$name.' is not set.');
        }

        return call_user_func($this->middleware[$name], $node);
    }
}
