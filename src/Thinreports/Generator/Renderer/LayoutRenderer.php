<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Generator\Renderer;

use Thinreports\Layout;
use Thinreports\Generator\PDF;
use Thinreports\Exception;

/**
 * @access private
 */
class LayoutRenderer extends AbstractRenderer
{
    private $items = array();

    /**
     * @param PDF\Document $doc
     * @param Layout $layout
     */
    public function __construct(PDF\Document $doc, Layout $layout)
    {
        parent::__construct($doc);
        $this->items = $this->parse($layout);
    }

    /**
     * @param Layout $layout
     * @return array()
     */
    public function parse(Layout $layout)
    {
        $svg = preg_replace('<%.+?%>', '', $layout->getSVG());

        $xml = new \SimpleXMLElement($svg);
        $xml->registerXPathNamespace('svg', 'http://www.w3.org/2000/svg');
        $xml->registerXPathNamespace('xlink', 'http://www.w3.org/1999/xlink');

        $items = array();

        foreach ($xml->g->children() as $element) {
            $attributes = (array) $element->attributes();
            $attributes = $attributes['@attributes'];

            switch ($attributes['class']) {
                case 's-text':
                    $text_lines = array();

                    foreach ($element->text as $text_line) {
                        $text_lines[] = $text_line;
                    }
                    $attributes['content'] = implode("\n", $text_lines);
                    break;
                case 's-image':
                    $xlink_attribute = $element->attributes('xlink', true);
                    $attributes['xlink:href'] = (string) $xlink_attribute['href'];
                    break;
            }

            $items[] = $attributes;
        }
        return $items;
    }

    public function render()
    {
        foreach ($this->items as $attributes) {
            $type_name = $attributes['class'];

            switch ($type_name) {
                case 's-text':
                    $this->renderSVGText($attributes);
                    break;
                case 's-image':
                    $this->renderSVGImage($attributes);
                    break;
                case 's-rect':
                    $this->renderSVGRect($attributes);
                    break;
                case 's-ellipse':
                    $this->renderSVGEllipse($attributes);
                    break;
                case 's-line':
                    $this->renderSVGLine($attributes);
                    break;
                default:
                    throw new Exception\StandardException('Unknown Element', $type_name);
                    break;
            }
        }
    }

    /**
     * @param array $svg_attrs
     */
    public function renderSVGText(array $svg_attrs)
    {
        $styles = $this->buildTextStyles($svg_attrs);

        if (array_key_exists('x-valign', $svg_attrs)) {
            $valign = $svg_attrs['x-valign'];
        } else {
            $valign = null;
        }
        $styles['valign'] = $this->buildVerticalAlign($valign);

        if (array_key_exists('x-line-height-ratio', $svg_attrs)
                && $svg_attrs['x-line-height-ratio'] !== '') {
            $styles['line_height'] = $svg_attrs['x-line-height-ratio'];
        }

        $this->doc->text->drawTextBox(
            $svg_attrs['content'],
            $svg_attrs['x-left'],
            $svg_attrs['x-top'],
            $svg_attrs['x-width'],
            $svg_attrs['x-height'],
            $styles
        );
    }

    /**
     * @param array $svg_attrs
     */
    public function renderSVGRect(array $svg_attrs)
    {
        $styles = $this->buildGraphicStyles($svg_attrs);
        $styles['radius'] = $svg_attrs['rx'];

        $this->doc->graphics->drawRect(
            $svg_attrs['x'],
            $svg_attrs['y'],
            $svg_attrs['width'],
            $svg_attrs['height'],
            $styles
        );
    }

    /**
     * @param array $svg_attrs
     */
    public function renderSVGEllipse(array $svg_attrs)
    {
        $this->doc->graphics->drawEllipse(
            $svg_attrs['cx'],
            $svg_attrs['cy'],
            $svg_attrs['rx'],
            $svg_attrs['ry'],
            $this->buildGraphicStyles($svg_attrs)
        );
    }

    /**
     * @param array $svg_attrs
     */
    public function renderSVGLine(array $svg_attrs)
    {
        $this->doc->graphics->drawLine(
            $svg_attrs['x1'],
            $svg_attrs['y1'],
            $svg_attrs['x2'],
            $svg_attrs['y2'],
            $this->buildGraphicStyles($svg_attrs)
        );
    }

    /**
     * @param array $svg_attrs
     */
    public function renderSVGImage(array $svg_attrs)
    {
        $this->doc->graphics->drawBase64Image(
            $this->extractBase64Data($svg_attrs),
            $svg_attrs['x'],
            $svg_attrs['y'],
            $svg_attrs['width'],
            $svg_attrs['height']
        );
    }
}
