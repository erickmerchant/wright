<?php namespace Wright\Middleware;

class FirstMiddleware
{
    public function __invoke($pages)
    {
        $results = [];

        foreach ($pages as $permalink => $page) {

            $result = $page;

            $collection = $page['page']->collection();

            $result['page'] = array_shift($collection);

            $results[$permalink] = $result;
        }

        return $results;
    }
}
