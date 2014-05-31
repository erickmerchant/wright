<?php namespace Wright\Model;

use Aura\Sql\ExtendedPdoInterface;

/**
 * @todo add docblocks
 */
class SiteModel
{
    protected $connection;

    public function __construct(ExtendedPdoInterface $connection)
    {
        $this->connection = $connection;
    }

    public function pages()
    {
        $result = [];

        $page_ids = $this->connection->fetchCol('SELECT page_id FROM pages');

        foreach($page_ids as $page_id)
        {
            $result[] = new PageModel($page_id, $this->connection);
        }

        return $result;
    }
}
