<?php namespace Wright\Middleware;

$container->bind(MiddlewareManagerInterface::class, MiddlewareManager::class);

$container->alias(MiddlewareManagerInterface::class, 'middleware');
