<?php namespace Wright\View;

use Twig_Environment;

/**
 * @todo add docblocks
 */
class TwigView implements ViewInterface
{
    protected $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render($template, array $data = [])
    {
        /**
         * @todo validate that $template is a string
         */

        $template = $this->twig->loadTemplate($template . '.twig');

        return $template->render($data);
    }
}
