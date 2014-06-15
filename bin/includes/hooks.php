<?php namespace Wright\Hooks;

$container->bind(HooksManagerInterface::class, HooksManager::class);

$container->alias(HooksManagerInterface::class, 'hooks');
