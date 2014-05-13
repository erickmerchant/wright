<?php namespace Wright\Converter;

use Michelf\MarkdownInterface;
use Michelf\SmartyPants;

class MarkdownConverter implements ConverterInterface
{
    /**
     * The markdown object for read markdown
     *
     * @var MarkdownExtra
     */
    protected $markdown;

    /**
     * SmartyPants for smart quotes and such.
     *
     * @var SmartyPants
     */
    protected $smartypants;

    public function __construct(MarkdownInterface $markdown, SmartyPants $smartypants = null)
    {
        $this->markdown = $markdown;

        $this->smartypants = $smartypants;
    }

    public function convert($content)
    {
        $content = $this->markdown->transform($content);

        if (!is_null($this->smartypants)) {
            $content = $this->smartypants->transform($content);
        }

        $content = trim($content);

        return $content;
    }
}
