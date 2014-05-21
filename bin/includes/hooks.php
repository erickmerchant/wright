<?php namespace Wright\Hooks;

$container->bind(HooksManager::class, HooksManager::class);

$container->alias(HooksManager::class, 'hooks');
