<?php namespace Wright\Data;

use Symfony\Component\Yaml\Yaml;
use Michelf\SmartyPants;
use Michelf\MarkdownExtra;
use Wright\Extensions\Twig;
use Imagine\Gd\Imagine;

$container->bind(DataInterface::class, MarkdownData::class, [

        'data_filesystem' => $container->get('data_filesystem'),

        'twig' => $container->get('data_twig'),

        'yaml' => $container->definition(Yaml::class),

        'markdown' => $container->definition(MarkdownExtra::class),

        'smartypants' => $container->definition(SmartyPants::class)
    ]);


$container->bind('data_twig', \Twig_Environment::class, [
        'loader' => $container->definition(\Twig_Loader_String::class)
    ])
    ->after(function ($twig) {

        $twig->addExtension(new Twig\StandardExtension);

        $twig->addExtension(new Twig\ThumbnailExtension(
            new Imagine,
            getcwd() . '/data/',
            getcwd() . '/site/'
        ));
    });
