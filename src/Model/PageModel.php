<?php namespace Wright\Model;

use Aura\Sql\ExtendedPdoInterface;

/**
 * @todo add docblocks
 */
class PageModel extends NodeModel
{
    public function __construct($page_id, ExtendedPdoInterface $connection)
    {
        $page = $connection->fetchOne("SELECT * FROM pages WHERE page_id = :page_id", ['page_id' => $page_id]);

        if ($page) {

            $this->fields = $page;

            parent::__construct($page['node_id'], $connection);

            if ($page['type'] == 'old') {

                $new_page = $connection->fetchOne("SELECT * FROM pages WHERE node_id = :node_id AND type = 'html'", ['node_id' => $page['node_id']]);

                $this->fields['new_url'] = $new_page['permalink'];
            }
        }
    }
}
