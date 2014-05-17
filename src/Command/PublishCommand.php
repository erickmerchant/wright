<?php namespace Wright\Command;

use Wright\View\ViewInterface;
use Wright\Model\Schema;
use Wright\Generators\GeneratorCollection;
use Wright\Settings\SettingsInterface;
use Wright\Middleware\MiddlewareManager;
use Wright\Model\SiteModel;
use Wright\Model\PageModel;
use Aura\Cli\Status;
use Aura\Cli\Stdio;
use League\Flysystem\FilesystemInterface;

class PublishCommand implements CommandInterface
{
    protected $base_filesystem;

    protected $site_filesystem;

    protected $view;

    protected $schema;

    protected $middleware;

    protected $settings;

    public function __construct(FilesystemInterface $base_filesystem, FilesystemInterface $site_filesystem, Schema $schema, GeneratorCollection $generators, MiddlewareManager $middleware, SettingsInterface $settings, ViewInterface $view)
    {
        $this->base_filesystem = $base_filesystem;

        $this->site_filesystem = $site_filesystem;

        $this->view = $view;

        $this->schema = $schema;

        $this->generators = $generators;

        $this->middleware = $middleware;

        $this->settings = $settings;
    }

    public function getDescription()
    {
        return 'publish your site';
    }

    public function getOptions()
    {
        return [];
    }

    public function getArguments()
    {
        return [];
    }

    public function execute(Stdio $stdio, array $params = [])
    {
        $this->generators->run();

        $this->schema->setup();

        foreach ($this->base_filesystem->listContents('/', true) as $file) {

            if ($file['type'] == 'file') {

                if (!$this->site_filesystem->has(dirname($file['path']))) {

                    $this->site_filesystem->createDir(dirname($file['path']));
                }

                $this->site_filesystem->put($file['path'], $this->base_filesystem->read($file['path']));
            }
        }

        $defaults = $this->settings->read('defaults');

        $pages = new SiteModel($this->schema);

        if ($pages) {

            foreach ($pages as $page) {

                if (substr($page['permalink'], -1) == '/') {
                    $page['permalink'] .= 'index.html';
                }

                $page_model = new PageModel($page['page_id'], $this->schema);

                if($page['middleware']) {

                    $page['middleware'] = json_decode($page['middleware']);

                    foreach($page['middleware'] as $middleware) {

                        $page_model = $this->middleware->call($middleware, $page_model);
                    }
                }

                $response = $this->view->render($page['template'], $defaults + ['page' => $page_model]);

                $this->site_filesystem->put($page['permalink'], $response);
            }
        }

        $stdio->outln('<<magenta>>Site published on ' . (new \DateTime)->format('Y-m-d') . '<<reset>>');

        return Status::SUCCESS;
    }
}
