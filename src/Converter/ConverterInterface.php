<?php namespace Wright\Converter;

interface ConverterInterface
{
    public function convert($content);

    public function getExt();
}
