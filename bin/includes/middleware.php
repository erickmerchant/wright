<?php namespace Wright\Middleware;

$container->bind(MiddlewareManager::class);

$container->alias(MiddlewareManager::class, 'middleware');
