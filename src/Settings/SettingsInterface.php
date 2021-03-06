<?php namespace Wright\Settings;

interface SettingsInterface
{

    /**
     * Reads a settings file.
     *
     * @param  string $file A file to read.
     * @return array  The settings read.
     */
    public function read($file);

    /**
     * Writes a settings file.
     *
     * @param  string $file     The file name
     * @param  array  $settings settings to write to the file.
     * @return void
     */
    public function write($file, $settings);
}
