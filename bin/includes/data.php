<?php namespace Wright\Data;

use Symfony\Component\Yaml\Yaml;
use Michelf\SmartyPants;
use Michelf\MarkdownExtra;
use Wright\Extensions\Twig;
use Wright\Converter\MarkdownConverter;

$container->bind(DataInterface::class, StandardData::class, [

        'data_filesystem' => $container->get('data_filesystem'),

        'twig' => $container->get('data_twig'),

        'yaml' => $container->definition(Yaml::class)
    ])
    ->after(function($data){

        $markdown = new MarkdownConverter(new MarkdownExtra, new SmartyPants);

        $data->addConverter('md', $markdown);

        $data->addConverter('markdown', $markdown);
    });

$container->alias(DataInterface::class, 'data');

$container->bind('data_twig', \Twig_Environment::class, [
        'loader' => $container->definition(\Twig_Loader_String::class)
    ])
    ->after(function ($twig) {

        $twig->addExtension(new Twig\StandardExtension);
    });
