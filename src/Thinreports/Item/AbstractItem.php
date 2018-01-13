<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Item;

use Thinreports\Page\Page;

abstract class AbstractItem
{
    protected $parent;
    protected $schema;

    protected $is_visible;
    protected $style;

    /**
     * @param Page $parent
     * @param array $schema
     */
    public function __construct(Page $parent, array $schema)
    {
        $this->parent = $parent;
        $this->schema = $schema;
        $this->is_visible = $schema['display'] === true;
    }

    /**
     * @param boolean $visible
     * @return $this
     */
    public function setVisible($visible)
    {
        $this->is_visible = $visible;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->is_visible;
    }

    /**
     * @return $this
     */
    public function hide()
    {
        $this->setVisible(false);
        return $this;
    }

    /**
     * @return $this
     */
    public function show()
    {
        $this->setVisible(true);
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->schema['id'];
    }

    /**
     * @param string $name
     * @param mixed $style
     * @return $this
     * @throws Thinreports\Exception\StandardException
     * @throws Thinreports\Exception\UnavailableStyleValue
     */
    public function setStyle($name, $style)
    {
        $this->style->set($name, $style);
        return $this;
    }

    /**
     * @param array $styles
     * @return $this
     * @throws Thinreports\Exception\StandardException
     * @throws Thinreports\Exception\UnavailableStyleValue
     */
    public function setStyles(array $styles)
    {
        foreach ($styles as $name => $style) {
            $this->setStyle($name, $style);
        }
        return $this;
    }

    /**
     * @return mixed
     * @throws Thinreports\Exception\StandardException
     */
    public function getStyle($name)
    {
        return $this->style->get($name);
    }

    /**
     * @access private
     *
     * @return array
     */
    public function exportStyles()
    {
        return $this->style->export();
    }

    public function __clone()
    {
        $this->style = clone $this->style;
    }

    /**
     * @access private
     *
     * @return Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @access private
     *
     * @return array
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @access private
     *
     * @param string $type_name
     * @return boolean
     */
    public function isTypeOf($type_name)
    {
        return $this->schema['type'] === $type_name;
    }

    /**
     * @access private
     *
     * @return array
     */
    abstract public function getBounds();
}
