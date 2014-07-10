<?php namespace Wright\Middleware;

use Wright\Model\NodeModel;

class MiddlewareManager implements MiddlewareManagerInterface
{
    protected $middleware = [];

    public function register($name, callable $callable)
    {
        $this->middleware[$name] = $callable;
    }

    public function call($name, array $pages, array $arguments = [])
    {
        if (!isset($this->middleware[$name])) {

            throw new \OutOfBoundsException('The middleware '.$name.' is not set.');
        }

        array_unshift($arguments, $pages);

        return call_user_func_array($this->middleware[$name], $arguments);
    }
}
