<?php namespace Wright\Extensions\Twig;

/**
 * @todo add docblocks
 */
class StandardExtension extends \Twig_Extension
{
    protected $base;

    public function __construct($base = '/')
    {
        /**
         * @todo validate that $base is a string
         */

        $this->base = $base;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('url', array($this, 'urlFunction')),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('content', array($this, 'contentFilter'), array('is_safe' => array('html'))),
        );
    }

    public function urlFunction($path = '')
    {
        /**
         * @todo validate that $path is a string
         */

        if (substr($path, 0, 7) != 'http://' && substr($path, 0, 8) != 'https://' && substr($path, 0, 1) != '/') {

            $path = (isset($this->base) ? $this->base : '/') . $path;
        }

        return $path;
    }

    public function contentFilter($item)
    {
        return $item;
    }

    public function getName()
    {
        return 'wright_standard_extension';
    }
}
