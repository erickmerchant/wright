<?php namespace Wright\Middleware;

use Wright\Middleware\FirstMiddleware;
use Wright\Middleware\PaginateMiddleware;

$container->bind(MiddlewareManagerInterface::class, MiddlewareManager::class);

$container->alias(MiddlewareManagerInterface::class, 'middleware');

$container->after('middleware', function($middleware){

    $middleware->register('first', new FirstMiddleware);

    $middleware->register('paginate', new PaginateMiddleware);

    $middleware->register('redirect', new RedirectMiddleware);
});
