<?php namespace Wright\Extensions\Twig;

use Imagine\Image;
use Imagine\Image\ImagineInterface;

/**
 * @todo add docblocks
 */
class ThumbnailExtension extends \Twig_Extension
{
    protected $base;

    protected $imagine;

    protected $data_directory;

    protected $site_directory;

    public function __construct(ImagineInterface $imagine, $data_directory, $site_directory, $base = '/')
    {
        /**
         * @todo validate that $base is a string
         */

        $this->imagine = $imagine;

        $this->data_directory = $data_directory;

        $this->site_directory = $site_directory;

        $this->base = $base;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('thumbnail', array($this, 'thumbnailFunction')),
        );
    }

    public function thumbnailFunction($path, $width = 0, $height = 0)
    {
        /**
         * @todo validate that $path is a string
         */

        if (!$width && !$height) {

            $target_path = $path;

        } else {

            $target_path = dirname($path) . '/' . $width . 'x' . $height . '/' . basename($path);
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

        return $this->base . $target_path;
    }

    public function getName()
    {
        return 'wright_thumbnail_extension';
    }
}
