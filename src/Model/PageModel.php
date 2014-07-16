<?php namespace Wright\Model;

use Aura\Sql\ExtendedPdoInterface;

/**
 * @todo add docblocks
 */
class PageModel extends NodeModel
{
    public function __construct($page_id, ExtendedPdoInterface $connection)
    {
        $this->connection = $connection;

        $page = $connection->fetchOne("SELECT * FROM pages WHERE page_id = :page_id", ['page_id' => $page_id]);

        if ($page) {

            $this->fields = $page;

            if ($page['node_id']) {

                parent::__construct($page['node_id'], $connection);
            }
        }
    }
}
