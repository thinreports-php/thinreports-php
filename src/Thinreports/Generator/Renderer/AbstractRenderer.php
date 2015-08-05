<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Generator\Renderer;

use Thinreports\Generator\PDF;

/**
 * @access private
 */
abstract class AbstractRenderer
{
    /**
     * @var PDF\Document
     */
    protected $doc;

    /**
     * @param PDF\Document $doc
     */
    public function __construct(PDF\Document $doc)
    {
        $this->doc = $doc;
    }

    /**
     * @param array $svg_attrs
     * @return array
     */
    public function buildGraphicStyles(array $svg_attrs)
    {
        if (array_key_exists('stroke-opacity', $svg_attrs)
            && $svg_attrs['stroke-opacity'] === '0') {
            $stroke_width = 0;
        } else {
            $stroke_width = $svg_attrs['stroke-width'];
        }

        return array(
            'stroke_color' => $svg_attrs['stroke'],
            'stroke_width' => $stroke_width,
            'stroke_dash'  => $svg_attrs['stroke-dasharray'],
            'fill_color'   => $svg_attrs['fill']
        );
    }

    /**
     * @param array $svg_attrs
     * @return array
     */
    public function buildTextStyles(array $svg_attrs)
    {
        return array(
            'font_family'    => $svg_attrs['font-family'],
            'font_size'      => $svg_attrs['font-size'],
            'font_style'     => $this->buildFontStyle($svg_attrs),
            'color'          => $svg_attrs['fill'],
            'align'          => $this->buildTextAlign($svg_attrs['text-anchor']),
            'letter_spacing' => $this->buildLetterSpacing($svg_attrs['letter-spacing'])
        );
    }

    /**
     * @param array $svg_attrs
     * @return string[]
     */
    public function buildFontStyle(array $svg_attrs)
    {
        $styles = array();

        if ($svg_attrs['font-weight'] === 'bold') {
            $styles[] = 'bold';
        }
        if ($svg_attrs['font-style'] === 'italic') {
            $styles[] = 'italic';
        }

        $decoration = $svg_attrs['text-decoration'];

        if (!empty($decoration) && $decoration !== 'none') {
            $decorations = explode(' ', $decoration);

            if (in_array('underline', $decorations)) {
                $styles[] = 'underline';
            }
            if (in_array('line-through', $decorations)) {
                $styles[] = 'strikethrough';
            }
        }
        return $styles;
    }

    /**
     * @param string $align
     * @return string
     */
    public function buildTextAlign($align)
    {
        switch ($align) {
            case 'start':
                return 'left';
                break;
            case 'middle':
                return 'center';
                break;
            case 'end':
                return 'right';
                break;
            default:
                return 'left';
        }
    }

    /**
     * @param string|null $valign
     * @return string
     */
    public function buildVerticalAlign($valign)
    {
        return $valign ?: 'top';
    }

    /**
     * @param string|null $letter_spacing
     * @return string|null
     */
    public function buildLetterSpacing($letter_spacing)
    {
        if (in_array($letter_spacing, array(null, 'auto', 'normal'))) {
            return null;
        } else {
            return $letter_spacing;
        }
    }

    /**
     * @param array $svg_attrs
     * @return string
     */
    public function extractBase64Data(array $svg_attrs)
    {
        return preg_replace('/^data:image\/[a-z]+?;base64,/',
                            '', $svg_attrs['xlink:href']);
    }
}
