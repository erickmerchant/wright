<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

$container->bind('base_filesystem', Filesystem::class, [
    'adapter' => $container->definition(Local::class, [ 'root' => getcwd() . '/base/' ])
]);

$container->bind('site_filesystem', Filesystem::class, [
    'adapter' => $container->definition(Local::class, [ 'root' => getcwd() . '/../erickmerchant.github.io/' ])
]);

$container->bind('data_filesystem', Filesystem::class, [
    'adapter' => $container->definition(Local::class, [ 'root' => getcwd() . '/data/' ])
]);

$container->bind('settings_filesystem', Filesystem::class, [
    'adapter' => $container->definition(Local::class, [ 'root' => getcwd() . '/settings/' ])
]);
