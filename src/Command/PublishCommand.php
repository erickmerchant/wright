<?php namespace Wright\Command;

use Wright\View\ViewInterface;
use Wright\Model\SchemaInterface;
use Wright\Hooks\HooksManagerInterface;
use Wright\Settings\SettingsInterface;
use Wright\Middleware\MiddlewareManagerInterface;
use Wright\Model\SiteModel;
use Aura\Cli\Status;
use Aura\Cli\Stdio;
use League\Flysystem\FilesystemInterface;

class PublishCommand implements CommandInterface
{
    protected $source_filesystem;

    protected $site_filesystem;

    protected $view;

    protected $schema;

    protected $hooks;

    protected $middleware;

    protected $settings;

    public function __construct(HooksManagerInterface $hooks, FilesystemInterface $source_filesystem, FilesystemInterface $site_filesystem, SchemaInterface $schema, MiddlewareManagerInterface $middleware, SettingsInterface $settings, ViewInterface $view)
    {
        $this->source_filesystem = $source_filesystem;

        $this->site_filesystem = $site_filesystem;

        $this->view = $view;

        $this->schema = $schema;

        $this->hooks = $hooks;

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
        $this->hooks->call('before.publish');

        foreach ($this->source_filesystem->listContents('/base/', true) as $file) {

            $path = substr(trim($file['path'], '/'), 5);

            if ($file['type'] == 'file') {

                if (!$this->site_filesystem->has(dirname($path))) {

                    $this->site_filesystem->createDir(dirname($path));
                }

                $this->site_filesystem->put($path, $this->source_filesystem->read($file['path']));
            }
        }

        $defaults = $this->settings->read('defaults');

        $site = new SiteModel($this->schema->getConnection());

        if ($site) {

            foreach ($site->pages() as $page_model) {

                if (substr($page_model->permalink, -1) == '/') {
                    $page_model->permalink .= 'index.html';
                }

                if ($page_model->middleware) {

                    $page_model->middleware = json_decode($page_model->middleware);

                    foreach ($page_model->middleware as $middleware) {

                        $page_model = $this->middleware->call($middleware, $page_model);
                    }
                }

                $response = $this->view->render($page_model->template, $defaults + ['page' => $page_model]);

                $this->site_filesystem->put($page_model->permalink, $response);
            }
        }

        $stdio->outln('<<magenta>>Site published on ' . (new \DateTime)->format('Y-m-d') . '<<reset>>');

        $this->hooks->call('after.publish');

        return Status::SUCCESS;
    }
}
