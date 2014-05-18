<?php namespace Wright\Data;

use Symfony\Component\Yaml\Yaml;
use Wright\Extensions\Twig;

$container->bind(DataInterface::class, StandardData::class, [

        'data_filesystem' => $container->get('data_filesystem'),

        'yaml' => $container->definition(Yaml::class)
    ]);

$container->alias(DataInterface::class, 'data');
