<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\tag\factory;

use pvc\html\tag\abstract\Tag;
use pvc\html\tag\abstract\TagVoid;

/**
 * Class TagFactory
 * this class's sole purpose in life is to produce tags and void tags with legitimate names.
 */
class TagFactory
{

    protected AbstractTagFactory $abstractTagFactory;

    public function __construct(AbstractTagFactory $abstractTagFactory)
    {
        $this->abstractTagFactory = $abstractTagFactory;
    }

    /**
     * makeA
     * @return Tag
     */
    public function makeA(): Tag
    {
        $tagName = 'a';
        return $this->abstractTagFactory->makeTag('a');
    }

    /**
     * makeBase
     * @return TagVoid
     */
    public function makeBase(): TagVoid
    {
        return $this->abstractTagFactory->makeTagVoid('base');
    }

    /**
     * makeBody
     * @return Tag
     */
    public function makeBody(): Tag
    {
        return $this->abstractTagFactory->makeTag('body');
    }

    /**
     * makeDiv
     * @return Tag
     */
    public function makeDiv(): Tag
    {
        return $this->abstractTagFactory->makeTag('div');
    }

    /**
     * makeFooter
     * @return Tag
     */
    public function makeFooter(): Tag
    {
        return $this->abstractTagFactory->makeTag('footer');
    }

    /**
     * makeForm
     * @return Tag
     */
    public function makeForm(): Tag
    {
        return $this->abstractTagFactory->makeTag('form');
    }

    /**
     * makeHead
     * @return Tag
     */
    public function makeHead(): Tag
    {
        return $this->abstractTagFactory->makeTag('head');
    }

    /**
     * makeHtml
     * @return Tag
     */
    public function makeHtml(): Tag
    {
        return $this->abstractTagFactory->makeTag('html');
    }

    /**
     * makeImg
     * @return TagVoid
     */
    public function makeImg(): TagVoid
    {
        return $this->abstractTagFactory->makeTagVoid('img');
    }

    /**
     * makeLink
     * @return TagVoid
     */
    public function makeLink(): TagVoid
    {
        return $this->abstractTagFactory->makeTagVoid('link');
    }

    /**
     * makeSpan
     * @return Tag
     */
    public function makeSpan(): Tag
    {
        return $this->abstractTagFactory->makeTag('span');
    }

    /**
     * makeStyle
     * @return Tag
     */
    public function makeStyle(): Tag
    {
        return $this->abstractTagFactory->makeTag('style');
    }

    /**
     * makeTitle
     * @return Tag
     */
    public function makeTitle(): Tag
    {
        return $this->abstractTagFactory->makeTag('title');
    }
}
