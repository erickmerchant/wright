<?php namespace Wright\Data;

interface DataInterface extends \Traversable
{
    public function write($file, $data);

    public function read($file);

    public function move($source, $target);
}
