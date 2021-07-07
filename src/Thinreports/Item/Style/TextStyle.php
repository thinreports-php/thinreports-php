<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Item\Style;

use Thinreports\Exception;

/**
 * @access private
 */
class TextStyle extends BasicStyle
{
    static protected $available_style_names = array(
        'bold', 'italic', 'underline', 'linethrough',
        'align', 'valign', 'color', 'font_size'
    );

    /**
     * @param string $color
     */
    public function set_color($color)
    {
        $this->styles['color'] = $color;
    }

    /**
     * @return string
     */
    public function get_color()
    {
        return $this->readStyle('color');
    }

    /**
     * @param float|integer $size
     */
    public function set_font_size($size)
    {
        $this->styles['font-size'] = $size;
    }

    /**
     * @return float|integer
     */
    public function get_font_size()
    {
        return $this->readStyle('font-size');
    }

    /**
     * @param boolean $enable
     */
    public function set_bold($enable)
    {
        $this->updateFontStyle('bold', $enable);
    }

    /**
     * @return boolean
     */
    public function get_bold()
    {
        return $this->hasFontStyle('bold');
    }

    /**
     * @param boolean $enable
     */
    public function set_italic($enable)
    {
        $this->updateFontStyle('italic', $enable);
    }

    /**
     * @return boolean
     */
    public function get_italic()
    {
        return $this->hasFontStyle('italic');
    }

    /**
     * @param boolean $enable
     */
    public function set_underline($enable)
    {
        $this->updateFontStyle('underline', $enable);
    }

    /**
     * @return boolean
     */
    public function get_underline()
    {
        return $this->hasFontStyle('underline');
    }

    /**
     * @param boolean $enable
     */
    public function set_linethrough($enable)
    {
        $this->updateFontStyle('linethrough', $enable);
    }

    /**
     * @return boolean
     */
    public function get_linethrough()
    {
        return $this->hasFontStyle('linethrough');
    }

    /**
     * @param string $alignment
     */
    public function set_align($alignment)
    {
        $this->verifyStyleValue('align', $alignment, array('left', 'center', 'right'));
        $this->styles['text-align'] = $alignment;
    }

    /**
     * @return string
     */
    public function get_align()
    {
        $alignment = $this->readStyle('text-align');
        return $alignment === '' ? 'left' : $alignment;
    }

    /**
     * @param string $alignment
     */
    public function set_valign($alignment)
    {
        $this->verifyStyleValue('valign', $alignment, array('top', 'middle', 'bottom'));
        $this->styles['vertical-align'] = $alignment;
    }

    /**
     * @return string
     */
    public function get_valign()
    {
        $alignment = $this->readStyle('vertical-align');
        return $alignment === '' ? 'top' : $alignment;
    }

    /**
     * @param string $type Availables are "bold", "italic", "underline", "linethrough"
     * @param boolean $enable
     */
    private function updateFontStyle($type, $enable)
    {
        if ($enable) {
            $this->enableFontStyle($type);
        } else {
            $this->disableFontStyle($type);
        }
    }

    /**
     * @param string $type {@see self::updateFontStyle()}
     */
    private function enableFontStyle($type)
    {
        if (!$this->hasFontStyle($type)) {
            array_push($this->styles['font-style'], $type);
        }
    }

    /**
     * @param string $type {@see self::updateFontStyle()}
     */
    private function disableFontStyle($type)
    {
        if ($this->hasFontStyle($type)) {
            $index = array_search($type, $this->styles['font-style']);
            array_splice($this->styles['font-style'], $index, 1);
        }
    }

    /**
     * @param string $type {@see self::updateFontStyle()}
     * @return boolean
     */
    private function hasFontStyle($type)
    {
        return array_search($type, $this->styles['font-style']) !== false;
    }
}
