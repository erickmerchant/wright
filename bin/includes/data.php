<?php namespace Wright\Data;

use Symfony\Component\Yaml\Yaml;
use Michelf\SmartyPants;
use Michelf\MarkdownExtra;
use Wright\Extensions\Twig;
use Wright\Converter\MarkdownConverter;

$container->bind(DataInterface::class, StandardData::class, [

        'data_filesystem' => $container->get('data_filesystem'),

        'yaml' => $container->definition(Yaml::class)
    ])
    ->after(function($data){

        $markdown = new MarkdownConverter(new MarkdownExtra, new SmartyPants);

        $data->addConverter('md', $markdown);

        $data->addConverter('markdown', $markdown);
    });

$container->alias(DataInterface::class, 'data');
