<?php namespace Wright\Hooks;

class HooksManager implements HooksManagerInterface
{
    protected $hooks = [];

    public function add($hook, callable $callable)
    {
        $this->hooks[$hook][] = $callable;
    }

    public function call($hook, array $args = [])
    {
        if(isset($this->hooks[$hook]))
        {
            foreach($this->hooks[$hook] as $hook) {

                call_user_func_array($hook, $args);
            }
        }
    }
}
