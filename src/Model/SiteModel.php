<?php namespace Wright\Model;

/**
 * @todo add docblocks
 */
class SiteModel
{
    protected $schema;

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    public function pages()
    {
        return $this->schema->query('SELECT * FROM pages');
    }
}
