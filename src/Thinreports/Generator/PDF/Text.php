<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Generator\PDF;

/**
 * @access private
 */
class Text
{
    static private $pdf_font_style = array(
        'bold'          => 'B',
        'italic'        => 'I',
        'underline'     => 'U',
        'strikethrough' => 'D'
    );

    static private $pdf_text_align = array(
        'left'   => 'L',
        'center' => 'C',
        'right'  => 'R'
    );

    static private $pdf_text_valign = array(
        'top'    => 'T',
        'center' => 'M',
        'bottom' => 'B'
    );

    static private $pdf_default_line_height = 1;

    /**
     * @var \TCPDF
     */
    private $pdf;

    /**
     * @param \TCPDF
     */
    public function __construct(\TCPDF $pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * @param string $content
     * @param float|string $x
     * @param float|string $y
     * @param float|string $width
     * @param float|string $height
     * @param array $attrs {
     *      @option string "font_family" required
     *      @option string[] "font_style" required
     *      @option float|string "font_size" required
     *      @option string "color" required
     *      @option string "overflow" optional default is "trunecate"
     *      @option boolean "single_row" optional default is false
     *      @option string "align" optional default is "left"
     *      @option string "valign" optional default is "top"
     *      @option string "letter_spacing" optional default is 0
     *      @option string "line_height" optional default is {@see self::$pdf_default_line_height}
     * }
     * @see http://www.tcpdf.org/doc/code/classTCPDF.html
     */
    public function drawTextBox($content, $x, $y, $width, $height, array $attrs = array())
    {
        $styles = $this->buildTextBoxStyles($height, $attrs);

        $this->setFontStyles($styles);
        $this->pdf->setFontSpacing($styles['letter_spacing']);
        $this->pdf->setCellHeightRatio($styles['line_height']);

        $overflow = $styles['overflow'];

        $font_family = $attrs['font_family'];
        $font_styles = $attrs['font_style'];
        $color       = $styles['color'];

        $emulating = $this->startStyleEmulation($font_family, $font_styles, $color);

        $this->pdf->MultiCell(
            $width,                  // width
            $height,                 // height
            $content,                // text
            0,                       // border
            $styles['align'],        // align
            false,                   // fill
            1,                       // ln
            $x,                      // x
            $y,                      // y
            true,                    // reset height
            0,                       // stretch mode
            false,                   // is html
            true,                    // autopadding
            $overflow['max_height'], // max-height
            $styles['valign'],       // valign
            $overflow['fit_cell']    // fitcell
        );

        if ($emulating) {
            $this->resetStyleEmulation();
        }
    }

    /**
     * {@see self::drawTextBox}
     */
    public function drawText($content, $x, $y, $width, $height, array $attrs = array())
    {
        $content = str_replace("\n", ' ', $content);
        $attrs['single_row'] = true;

        $this->drawTextBox($content, $x, $y, $width, $height, $attrs);
    }

    /**
     * @param array $style
     */
    public function setFontStyles(array $style)
    {
        $this->pdf->SetFont(
            $style['font_family'],
            $style['font_style'],
            $style['font_size']
        );
        $this->pdf->SetTextColorArray($style['color']);
    }

    /**
     * @param array $attrs
     * @return array
     */
    public function buildTextStyles(array $attrs)
    {
        $font_style = array();

        foreach ($attrs['font_style'] ?: array() as $style) {
            $font_style[] = self::$pdf_font_style[$style];
        }

        if (array_key_exists('line_height', $attrs)) {
            $line_height = $attrs['line_height'];
        } else {
            $line_height = self::$pdf_default_line_height;
        }

        if (array_key_exists('letter_spacing', $attrs)) {
            $letter_spacing = $attrs['letter_spacing'];
        } else {
            $letter_spacing = 0;
        }

        if (array_key_exists('align', $attrs)) {
            $align = $attrs['align'];
        } else {
            $align = 'left';
        }

        if (array_key_exists('valign', $attrs)) {
            $valign = $attrs['valign'];
        } else {
            $valign = 'top';
        }

        return array(
            'font_size'      => $attrs['font_size'],
            'font_family'    => Font::getFontName($attrs['font_family']),
            'font_style'     => implode('', $font_style),
            'color'          => ColorParser::parse($attrs['color']),
            'align'          => self::$pdf_text_align[$align],
            'valign'         => self::$pdf_text_valign[$valign],
            'line_height'    => $line_height,
            'letter_spacing' => $letter_spacing
        );
    }

    /**
     * @param float|string $box_height
     * @param array $attrs
     * @return array
     */
    public function buildTextBoxStyles($box_height, array $attrs)
    {
        $is_single = array_key_exists('single_row', $attrs)
                     && $attrs['single_row'] === true;

        if ($is_single) {
            unset($attrs['line_height']);
        }

        if (array_key_exists('overflow', $attrs)) {
            $overflow = $attrs['overflow'];
        } else {
            $overflow = 'truncate';
        }
        switch ($overflow) {
            case 'truncate':
                $fit_cell   = false;
                $max_height = $box_height;
                break;
            case 'fit':
                $fit_cell   = true;
                $max_height = $box_height;
                break;
            case 'expand':
                $fit_cell   = false;
                $max_height = 0;
                break;
        }

        $styles = $this->buildTextStyles($attrs);

        $styles['overflow'] = array(
            'fit_cell'   => $fit_cell,
            'max_height' => $max_height
        );

        return $styles;
    }

    /**
     * @param string $family
     * @param array $styles
     * @param integer[] $color
     * @return boolean
     */
    public function startStyleEmulation($family, array $styles, array $color)
    {
        $need_emulate = in_array('bold', $styles)
                        && Font::isBuiltinUnicodeFont($family);

        if (!$need_emulate) {
            return false;
        } else {
            $this->pdf->setDrawColorArray($color);
            $this->pdf->setTextRenderingMode($this->pdf->GetLineWidth() * 0.1);
            return true;
        }
    }

    public function resetStyleEmulation()
    {
        $this->pdf->setTextRenderingMode(0);
    }
}
