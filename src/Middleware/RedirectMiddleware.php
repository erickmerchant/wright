<?php namespace Wright\Middleware;

class RedirectMiddleware
{
    public function __invoke($pages, $redirect)
    {
        $results = [];

        foreach ($pages as $permalink => $page) {

            $page['redirect'] = $redirect;

            $results[$permalink] = $page;
        }

        return $results;
    }
}
