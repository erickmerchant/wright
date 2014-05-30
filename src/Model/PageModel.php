<?php namespace Wright\Model;

/**
 * @todo add docblocks
 */
class PageModel extends NodeModel
{
    public function __construct($page_id, Schema $schema)
    {
        $page = $schema->row("SELECT * FROM pages WHERE page_id = " . $page_id);

        if ($page) {

            parent::__construct($page['node_id'], $schema);

            if ($page['type'] == 'old') {

                $new_page = $schema->row("SELECT * FROM pages WHERE node_id = " . $page['node_id'] . " AND type = 'html'");

                $this->fields['new_url'] = $new_page['permalink'];
            }
        }
    }
}
