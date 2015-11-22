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

    static private $text_alignments = array(
        'left'   => 'start',
        'center' => 'middle',
        'right'  => 'end'
    );

    static private $vertical_alignments = array(
        'top', 'center', 'bottom'
    ) ;

    private $vertical_align = 'top';

    /**
     * {@inheritdoc}
     */
    protected function initializeStyles(array $item_format)
    {
        parent::initializeStyles($item_format);

        if (!empty($item_format['valign'])) {
            $this->vertical_align = $item_format['valign'];
        }
    }

    /**
     * @param string $color
     */
    public function set_color($color)
    {
        $this->styles['fill'] = $color;
    }

    /**
     * @return string
     */
    public function get_color()
    {
        return $this->readStyle('fill');
    }

    /**
     * @param float|string
     */
    public function set_font_size($size)
    {
        $this->styles['font-size'] = $size;
    }

    /**
     * @return float|string
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
        $this->styles['font-weight'] = $enable ? 'bold' : 'normal';
    }

    /**
     * @return boolean
     */
    public function get_bold()
    {
        return $this->readStyle('font-weight') === 'bold';
    }

    /**
     * @param boolean $enable
     */
    public function set_italic($enable)
    {
        $this->styles['font-style'] = $enable ? 'italic' : 'normal';
    }

    /**
     * @return boolean
     */
    public function get_italic()
    {
        return $this->readStyle('font-style') === 'italic';
    }

    /**
     * @param boolean $enable
     */
    public function set_underline($enable)
    {
        $this->setTextDecoration($enable, null);
    }

    /**
     * @return boolean
     */
    public function get_underline()
    {
        return $this->hasTextDecoration('underline');
    }

    /**
     * @param boolean $enable
     */
    public function set_linethrough($enable)
    {
        $this->setTextDecoration(null, $enable);
    }

    /**
     * @return boolean
     */
    public function get_linethrough()
    {
        return $this->hasTextDecoration('line-through');
    }

    /**
     * @param string $alignment {@see TextStyle::$text_alignments}
     */
    public function set_align($alignment)
    {
        $this->verifyStyleValue('align', $alignment, array_keys(self::$text_alignments));
        $this->styles['text-anchor'] = self::$text_alignments[$alignment];
    }

    /**
     * @return string {@see TextStyle::$text_alignments}
     * @throws Exception\UnavailableStyleValue
     */
    public function get_align()
    {
        $alignment_value = $this->readStyle('text-anchor');

        if (empty($alignment_value)) {
            return 'left';
        }

        $alignment_key = array_search($alignment_value, self::$text_alignments);

        if (!$alignment_key) {
            throw new Exception\UnavailableStyleValue(
                'align', $alignment_value, array_keys(self::$text_alignments));
        }
        return $alignment_key;
    }

    /**
     * @param string $alignment {@see TextStyle::$vertical_alignments}
     */
    public function set_valign($alignment)
    {
        $this->verifyStyleValue('valign', $alignment, self::$vertical_alignments);
        $this->vertical_align = $alignment;
    }

    /**
     * @return string {@see TextStyle::$vertical_alignments}
     */
    public function get_valign()
    {
        return $this->vertical_align;
    }

    /**
     * @param boolean $underline = null
     * @param boolean $linethrough = null
     */
    private function setTextDecoration($underline = null, $linethrough = null)
    {
        $decorations = array();

        if ($underline === null) {
            $underline = $this->get_underline();
        }
        if ($linethrough === null) {
            $linethrough = $this->get_linethrough();
        }

        if ($underline || $linethrough) {
            if ($underline) {
                $decorations[] = 'underline';
            }
            if ($linethrough) {
                $decorations[] = 'line-through';
            }
        } else {
            $decorations[] = 'none';
        }

        $this->styles['text-decoration'] = implode(' ', $decorations);
    }

    /**
     * @param string|null $decoration_name
     * @return boolean
     */
    private function hasTextDecoration($decoration_name)
    {
        $decoration = $this->readStyle('text-decoration');

        if (!empty($decoration)) {
            return in_array($decoration_name, explode(' ', $decoration), true);
        } else {
            return false;
        }
    }
}
