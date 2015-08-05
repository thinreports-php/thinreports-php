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
     * @param float|string $width
     */
    public function set_border_width($width)
    {
        if ((float) $width > 0) {
            $this->styles['stroke-opacity'] = '1';
        }
        $this->styles['stroke-width'] = $width;
    }

    /**
     * @return float|string
     */
    public function get_border_width()
    {
        return $this->readStyle('stroke-width');
    }

    /**
     * @param string $color
     */
    public function set_border_color($color)
    {
        $this->styles['stroke'] = $color;
    }

    /**
     * @return string
     */
    public function get_border_color()
    {
        return $this->readStyle('stroke');
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
        $this->styles['fill'] = $color;
    }

    /**
     * @return string
     */
    public function get_fill_color()
    {
        return $this->readStyle('fill');
    }
}
