<?php namespace Wright\Converter;

use Parsedown;

class ParsedownConverter implements ConverterInterface
{
    protected $parsedown;

    public function __construct(Parsedown $parsedown)
    {
        $this->parsedown = $parsedown;
    }

    public function convert($content)
    {
        $content = $this->parsedown->text($content);

        $content = trim($content);

        return $content;
    }
}
