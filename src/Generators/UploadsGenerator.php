<?php namespace Wright\Generators;

use Imagine\Image;
use Imagine\Image\ImagineInterface;
use Wright\Settings\SettingsInterface;

class UploadsGenerator
{
    /**
     * Imagine for working with images
     *
     * @var ImagineInterface
     */
    protected $imagine;

    protected $settings;

    protected $data_directory;

    protected $site_directory;

    /**
     * @param ImagineInterface     $imagine     Imagine for working with images
     */
    public function __construct(ImagineInterface $imagine, SettingsInterface $settings, $data_directory, $site_directory)
    {
        $this->imagine = $imagine;

        $this->settings = $settings;

        $this->data_directory = $data_directory;

        $this->site_directory = $site_directory;
    }

    protected function thumbnail($path, $name, $width = 0, $height = 0)
    {
        /**
         * @todo validate that $path is a string
         */

        if (!$width && !$height) {

            $target_path = $path;

        } else {

            $target_path = dirname($path) . '/' . $name . '/' . basename($path);
        }

        $source_image = $this->imagine->open($this->data_directory . $path);

        if ($width || $height) {

            $size = $source_image->getSize();

            if (!$width) {

                $width = $size->getWidth();
            }

            if (!$height) {

                $height = $size->getHeight();
            }

            $box = new Image\Box($width, $height);

            $source_image = $source_image->thumbnail($box, Image\ImageInterface::THUMBNAIL_INSET);
        }

        if (!file_exists(dirname($this->site_directory . $target_path))) {

            mkdir(dirname($this->site_directory . $target_path), 0775, true);
        }

        $source_image->save($this->site_directory . $target_path);
    }

    /**
     * Run this controller. Generate some images.
     *
     * @return void
     */
    public function __invoke()
    {
        $upload_settings = $this->settings->read('uploads');

        if (!file_exists($this->site_directory . 'uploads/')) {

            mkdir($this->site_directory . 'uploads/', 0775, true);
        }

        foreach (new \DirectoryIterator($this->data_directory . 'uploads/') as $file) {

            if (in_array($file->getExtension(), ['jpg', 'jpeg', 'gif', 'png'])) {

                $source_image = $this->imagine->open($this->data_directory . 'uploads/' . $file);

                $source_image->save($this->site_directory . 'uploads/' . $file);

                if (is_array($upload_settings)) {

                    foreach ($upload_settings as $name => $settings) {

                        list($width, $height) = $settings;

                        $this->thumbnail('uploads/' . $file, $name, $width, $height);
                    }
                }
            }
        }
    }
}
