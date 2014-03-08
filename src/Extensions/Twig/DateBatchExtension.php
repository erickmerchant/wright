<?php namespace Wright\Extensions\Twig;

/**
 * @todo add docblocks
 */
class DateBatchExtension extends \Twig_Extension
{
    protected $url_settings;

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('date_batch', array($this, 'dateBatchFilter')),
        );
    }

    public function dateBatchFilter($items, $prop, $format = 'Y-m')
    {
        /**
         * @todo validate that $items are an array or Traversable
         * @todo validate that $prop is a string
         * @todo validate that $format is a string
         */

        $result = [];

        foreach ($items as $item) {

            if (is_array($item)) {
                $date = $item[$prop];
            } elseif (is_object($item)) {
                $date = $item->{$prop};
            } else {
                continue;
            }

            if ($date instanceof \DateTime) {

                $key = $date->format($format);

                $result[$key][] = $item;
            }
        }

        return $result;
    }

    public function getName()
    {
        return 'wright_date_batch_extension';
    }
}
