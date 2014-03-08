<?php namespace Wright\Command;

use Aura\Cli\Stdio;

interface CommandInterface
{
    public function getDescription();

    public function getOptions();

    public function getArguments();

    public function execute(Stdio $stdio, array $params = []);
}
