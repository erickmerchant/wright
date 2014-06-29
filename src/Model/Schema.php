<?php namespace Wright\Model;

use Wright\Data\DataInterface;
use Wright\Settings\SettingsInterface;
use Aura\Sql\ExtendedPdoInterface;

class Schema implements SchemaInterface
{
    protected $connection;

    protected $data;

    protected $settings;

    protected $is_setup = false;

    public function __construct(ExtendedPdoInterface $connection, DataInterface $data, SettingsInterface $settings)
    {
        $this->connection = $connection;

        $this->data = $data;

        $this->settings = $settings;
    }

    protected function setup()
    {
        $this->setupTables();

        $this->setupNodes();

        $this->setupPages();

        $this->is_setup = true;
    }

    public function getConnection()
    {
        if (!$this->is_setup) {
            $this->setup();
        }

        return $this->connection;
    }

    protected function setupTables()
    {
        $this->connection->query(
            'CREATE TABLE IF NOT EXISTS
                nodes (
                    node_id INTEGER PRIMARY KEY AUTOINCREMENT,
                    parent_node_id INTEGER,
                    path TEXT UNIQUE,
                    slug TEXT,
                    published_on TEXT,
                    fields TEXT
                )'
        );

        $this->connection->query(
            'CREATE TABLE IF NOT EXISTS
                relationships (
                    node_id INTEGER,
                    related_node_id INTEGER
                )'
        );

        $this->connection->query('CREATE UNIQUE INDEX relationships_uk_1 ON relationships(node_id, related_node_id);');

        $this->connection->query(
            'CREATE TABLE IF NOT EXISTS
                pages (
                    page_id INTEGER PRIMARY KEY AUTOINCREMENT,
                    permalink TEXT UNIQUE,
                    type TEXT,
                    node_id INTEGER,
                    template TEXT,
                    middleware TEXT
                )'
        );
    }

    protected function setupNodes()
    {
        $insert_node_fn = function ($directory, $file) {

            $path = $file;

            if ($directory && $directory != '.') {

                $path = $directory . '/' . $file;
            }

            $node_query = $this->connection->fetchOne("SELECT node_id FROM nodes WHERE path = :path LIMIT 1", ['path' => $path]);

            if ($node_query) {

                $result = $node_query['node_id'];

            } else {

                $match = preg_match('/^(.*?\/)?([0-9]{4}-[0-9]{2}-[0-9]{2}\.)([^\/]*)$/', $file, $matches);

                $published_on = $match ? new \DateTime(trim($matches[2], '.')) : new \DateTime();

                $matches = [];

                preg_match('/^(.*?\/)?([0-9]{4}-[0-9]{2}-[0-9]{2}\.|[0-9]+\.|)([^\/]*)$/', $file, $matches);

                $slug = trim($matches[3], '/');

                $parent_node_id = 0;

                if ($directory) {

                    $parent_node_query = $this->connection->fetchOne("SELECT node_id FROM nodes WHERE path = :path LIMIT 1", ['path' => $directory]);

                    if ($parent_node_query) {

                        $parent_node_id = $parent_node_query['node_id'];

                    } else {

                        $this->connection->perform("INSERT INTO nodes (parent_node_id, path, slug, published_on) VALUES (:parent_node_id, :path, :slug, :published_on)", [
                            'parent_node_id' => 0,
                            'path' => $directory,
                            'slug' => '',
                            'published_on' => $published_on->format('Y-m-d')
                        ]);

                        $parent_node_id = $this->connection->lastInsertId();
                    }
                }

                $this->connection->perform("INSERT INTO nodes (parent_node_id, path, slug, published_on) VALUES (:parent_node_id, :path, :slug, :published_on)", [
                    'parent_node_id' => $parent_node_id,
                    'path' => $path,
                    'slug' => $slug,
                    'published_on' => $published_on->format('Y-m-d')
                ]);

                $result = $this->connection->lastInsertId();
            }

            return $result;
        };

        foreach ($this->data as $path) {

            $directory = trim(dirname($path), '/');

            $file = pathinfo($path, PATHINFO_FILENAME);

            $fields = $this->data->read($path);

            $json_fields = json_encode($fields);

            $node_id = $insert_node_fn($directory, $file);

            $this->connection->perform("UPDATE nodes SET fields = :fields WHERE node_id = :node_id", [
                'fields' => $json_fields,
                'node_id' => $node_id
            ]);

            if (isset($fields['related'])) {

                foreach ($fields['related'] as $related) {

                    $directory = trim(substr($related, 0, strrpos($related, '/')), '/');

                    $file = trim(substr($related, strrpos($related, '/')+1), '/');

                    $related_node_id = $insert_node_fn($directory, $file);

                    $this->connection->perform("INSERT INTO relationships (node_id, related_node_id) VALUES (:node_id, :related_node_id)", [
                        'node_id' => $node_id,
                        'related_node_id' => $related_node_id
                    ]);
                }
            }
        }
    }

