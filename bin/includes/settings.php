<?php namespace Wright\Settings;

use Symfony\Component\Yaml\Yaml;

$container->bind(SettingsInterface::class, YamlSettings::class, [

    'settings_filesystem' => $container->get('settings_filesystem'),

    'yaml' => $container->definition(Yaml::class)
]);
