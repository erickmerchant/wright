<?php namespace Wright;

use Aura\Cli\CliFactory;

$cli_factory = new CliFactory;

$container->bind(Application::class, Application::class, [
        'stdio' => $container->resolvable(function () use ($cli_factory) {

            return $cli_factory->newStdio();
        }),
        'context' => $container->resolvable(function () use ($cli_factory) {

            return $cli_factory->newContext($GLOBALS);
        }),
        'commands' => [
            'publish' => $container->definition(Command\PublishCommand::class, [
                'base_filesystem' => $container->get('base_filesystem'),
                'site_filesystem' => $container->get('site_filesystem')
            ]),
            'make' => $container->definition(Command\MakeCommand::class),
            'move' => $container->definition(Command\MoveCommand::class)
        ]
    ]);
