<?php namespace Wright\Model;

/**
 * @todo add docblocks
 */
class SiteModel implements \IteratorAggregate
{
    protected $schema;

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    public function getIterator()
    {
        return $this->schema->query('SELECT * FROM pages');
    }
}
