<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

$container->bind('source_filesystem', Filesystem::class, [
    'adapter' => $container->definition(Local::class, [ 'root' => getcwd() . '/' ])
]);

$container->bind('site_filesystem', Filesystem::class, [
    'adapter' => $container->definition(Local::class, [ 'root' => getcwd() . '/site/' ])
]);
