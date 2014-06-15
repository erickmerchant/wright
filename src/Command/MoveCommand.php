<?php namespace Wright\Command;

use Aura\Cli\Status;
use Aura\Cli\Stdio;
use Wright\Data\DataInterface;
use Wright\Hooks\HooksManagerInterface;

class MoveCommand implements CommandInterface
{
    protected $data;

    protected $hooks;

    public function __construct(HooksManagerInterface $hooks, DataInterface $data)
    {
        $this->data = $data;

        $this->hooks = $hooks;
    }

    public function getDescription()
    {
        return 'move an entry';
    }

    public function getOptions()
    {
        return [
            'date,d' => [
                'description' => 'add the current date to the file name'
            ],
            'title,t:' => [
                'description' =>'change the title of the entry'
            ]
        ];
    }

    public function getArguments()
    {
        return [
            'source' => [
                'description' =>'the entry to move'
            ],
            'target' => [
                'description' =>'the directory to move it to (the collection)'
            ]
        ];
    }

    public function execute(Stdio $stdio, array $params = [])
    {
        if (strpos($params['source'], 'data/') !== 0) {
            throw new \DomainException('The source must be in the data directory.');
        }

        if (strpos($params['target'], 'data/') !== 0) {
            throw new \DomainException('The target must be in the data directory.');
        }

        $params['source'] = substr($params['source'], strlen('data/'));

        $params['target'] = substr($params['target'], strlen('data/'));

        $match = preg_match('/^(?:.*?\/)?([0-9]{4}-[0-9]{2}-[0-9]{2}\.|[0-9]+\.|)([^\/]*)$/', $params['source'], $matches);

        $matches[1] = trim($matches[1], '.');

        if ($params['--date']) {

            $matches[1] = (new \DateTime)->format('Y-m-d');
        }

        if ($params['--title']) {

            $matches[2] = \URLify::filter($params['--title']);
        }

        $file = trim($params['target'], '/') . '/';

        $file .= $params['--date'] ? $matches[1] . '.' : '';

        $file .= $matches[2];

        $this->hooks->call('before.move');

        $this->data->move($params['source'], $file);

        if ($params['--title']) {

            $data = $this->data->read($file);

            $data['title'] = $params['--title'];

            $this->data->write($file, $data);
        }

        $stdio->outln('<<magenta>>' . $params['source'] . ' moved to ' . $file . '<<reset>>');

        $this->hooks->call('after.move');

        return Status::SUCCESS;
    }
}
