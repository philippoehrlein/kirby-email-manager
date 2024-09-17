<?php
namespace KirbyEmailManager\PageMethods;

use Kirby\Cms\Content;
use Kirby\Cms\Field;

class ContentWrapper extends Content
{
    private $blockContent;
    private $page;

    public function __construct($page, $blockContent = null)
    {
        $this->page = $page;
        parent::__construct($page->content()->toArray());
        $this->blockContent = $blockContent;
    }

    public function __call(string $name, array $arguments = []): Field
    {
        if ($this->blockContent && isset($this->blockContent[$name])) {
            return new Field($this->page, $name, $this->blockContent[$name]);
        }
        return parent::__call($name, $arguments);
    }
}