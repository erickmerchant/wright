<?php namespace Wright\View;

use Wright\Extensions\Twig;

$container->bind(ViewInterface::class, TwigView::class, [
    'twig' => $container->get('view_twig')
]);

$container->bind('view_twig', \Twig_Environment::class, [
        'loader' => $container->definition(\Twig_Loader_Filesystem::class, [
            'paths' => getcwd() . '/templates'
        ]),
        'options' => [
            'cache' => getcwd() . '/cache/twig',
            'auto_reload' => true
        ]
    ])
    ->after(function ($twig) {

        $twig->addExtension(new Twig\StandardExtension);
    });
