<?php namespace Wright\View;

/**
 * @todo add docblocks
 */
interface ViewInterface
{
    public function render($template, array $data = []);
}