    protected function setupPages()
    {
        $sitemap_settings = $this->settings->read('sitemap');

        $old_permalinks_settings = $this->settings->read('old-permalinks');

        if (!is_array($old_permalinks_settings)) {

            $old_permalinks_settings = [];
        }

        $insert_page_fn = function ($permalink_pattern, $settings, $fields, $node_id = 0) use ($old_permalinks_settings) {

            if (substr($permalink_pattern, -1) == '/' || substr($permalink_pattern, -4) == 'html') {

                $type = 'html';

            } elseif (isset($settings['type'])) {

                $type = $settings['type'];
            }

            $permalink = $this->makePermalink($permalink_pattern, $fields);

            $settings['middleware'] = isset($settings['middleware']) ? json_encode($settings['middleware']) : '';

            $this->connection->perform("INSERT INTO pages (permalink, type, node_id, template, middleware) VALUES (:permalink, :type, :node_id, :template, :middleware)", [
                'permalink' => $permalink,
                'type' => $type,
                'node_id' => $node_id,
                'template' => $settings['template'],
                'middleware' => $settings['middleware']
            ]);

            foreach ($old_permalinks_settings as $old_url => $new_url) {

                $old_url = ltrim($old_url, '/');

                $new_url = ltrim($new_url, '/');

                if ($new_url == $permalink) {

                    $this->connection->perform("INSERT INTO pages (permalink, type, node_id, template, middleware) VALUES (:permalink, :type, :node_id, :template, :middleware)", [
                        'permalink' => $old_url,
                        'type' => 'old',
                        'node_id' => $node_id,
                        'template' => $settings['template'],
                        'middleware' => $settings['middleware']
                    ]);
                }
            }
        };

        foreach ($sitemap_settings as $permalink_pattern => $settings) {

            if (!isset($settings['template'])) {

                throw new SiteMapException('Exception thrown while parsing sitemap. No template found for ' . $permalink_pattern);
            }

            $type = null;

            if (isset($settings['data'])) {

                $is_glob = substr($settings['data'], -2) == '/*';

                if ($is_glob) {

                    $settings['data'] = substr($settings['data'], 0, strlen($settings['data']) - 2);

                    $nodes = $this->connection->query("SELECT child_nodes.node_id, child_nodes.slug, child_nodes.published_on, child_nodes.fields FROM nodes as child_nodes LEFT JOIN nodes as parent_nodes ON child_nodes.parent_node_id = parent_nodes.node_id WHERE parent_nodes.path = '$settings[data]'");

                } else {

                    $nodes = $nodes = $this->connection->query("SELECT node_id, slug, published_on, fields FROM nodes WHERE path = '$settings[data]'");
                }

                foreach ($nodes as $node) {

                    $fields = $node['fields'] ? json_decode($node['fields'], true) : [];

                    $fields['slug'] = $node['slug'];

                    list($year, $month, $day) = explode('-', $node['published_on']);

                    $fields['year'] = $year;

                    $fields['month'] = $month;

                    $fields['day'] = $day;

                    $insert_page_fn($permalink_pattern, $settings, $fields, $node['node_id']);
                }

            } else {

                $insert_page_fn($permalink_pattern, $settings, $fields);
            }
        }
    }

    protected function makePermalink($pattern, array $data = [])
    {
        $permalink = preg_replace_callback('/\:(\w+)/', function ($matches) use ($data) {

            if (isset($data[$matches[1]])) {
                return $data[$matches[1]];
            }

            throw new SiteMapException('Exception thrown while generating permalink. No value found for ' . $matches[1]);

        }, $pattern);

        if ($permalink != '/') {

            $permalink = ltrim($permalink, '/');
        }

        return $permalink;
    }
}
