<?php namespace Wright\Data;

use Symfony\Component\Yaml\Yaml;
use Michelf\SmartyPants;
use Michelf\MarkdownExtra;
use Wright\Extensions\Twig;
use Imagine\Gd\Imagine;

$container->bind(DataInterface::class, MarkdownData::class, [

        'data_filesystem' => $container->get('data_filesystem'),

        'twig' => $container->resolvable(function ($container) {

            $twig_loader = new \Twig_Loader_String();

            $twig = new \Twig_Environment($twig_loader);

            $twig->addExtension(new Twig\StandardExtension);

            $twig->addExtension(new Twig\ThumbnailExtension(
                new Imagine,
                getcwd() . '/data/',
                getcwd() . '/site/'
            ));

            return $twig;
        }),

        'yaml' => $container->definition(Yaml::class),

        'markdown' => $container->definition(MarkdownExtra::class),

        'smartypants' => $container->definition(SmartyPants::class)
    ]);
