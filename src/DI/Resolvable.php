<?php namespace Wright\DI;

class Resolvable implements DefinitionInterface
{
    /**
     * The container.
     *
     * @var Container
     */
    protected $container;

    protected $closure;

    /**
     * @param Container   $container The container.
     * @param string|null $concrete  The class to be reflected and ultimately resolved
     */
    public function __construct(Container $container, \Closure $closure)
    {
        $this->container = $container;

        $this->closure = $closure;
    }

    public function resolve()
    {
        $closure = $this->closure;

        return $closure($this->container);
    }
}
