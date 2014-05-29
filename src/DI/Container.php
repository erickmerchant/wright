<?php namespace Wright\DI;

class Container
{
    /**
     * The definitions that are bound.
     *
     * @var Definition[]
     */
    protected $definitions = [];

    /**
     * Instantiate a new Definition
     *
     * @param  string|null $concrete A class. Not an interface.
     * @return Definition
     */
    public function definition($concrete = null, array $args = [])
    {
        /**
         * @todo validate that $concrete is a string or null
         */

        $definition = new Definition($this, $concrete, $args);

        return $definition;
    }

    /**
     * Instantiate a new Definition
     *
     * @param  string|null $concrete A class. Not an interface.
     * @return Definition
     */
    public function resolvable(\Closure $concrete)
    {
        /**
         * @todo validate that $concrete is a string or null
         */

        $definition = new Resolvable($this, $concrete);

        return $definition;
    }

    /**
     * Return a bound definition if set or bind a new definition and return it.
     *
     * @param  string     $abstract A class. Probably an interface.
     * @return Definition
     */
    public function get($abstract)
    {
        /**
         * @todo validate that $abstract is a string
         */

        if (!isset($this->definitions[$abstract])) {

            $this->definitions[$abstract] = new Definition($this);
        }

        return $this->definitions[$abstract];
    }

    public function alias($abstract, $alias)
    {
        $definition = $this->get($abstract);

        $this->definitions[$alias] = $definition;

        return $definition;
    }

    public function after($abstract, \Closure $after)
    {
        $definition = $this->get($abstract);

        $definition->after($after);

        return $definition;
    }

    public function resolve($abstract)
    {
        /**
         * @todo validate that $abstract is a string
         */

        if (isset($this->definitions[$abstract])) {
            return $this->definitions[$abstract]->resolve();
        }

        throw new ResolveException('Abstract ' . $abstract . ' is not defined');
    }

    /**
     * Bind a new definition or if it's already set, set the concrete class of the bound definition.
     *
     * @param  string      $abstract A class. Possibly an interface if $concrete is not null.
     * @param  string|null $concrete A class. Not an interface.
     * @return Definition
     */
    public function bind($abstract, $concrete = null, array $args = [])
    {
        /**
         * @todo validate that $abstract is a string
         * @todo validate that $concrete is a string or null
         */

        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if (isset($this->definitions[$abstract])) {

            $definition = $this->definitions[$abstract];

            $definition->setClass($concrete);

            $definition->withArgs($args);

        } else {

            $definition = new Definition($this, $concrete, $args);

            $this->definitions[$abstract] = $definition;
        }

        return $definition;
    }
}
