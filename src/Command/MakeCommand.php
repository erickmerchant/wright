<?php namespace Wright\Command;

use Aura\Cli\Status;
use Aura\Cli\Stdio;
use Wright\Data\DataInterface;
use Wright\Hooks\HooksManagerInterface;

class MakeCommand implements CommandInterface
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
        return 'make a new entry';
    }

    public function getOptions()
    {
        return [
            'target,t:' => [
                'description' => 'the collection to add the entry to'
            ],
            'related,r*:' => [
                'description' => 'other entries that this one is related to'
            ],
            'date,d' => [
                'description' => 'add the current date to the file name'
            ],
            'ext,e' => [
                'description' => 'the file extension to add. defaults to md'
            ]
        ];
    }

    public function getArguments()
    {
        return [
            'title' => [
                'description' =>'the title of the entry'
            ],
            'summary' => [
                'description' =>'a short description of the entry (optional)',
                'default' => ''
            ]
        ];
    }

    public function execute(Stdio $stdio, array $params = [])
    {
        if (strpos($params['--target'], 'data/') !== 0) {
            throw new \DomainException('The --target must be in the data directory.');
        }

        $params['--target'] = substr($params['--target'], strlen('data/'));

        if (!empty($params['--related'])) {

            foreach ($params['--related'] as $key => $related) {

                if (strpos($related, 'data/') !== 0) {
                    throw new \DomainException('Each --related must be in the data directory.');
                }

                $params['--related'][$key] = substr(dirname($related), strlen('data/')) . '/' . pathinfo($related, PATHINFO_FILENAME);
            }
        }

        $data = [];

        foreach (['title', 'summary', '--related'] as $param_key) {

            if (!empty($params[$param_key])) {

                $data[trim($param_key, '-')] = $params[$param_key];
            }
        }

        $file = '';

        if ($params['--target']) {

            $file = trim($params['--target'], '/') . '/';
        }

        if ($params['--date']) {

            $file .= (new \DateTime)->format('Y-m-d') . '.';
        }

        $file .= \URLify::filter($data['title']);

        $file .= $params['--ext'] ? $params['--ext'] : '.md';

        $file = trim($file, '/');

        $this->hooks->call('before.make');

        $this->data->write($file, $data);

        $stdio->outln('<<magenta>>' . $file . ' published on ' . (new \DateTime)->format('Y-m-d') . '<<reset>>');

        $this->hooks->call('after.make');

        return Status::SUCCESS;
    }
}
