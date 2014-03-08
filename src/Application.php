<?php namespace Wright;

use Aura\Cli\Status;
use Aura\Cli\Stdio;
use Aura\Cli\Context;

class Application
{
    /**
     * Run `figlet -W -f small Wright` from the terminal to generate this.
     *
     * @var string
     */
    const LOGO = <<<'WRIGHT'
__      __        _          _      _
\ \    / /  _ _  (_)  __ _  | |_   | |_
 \ \/\/ /  | '_| | | / _` | | ' \  |  _|
  \_/\_/   |_|   |_| \__, | |_||_|  \__|
                     |___/
WRIGHT;

    protected $commands;

    public function __construct(array $commands = [])
    {
        $this->commands = $commands;
    }

    public function run(Context $context, Stdio $stdio)
    {
        $getopt = $context->getopt(['--help,h']);

        $command_name = $getopt->get(1);

        if ($command_name) {

            $command_name = $this->findCommand($command_name);

            if (isset($this->commands[$command_name])) {

                if ($getopt->get('--help', false)) {

                    if ($getopt->hasErrors()) {

                        $errors = $getopt->getErrors();

                        foreach ($errors as $error) {

                            $stdio->errln('<<redbg white>>' . $error->getMessage() . '<<reset>>');
                        }

                        $status = Status::USAGE;

                    } else {

                        $stdio->outln('<<magenta>>Description:<<reset>>');

                        $stdio->outln($this->commands[$command_name]->getDescription());

                        $arguments = $this->commands[$command_name]->getArguments();

                        $options = $this->commands[$command_name]->getOptions();

                        $stdio->outln('');

                        $stdio->outln('<<magenta>>Usage:<<reset>>');

                        $usage[] = './wright ' . $command_name;

                        if (count($options)) {

                            $usage[] = '[options]';
                        }

                        if (count($arguments)) {

                            $usage[] = '[arguments]';
                        }

                        $stdio->outln(implode(' ', $usage));

                        if (count($options)) {

                            $stdio->outln('');

                            $stdio->outln('<<magenta>>Options:<<reset>>');

                            $longest = max(array_map('strlen', array_keys($options)));

                            foreach ($options as $key => $val) {

                                $description = isset($val['description']) ? $val['description'] : '';

                                $stdio->outln('<<cyan>>' . str_pad($key, $longest) . '<<reset>>  ' . $description);
                            }
                        }

                        if (count($arguments)) {

                            $stdio->outln('');

                            $stdio->outln('<<magenta>>Arguments:<<reset>>');

                            $longest = max(array_map('strlen', array_keys($arguments)));

                            foreach ($arguments as $key => $val) {

                                $description = isset($val['description']) ? $val['description'] : '';

                                $stdio->outln('<<cyan>>' . str_pad($key, $longest) . '<<reset>>  ' . $description);
                            }
                        }

                        $status = Status::USAGE;
                    }

                } else {

                    $params = [];

                    $getopt = $context->getopt(array_keys($this->commands[$command_name]->getOptions()));

                    $errors = false;

                    $k = 2;

                    foreach ($this->commands[$command_name]->getArguments() as $argument_name => $argument) {

                        if ($getopt->get($k) == null) {

                            if (!isset($argument['default'])) {

                                $errors = true;

                            } else {

                                $params[$argument_name] = $argument['default'];
                            }

                        } else {

                            $params[$argument_name] = $getopt->get($k);
                        }

                        $k++;
                    }

                    foreach ($getopt->getOptions() as $option) {

                        $params[$option['name']] = $getopt->get($option['name']);
                    }

                    if ($errors || $getopt->hasErrors()) {

                        $errors = $getopt->getErrors();

                        foreach ($errors as $error) {

                            $stdio->errln('<<redbg white>>' . $error->getMessage() . '<<reset>>');
                        }

                        $k = 2;

                        foreach ($this->commands[$command_name]->getArguments() as $argument_name => $argument) {

                            if (!isset($argument['default']) && $getopt->get($k) == null) {

                                $name = isset($argument_name) ? $argument_name : 'argument ' . $k;

                                $stdio->errln('<<redbg white>>The argument \'' . $name . '\' is required.<<reset>>');

                                $errors = true;
                            }

                            $k++;
                        }

                        $stdio->errln('<<cyan>>Try running ./wright ' . $command_name . ' --help<<reset>>');

                        $status = Status::USAGE;

                    } else {

                        $start_time = microtime(true);

                        $status = $this->commands[$command_name]->execute($stdio, $params);

                        if ($status == Status::SUCCESS) {

                            $stdio->outln('<<cyan>>' . $command_name . ' ran in ' . $this->getTime($start_time) . ' using ' . $this->getMemoryUsage() . '<<reset>>');
                        }
                    }
                }

            } else {

                $stdio->errln('<<redbg white>>There is no command \'' . $command_name . '\'<<reset>>');
            }

        } else {

            if ($getopt->hasErrors()) {

                $errors = $getopt->getErrors();

                foreach ($errors as $error) {

                    $stdio->errln('<<redbg white>>' . $error->getMessage() . '<<reset>>');
                }

                $status = Status::USAGE;

            } else {

                $stdio->outln('<<magenta bold>>' . self::LOGO . '<<reset>>');

                $stdio->outln('a static site generator');

                $stdio->outln('');

                $stdio->outln('<<magenta>>Usage:<<reset>>');

                $stdio->outln('./wright command [options] [arguments]');

                $stdio->outln('');

                $stdio->outln('<<magenta>>Commands:<<reset>>');

                $longest = max(array_map('strlen', array_keys($this->commands)));

                foreach ($this->commands as $command_name => $command) {

                    $stdio->outln('<<cyan>>' . str_pad($command_name, $longest) . '<<reset>>  ' . $command->getDescription());
                }

                $stdio->outln('');

                $stdio->outln('<<magenta>>Options:<<reset>>');

                $stdio->outln('<<cyan>>--help,h<<reset>>  get a command\'s usage');

                $stdio->outln('* Each command may have additional options.');

                $stdio->outln('');

                $stdio->outln('<<magenta>>Arguments:<<reset>>');

                $stdio->outln('Each command may have arguments.');

                $stdio->outln('');

                $stdio->outln('<<magenta>>Example:<<reset>>');

                $stdio->outln('./wright foo --help');

                $status = Status::USAGE;
            }
        }

        exit($status);
    }

    protected function findCommand($command_name)
    {
        if (!isset($this->commands[$command_name])) {

            $matches = [];

            foreach (array_keys($this->commands) as $possible_command_name) {

                if (strpos($possible_command_name, $command_name) === 0) {

                    $matches[] = $possible_command_name;
                }
            }

            if (count($matches) == 1) {

                $command_name = $matches[0];

            }
        }

        return $command_name;
    }

    protected function getTime($time)
    {
        $diff = number_format(microtime(true) - $time, 3);

        return $diff . 's';
    }

    protected function getMemoryUsage()
    {
        $usage = memory_get_peak_usage(true);

        $formats = ['B', 'KB', 'MB', 'GB'];

        $limit = 1;

        foreach ($formats as $format) {

            $result = round($usage / $limit, 2) . $format;

            $limit *= 1024;

            if ($usage < $limit) {

                break;
            }
        }

        return $result;
    }
}
