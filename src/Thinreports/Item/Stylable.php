<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Item;

trait Stylable {
    protected $style;

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
}
