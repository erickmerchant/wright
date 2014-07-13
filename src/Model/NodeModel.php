<?php namespace Wright\Model;

use Aura\Sql\ExtendedPdoInterface;

/**
 * @todo add docblocks
 */
class NodeModel
{
    protected $connection;

    protected $node_id;

    protected $parent_node_id;

    protected $fields = [];

    protected $published_on;

    public function __construct($node_id, ExtendedPdoInterface $connection)
    {
        $this->node_id = $node_id;

        $this->connection = $connection;

        $row = $this->connection->fetchOne("SELECT parent_node_id, published_on, fields FROM nodes WHERE node_id = :node_id", ['node_id' => $node_id]);

        if ($row) {

            $this->parent_node_id = $row['parent_node_id'];

            $this->published_on = $row['published_on'];

            $fields = json_decode($row['fields'], true);

            $this->fields = array_merge($this->fields, (array) $fields);

            $this->fields['published_on'] = date_create_from_format('Y-m-d', $row['published_on']);
        }

        $query = $this->connection->fetchAll("SELECT * FROM pages WHERE node_id = :node_id", ['node_id' => $node_id]);

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

    public function collection()
    {
        $query = $this->connection->fetchAll("SELECT node_id FROM nodes WHERE parent_node_id = :parent_node_id ORDER BY published_on DESC, slug ASC", ['parent_node_id' => $this->node_id]);

        $collection = [];

        if ($query) {

            foreach ($query as $row) {

                $collection[] = new self($row['node_id'], $this->connection);
            }
        }

        return $collection;
    }

    public function next()
    {
        $row = $this->connection->fetchOne("SELECT node_id FROM nodes WHERE (published_on > :published_on OR (published_on = :published_on AND node_id > :node_id)) AND parent_node_id = :parent_node_id ORDER BY published_on, node_id LIMIT 1", ['parent_node_id' => $this->parent_node_id, 'published_on' => $this->published_on, 'node_id' => $this->node_id]);

        if ($row) {
            return new self($row['node_id'], $this->connection);
        }
    }

    public function previous()
    {
        $row = $this->connection->fetchOne("SELECT node_id FROM nodes WHERE (published_on < :published_on OR (published_on = :published_on AND node_id < :node_id)) AND parent_node_id = :parent_node_id ORDER BY published_on DESC, node_id DESC LIMIT 1", ['parent_node_id' => $this->parent_node_id, 'published_on' => $this->published_on, 'node_id' => $this->node_id]);

        if ($row) {
            return new self($row['node_id'], $this->connection);
        }
    }

    public function parent()
    {
        return new self($this->parent_node_id, $this->connection);
    }

    public function related($path)
    {
        $collection = [];

        $path .= '/';

        $query = $this->connection->fetchAll("SELECT nodes.node_id FROM relationships LEFT JOIN nodes ON relationships.related_node_id = nodes.node_id WHERE relationships.node_id = :node_id AND nodes.path LIKE :path ORDER BY published_on DESC, slug ASC", ['node_id' => $this->node_id, 'path' => $path . '%']);

        if ($query) {

            foreach ($query as $row) {

                $collection[] = new self($row['node_id'], $this->connection);
            }
        }

        $query = $this->connection->fetchAll("SELECT nodes.node_id FROM relationships LEFT JOIN nodes ON relationships.node_id = nodes.node_id WHERE relationships.related_node_id = :node_id AND nodes.path LIKE :path ORDER BY published_on DESC, slug ASC", ['node_id' => $this->node_id, 'path' => $path . '%']);

        if ($query) {

            foreach ($query as $row) {

                $collection[] = new self($row['node_id'], $this->connection);
            }
        }

        return $collection;
    }

    public function __set($key, $value)
    {
        $this->fields[$key] = $value;
    }

    public function __get($key)
    {
        if (isset($this->fields[$key])) {
            return $this->fields[$key];
        }
    }

    public function __isset($key)
    {
        return isset($this->fields[$key]);
    }
}
