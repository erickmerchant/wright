<?php namespace Wright\Middleware;

use Wright\Middleware\FirstMiddleware;

$container->bind(MiddlewareManagerInterface::class, MiddlewareManager::class);

$container->alias(MiddlewareManagerInterface::class, 'middleware');

$container->after('middleware', function($middleware){

    $middleware->register('first', new FirstMiddleware);
});
