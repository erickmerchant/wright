<?php namespace Wright;

use Aura\Cli\Stdio;
use Aura\Cli\Context;
use Aura\Cli\Stdio\Handle;
use Aura\Cli\Stdio\Formatter;

$container->bind(Application::class, Application::class, [
        'stdio' => $container->definition(Stdio::class, [
            'stdin' => $container->resolvable(function(){

                return new Handle('php://stdin', 'r');
            }),
            'stdout' => $container->resolvable(function(){

                return new Handle('php://stdout', 'w+');
            }),
            'stderr' => $container->resolvable(function(){

                return new Handle('php://stderr', 'w+');
            }),
            'formatter' => $container->definition(Formatter::class)
        ]),
        'context' => $container->definition(Context::class, [
            'env'    => $container->resolvable(function(){

                return new Context\Env($_ENV);
            }),
            'server' => $container->resolvable(function(){

                return new Context\Server($_SERVER);
            }),
            'argv'   => $container->resolvable(function(){

                return new Context\Argv(isset($_SERVER['argv']) ? $_SERVER['argv'] : array());
            }),
            'getopt' => $container->definition(Context\Getopt::class)
        ]),
        'commands' => [
            'publish' => $container->definition(Command\PublishCommand::class, [
                'base_filesystem' => $container->get('base_filesystem'),
                'site_filesystem' => $container->get('site_filesystem')
            ]),
            'make' => $container->definition(Command\MakeCommand::class),
            'move' => $container->definition(Command\MoveCommand::class)
        ]
    ]);
