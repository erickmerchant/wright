<?php namespace Wright\Data;

use Symfony\Component\Yaml\Yaml;
use Michelf\MarkdownInterface;
use Michelf\SmartyPants;
use Twig_Environment;
use League\Flysystem\FilesystemInterface;

class MarkdownData implements \IteratorAggregate, DataInterface
{
    /**
     * The yaml object for read and writing front matter.
     *
     * @var Yaml
     */
    protected $yaml;

    /**
     * The markdown object for read markdown
     *
     * @var MarkdownExtra
     */
    protected $markdown;

    /**
     * SmartyPants for smart quotes and such.
     *
     * @var SmartyPants
     */
    protected $smartypants;

    protected $data_filesystem;

    protected $twig;

    public function __construct(FilesystemInterface $data_filesystem, Twig_Environment $twig, Yaml $yaml, MarkdownInterface $markdown, SmartyPants $smartypants = null)
    {
        $this->data_filesystem = $data_filesystem;

        $this->yaml = $yaml;

        $this->markdown = $markdown;

        $this->smartypants = $smartypants;

        $this->twig = $twig;
    }

    public function getIterator()
    {
        $result = [];

        foreach ($this->data_filesystem->listPaths('/', true) as $path) {

            if (substr($path, -strlen('.md')) !== '.md') {

                continue;
            }

            $directory = trim(dirname($path), '/');

            $file = trim(basename($path, '.md'), '/');

            if ($directory == '.') {

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
     * @param  string  $file  The file name
     * @param  array   $data  Data to write to the file.
     * @param  boolean $force If the file exists, should it be overwritten.
     * @return void
     */
    public function write($file, $data, $force = true)
    {
        /**
         * @todo validate that $file is a string
         * @todo validate that $data is a string
         * @todo validate that $force is a boolean
         */

        $data = json_decode(json_encode($data), true);

        $data_content = isset($data['content']) ? $data['content'] : '';

        unset($data['content']);

        $data = '---' . PHP_EOL . trim($this->yaml->dump($data, 5)) . PHP_EOL . '---' . PHP_EOL . $data_content;

        $this->data_filesystem->createDir(dirname($file . '.md'));

        $this->data_filesystem->put($file . '.md', $data);
    }

    /**
     * Reads a data file.
     *
     * @param  string $file A file to read.
     * @return array  The data read.
     */
    public function read($file)
    {
        /**
         * @todo validate that $file is a string
         */

        $result = [];

        $meta = [];

        $content = '';

        $file_contents = $this->data_filesystem->read($file . '.md');

        if ($file_contents) {

            $parts = preg_split('/\-{3}/', $file_contents, 3);

            if (count($parts) === 3) {

                $meta = $parts[1] ?: [];

                $content = $parts[2];
            } else {

                $content = $file_contents;
            }

            $result = $this->yaml->parse($meta);

            $template = $this->twig->loadTemplate($content);

            $content = $template->render([]);

            $content = $this->markdown->transform($content);

            if (!is_null($this->smartypants)) {
                $content = $this->smartypants->transform($content);
            }

            $result['content'] = trim($content);
        }

        return $result;
    }

    public function move($source, $target)
    {
        $this->data_filesystem->createDir(dirname($target . '.md'));

        $this->data_filesystem->rename($source . '.md', $target . '.md');
    }
}
