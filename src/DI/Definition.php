<?php namespace Wright\DI;

class Definition implements ResolvableInterface
{
    /**
     * The container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The class that will be resolved
     *
     * @var \ReflectionClass|null
     */
    protected $concrete = null;

    /**
     * The arguments that the class will be constructed with.
     *
     * @var array
     */
    protected $args = [];

    /**
     * The setters that will be called after instantiation.
     *
     * @var array Each array will contain 2 elements. The first is a string that is the method. The second is an array of arguments to pass to that method.
     */
    protected $setters = [];

    /**
     * @param Container   $container The container.
     * @param string|null $concrete  The class to be reflected and ultimately resolved
     */
    public function __construct(Container $container, $concrete = null, array $args = [])
    {
        /**
         * @todo validate that $concrete is a string
         */

        $this->container = $container;

        if (!empty($concrete)) {

            $this->concrete = $concrete;
        }

        if (isset($args)) {

            $this->withArgs($args);
        }
    }

    /**
     * Set the class that will be resolved.
     *
     * @param string $concrete
     */
    public function setClass($concrete)
    {
        /**
         * @todo validate that $concrete is a string
         */

        $this->concrete = $concrete;
    }

    /**
     * Set the arguments it will be resolved with.
     *
     * @param  array $args
     * @return self
     */
    public function withArgs(array $args = [])
    {
        $this->args = $args;

        return $this;
    }

    /**
     * Set a method to be called after constructing this class along with it's arguments.
     *
     * @param  string $method
     * @param  array  $args
     * @return self
     */
    public function withSetter($method, array $args = [])
    {
        $this->setters[] = [$method, $args];

        return $this;
    }

    /**
     * Resolves the definition
     *
     * @throws DefinitionException If a class has not yet been set.
     * @return object
     */
    public function resolve()
    {
        $instance = null;

        $args = [];

        if (isset($this->concrete)) {

            $reflected_class = new \ReflectionClass($this->concrete);

        } else {

            throw new ResolveException;
        }

        $reflected_method = $reflected_class->getConstructor();

        if ($reflected_method) {
            $args = $this->resolveArgs($reflected_method, $this->args);
        }

        $instance = $reflected_class->newInstanceArgs($args);

        foreach ($this->setters as list($method, $args)) {
            $reflected_method = $reflected_class->getMethod($method);

            $args = $this->resolveArgs($reflected_method, $args);

            $reflected_method->invokeArgs($instance, $args);
        }

        return $instance;
    }

    protected function resolveArgs(\ReflectionMethod $reflected_method, $args)
    {
        $results = [];

        if (is_array($args)) {

            foreach ($reflected_method->getParameters() as $key => $reflected_parameter) {

                try {

                    $class = $reflected_parameter->getClass();

                    $name = $reflected_parameter->getName();

                    if (isset($args[$name])) {
                        $results[$key] = $args[$name];

                        if (is_object($results[$key]) && $results[$key] instanceof ResolvableInterface) {

                            $results[$key] = $results[$key]->resolve();
                        } elseif ($class && is_string($results[$key])) {

                            $results[$key] = $this->container->resolve($results[$key]);
                        } elseif (is_array($results[$key])) {
                            array_walk_recursive($results[$key], function (&$item) {

                                if (is_object($item) && $item instanceof ResolvableInterface) {
                                    $item = $item->resolve();
                                }
                            });
                        }
                    } elseif ($class) {
                        if ($reflected_parameter->allowsNull()) {
                            $results[$key] = null;
                        } else {
                            $results[$key] = $this->container->resolve($class->getName());
                        }
                    } elseif ($reflected_parameter->isOptional()) {
                        $results[$key] = $reflected_parameter->getDefaultValue();
                    }
                } catch (DefinitionException $e) {
                    throw new ResolveArgsException('Can not resolve argument ' . $reflected_parameter->getName() . ' while resolving arguments for ' . $reflected_method->getName(), 0, $e);
                }
            }
        }

        return $results;
    }
}
