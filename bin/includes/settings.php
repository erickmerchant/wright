<?php namespace Wright\Settings;

use Symfony\Component\Yaml\Yaml;

$container->bind(SettingsInterface::class, YamlSettings::class, [

    'source_filesystem' => $container->get('source_filesystem'),

    'yaml' => $container->definition(Yaml::class)
]);
