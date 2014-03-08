<?php namespace Wright\Settings;

use Symfony\Component\Yaml\Yaml;
use League\Flysystem\FilesystemInterface;

class YamlSettings implements SettingsInterface
{
    /**
     * The yaml object for read and writing front matter.
     *
     * @var Yaml
     */
    protected $yaml;

    protected $settings_filesystem;

    /**
     * @param FileManagerInterface $filemanager The file manager for read, writing files, etc.
     */
    public function __construct(FilesystemInterface $settings_filesystem, Yaml $yaml)
    {
        $this->yaml = $yaml;

        $this->settings_filesystem = $settings_filesystem;
    }

    /**
     * Writes a settings file.
     *
     * @param  string  $file     The file name
     * @param  array   $settings Settings to write to the file.
     * @param  boolean $force    If the file exists, should it be overwritten.
     * @return void
     */
    public function write($file, $settings, $force = true)
    {
        /**
         * @todo validate that $file is a string
         * @todo validate that $settings is a string
         * @todo validate that $force is a boolean
         */

        $settings = json_decode(json_encode($settings), true);

        $settings = $this->yaml->dump($settings);

        $this->settings_filesystem->put($file . '.yml', $settings);
    }

    /**
     * Reads a settings file.
     *
     * @param  string $file A file to read.
     * @return array  The settings read.
     */
    public function read($file)
    {
        /**
         * @todo validate that $file is a string
         */

        $result = $this->yaml->parse($this->settings_filesystem->read($file . '.yml'));

        return $result;
    }
}
