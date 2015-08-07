<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Item;

use Thinreports\Page\Page;
use Thinreports\Item\Style;

class BasicItem extends AbstractItem
{
    const TYPE_NAME = 'basic';

    /**
     * {@inheritdoc}
     */
    public function __construct(Page $parent, array $format)
    {
        parent::__construct($parent, $format);

        switch (true) {
            case $this->isImage():
                $this->style = new Style\BasicStyle($format);
                break;
            case $this->isText():
                $this->style = new Style\TextStyle($format);
                break;
            default:
                $this->style = new Style\GraphicStyle($format);
                break;
        }
    }

    /**
     * @access private
     *
     * @return boolean
     */
    public function isImage()
    {
        return $this->isTypeOf('s-image');
    }

    /**
     * @access private
     *
     * @return boolean
     */
    public function isText()
    {
        return $this->isTypeOf('s-text');
    }

    /**
     * @access private
     *
     * @return boolean
     */
    public function isRect()
    {
        return $this->isTypeOf('s-rect');
    }

    /**
     * @access private
     *
     * @return boolean
     */
    public function isEllipse()
    {
        return $this->isTypeOf('s-ellipse');
    }

    /**
     * @access private
     *
     * @return boolean
     */
    public function isLine()
    {
        return $this->isTypeOf('s-line');
    }

    /**
     * {@inheritdoc}
     */
    public function isTypeOf($type_name)
    {
        return parent::isTypeOf($type_name) || self::TYPE_NAME == $type_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getBounds()
    {
        $svg_attrs = $this->getSVGAttributes();

        switch (true) {
            case $this->isImage() || $this->isRect():
                return array(
                    'x'      => $svg_attrs['x'],
                    'y'      => $svg_attrs['y'],
                    'width'  => $svg_attrs['width'],
                    'height' => $svg_attrs['height']
                );
                break;
            case $this->isText():
                return $this->format['box'];
                break;
            case $this->isEllipse():
                return array(
                    'cx' => $svg_attrs['cx'],
                    'cy' => $svg_attrs['cy'],
                    'rx' => $svg_attrs['rx'],
                    'ry' => $svg_attrs['ry']
                );
                break;
            case $this->isLine():
                return array(
                    'x1' => $svg_attrs['x1'],
                    'y1' => $svg_attrs['y1'],
                    'x2' => $svg_attrs['x2'],
                    'y2' => $svg_attrs['y2']
                );
                break;
        }
    }
}
