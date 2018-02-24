<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Item\Style;

/**
 * @access private
 */
class GraphicStyle extends BasicStyle
{
    static protected $available_style_names = array(
        'border_color',
        'border_width',
        'border',
        'fill_color'
    );

    /**
     * @param float|integer $width
     */
    public function set_border_width($width)
    {
        $this->styles['border-width'] = $width;
    }

    /**
     * @return float|integer
     */
    public function get_border_width()
    {
        return $this->readStyle('border-width');
    }

    /**
     * @param string $color
     */
    public function set_border_color($color)
    {
        $this->styles['border-color'] = $color;
    }

    /**
     * @return string
     */
    public function get_border_color()
    {
        return $this->readStyle('border-color');
    }

    /**
     * @param mixed[] $width_and_color
     */
    public function set_border($width_and_color)
    {
        list($width, $color) = $width_and_color;

        $this->set_border_width($width);
        $this->set_border_color($color);
    }

    /**
     * @return mixed[]
     */
    public function get_border()
    {
        return array($this->get_border_width(), $this->get_border_color());
    }

    /**
     * @param string $color
     */
    public function set_fill_color($color)
    {
        $this->styles['fill-color'] = $color;
    }

    /**
     * @return string
     */
    public function get_fill_color()
    {
        return $this->readStyle('fill-color');
    }
}
