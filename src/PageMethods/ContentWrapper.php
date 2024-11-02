<?php
namespace KirbyEmailManager\PageMethods;

use Kirby\Content\Content;
use Kirby\Content\Field;

/**
 * ContentWrapper class for managing content
 * 
 * This class provides methods to wrap content and manage it.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class ContentWrapper extends Content
{
    private $blockContent;
    private $page;

    /**
     * Constructs a new ContentWrapper instance.
     * 
     * @param Page $page The page instance.
     * @param array|null $blockContent The block content array.
     */
    public function __construct($page, $blockContent = null)
    {
        $this->page = $page;
        parent::__construct($page->content()->toArray());
        $this->blockContent = $blockContent;
    }

    /**
     * Calls a method on the content.
     * 
     * @param string $name The name of the method.
     * @param array $arguments The arguments for the method.
     * @return Field The field instance.
     */
    public function __call(string $name, array $arguments = []): Field
    {
        if ($this->blockContent && isset($this->blockContent[$name])) {
            return new Field($this->page->toPage(), $name, $this->blockContent[$name]);
        }
        return parent::__call($name, $arguments);
    }
}