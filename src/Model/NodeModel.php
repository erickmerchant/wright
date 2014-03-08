<?php namespace Wright\Model;

/**
 * @todo add docblocks
 */
class NodeModel implements \IteratorAggregate
{
    protected $schema;

    protected $node_id;

    protected $parent_node_id;

    protected $fields;

    protected $published_on;

    public function __construct($node_id, Schema $schema)
    {
        $this->node_id = $node_id;

        $this->schema = $schema;

        $row = $this->schema->row("SELECT parent_node_id, published_on, fields FROM nodes WHERE node_id = " . $node_id);

        if ($row) {

            $this->parent_node_id = $row['parent_node_id'];

            $this->published_on = $row['published_on'];

            $this->fields = json_decode($row['fields'], true);

            $this->fields['published_on'] = date_create_from_format('Y-m-d', $row['published_on']);
        }

        $query = $this->schema->query("SELECT * FROM pages WHERE node_id = " . $node_id);

        if ($query) {

            foreach ($query as $row) {

                if ($row['type'] == 'html') {

                    $this->fields['url'] = $row['permalink'];

                } else {

                    $this->fields[$row['type'] . '_url'] = $row['permalink'];
                }
            }
        }
    }

    public function getIterator()
    {
        return $this->collection();
    }

    public function collection()
    {
        $query = $this->schema->query("SELECT node_id FROM nodes WHERE parent_node_id = " . $this->node_id . " ORDER BY published_on DESC, slug ASC");

        $collection = [];

        if ($query) {

            foreach ($query as $row) {

                $collection[] = new self($row['node_id'], $this->schema);
            }
        }

        return $collection;
    }

    public function next()
    {
        $row = $this->schema->row("SELECT node_id FROM nodes WHERE published_on > '" . $this->published_on . "' AND parent_node_id = " . $this->parent_node_id . " ORDER BY published_on LIMIT 1");

        if ($row) {
            return new self($row['node_id'], $this->schema);
        }
    }

    public function previous()
    {
        $row = $this->schema->row("SELECT node_id FROM nodes WHERE published_on < '" . $this->published_on . "' AND parent_node_id = " . $this->parent_node_id . " ORDER BY published_on DESC LIMIT 1");

        if ($row) {
            return new self($row['node_id'], $this->schema);
        }
    }

    public function parent()
    {
        return new self($this->parent_node_id, $this->schema);
    }

    public function related($path)
    {
        $collection = [];

        $path .= '/';

        $query = $this->schema->query("SELECT nodes.node_id FROM relationships LEFT JOIN nodes ON relationships.related_node_id = nodes.node_id WHERE relationships.node_id = '" . $this->node_id . "' AND nodes.path LIKE '" . $path . "%' ORDER BY published_on DESC, slug ASC");

        if ($query) {

            foreach ($query as $row) {

                $collection[] = new self($row['node_id'], $this->schema);
            }
        }

        $query = $this->schema->query("SELECT nodes.node_id FROM relationships LEFT JOIN nodes ON relationships.node_id = nodes.node_id WHERE relationships.related_node_id = '" . $this->node_id . "' AND nodes.path LIKE '" . $path . "%' ORDER BY published_on DESC, slug ASC");

        if ($query) {

            foreach ($query as $row) {

                $collection[] = new self($row['node_id'], $this->schema);
            }
        }

        return $collection;
    }

    public function __get($key)
    {
        if (isset($this->fields[$key])) {
            return $this->fields[$key];
        }
    }

    public function __isset($key)
    {
        if (isset($this->fields[$key])) {
            return true;
        }

        return false;
    }
}
