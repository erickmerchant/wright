<?php namespace Wright\Data;

use Symfony\Component\Yaml\Yaml;
use League\Flysystem\FilesystemInterface;
use Wright\Converter\ConverterInterface;

class StandardData implements \IteratorAggregate, DataInterface
{
    protected $source_filesystem;

    protected $yaml;

    protected $converters = [];

    public function __construct(FilesystemInterface $source_filesystem, Yaml $yaml)
    {
        $this->source_filesystem = $source_filesystem;

        $this->yaml = $yaml;
    }

    public function addConverter($ext, ConverterInterface $converter)
    {
        $this->converters[$ext] = $converter;
    }

    public function getIterator()
    {
        $result = [];

        foreach ($this->source_filesystem->listPaths('/data/', true) as $path) {

            $ext = pathinfo($path, PATHINFO_EXTENSION);

            if (!isset($this->converters[$ext])) {

                continue;
            }

            $directory = substr(trim(dirname($path), '/'), 5);

            $file = trim(basename($path), '/');

            if ($directory == '') {

                $result[] = $file;

            } else {

                $result[] = $directory . '/' . $file;
            }
        }

        return new \ArrayIterator($result);
    }

    /**
     * Writes a data file.
     *
     * @param  string $file The file name
     * @param  array  $data Data to write to the file.
     * @return void
     */
    public function write($file, $data)
    {
        $file = '/data/' . $file;

        /**
         * @todo validate that $file is a string
         * @todo validate that $data is a string
         */

        $data = json_decode(json_encode($data), true);

        $data_content = isset($data['content']) ? $data['content'] : '';

        unset($data['content']);

        $data = '---' . PHP_EOL . trim($this->yaml->dump($data, 5)) . PHP_EOL . '---' . PHP_EOL . $data_content;

        $this->source_filesystem->createDir(dirname($file));

        $this->source_filesystem->put($file, $data);
    }

    /**
     * Reads a data file.
     *
     * @param  string $file A file to read.
     * @return array  The data read.
     */
    public function read($file)
    {
        $file = '/data/' . $file;

        /**
         * @todo validate that $file is a string
         */

        $result = [];

        $ext = pathinfo($file, PATHINFO_EXTENSION);

        if (isset($this->converters[$ext])) {

            $meta = [];

            $content = '';

            $file_contents = $this->source_filesystem->read($file);

            if ($file_contents) {

                $parts = preg_split('/\-{3}/', $file_contents, 3);

                if (count($parts) === 3) {

                    $meta = $parts[1] ?: [];

                    $content = $parts[2];
                } else {

                    $content = $file_contents;
                }

                $result = $this->yaml->parse($meta);

                $content = $this->converters[$ext]->convert($content);

                $result['content'] = trim($content);
            }
        }

        return $result;
    }

    public function move($source, $target)
    {
        $source = '/data/' . $source;

        $target = '/data/' . $target;

        $this->source_filesystem->createDir(dirname($target));

        $this->source_filesystem->rename($source, $target);
    }
}
