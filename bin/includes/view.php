<?php namespace Wright\View;

use Wright\Extensions\Twig;

$container->bind(ViewInterface::class, TwigView::class, [
    'twig' => $container->resolvable(function ($container) {

        $twig_loader = new \Twig_Loader_Filesystem(getcwd() . '/templates');

        $twig = new \Twig_Environment($twig_loader, [
            'cache' => getcwd() . '/cache/twig',
            'auto_reload' => true
        ]);

        $twig->addExtension(new Twig\StandardExtension);

        $twig->addExtension(new Twig\DateBatchExtension);

        return $twig;
    })
]);
