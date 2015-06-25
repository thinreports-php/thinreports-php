<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Generator;

use Thinreports\Item;

/**
 * @access private
 */
trait ItemRenderers
{
    use StyleBuilder;

    /**
     * @param Item\TextBlockItem $item
     */
    public function renderTextBlockItem(Item\TextBlockItem $item)
    {
        $format = $item->getFormat();
        $bounds = $item->getBounds();
        $styles = $this->buildTextStyles($item->exportStyles());

        $styles['valign'] = $item->getStyle('valign');

        if ($format['overflow'] !== '') {
            $styles['overflow'] = $format['overflow'];
        }
        if ($format['line-height-ratio'] !== '') {
            $styles['line_height'] = $format['line-height-ratio'];
        }

        if ($item->isMultiple()) {
            $this->pdf->drawTextBox(
                $item->getRealValue(),
                $bounds['x'],
                $bounds['y'],
                $bounds['width'],
                $bounds['height'],
                $styles
            );
        } else {
            $this->pdf->drawText(
                $item->getRealValue(),
                $bounds['x'],
                $bounds['y'],
                $bounds['width'],
                $bounds['height'],
                $styles
            );
        }
    }

    /**
     * @param Item\ImageBlockItem $item
     */
    public function renderImageBlockItem(Item\ImageBlockItem $item)
    {
        $bounds = $item->getBounds();
        $styles = $this->buildImageBoxItemStyles($item);

        $this->pdf->drawImage(
            $item->getSource(),
            $bounds['x'],
            $bounds['y'],
            $bounds['width'],
            $bounds['height'],
            $styles
        );
    }

    /**
     * @param Item\PageNumberItem $item
     */
    public function renderPageNumberItem(Item\PageNumberItem $item)
    {
        $format = $item->getFormat();
        $bounds = $item->getBounds();

        $this->pdf->drawText(
            $item->getFormattedPageNumber(),
            $bounds['x'],
            $bounds['y'],
            $bounds['width'],
            $bounds['height'],
            $this->buildTextStyles($item->exportStyles())
        );
    }

    /**
     * @param Item\BasicItem $item
     */
    public function renderImageItem(Item\BasicItem $item)
    {
        $bounds = $item->getBounds();
        $attrs  = $item->exportStyles();

        $this->pdf->drawBase64Image(
            $this->extractBase64Data($attrs),
            $bounds['x'],
            $bounds['y'],
            $bounds['width'],
            $bounds['height']
        );
    }

    /**
     * @param Item\BasicItem $item
     */
    public function renderTextItem(Item\BasicItem $item)
    {
        $format = $item->getFormat();
        $bounds = $item->getBounds();

        $this->pdf->drawTextBox(
            implode("\n", $format['text']),
            $bounds['x'],
            $bounds['y'],
            $bounds['width'],
            $bounds['height'],
            $this->buildTextStyles($item->exportStyles())
        );
    }

    /**
     * @param Item\BasicItem $item
     */
    public function renderRectItem(Item\BasicItem $item)
    {
        $bounds = $item->getBounds();
        $attrs  = $item->exportStyles();

        $styles = $this->buildGraphicStyles($attrs);
        $styles['radius'] = $attrs['rx'];

        $this->pdf->drawRect(
            $bounds['x'],
            $bounds['y'],
            $bounds['width'],
            $bounds['height'],
            $this->buildGraphicStyles($item->exportStyles())
        );
    }

    /**
     * @param Item\BasicItem $item
     */
    public function renderEllipseItem(Item\BasicItem $item)
    {
        $bounds = $item->getBounds();

        $this->pdf->drawEllipse(
            $bounds['cx'],
            $bounds['cy'],
            $bounds['rx'],
            $bounds['ry'],
            $this->buildGraphicStyles($item->exportStyles())
        );
    }

    /**
     * @param Item\BasicItem $item
     */
    public function renderLineItem(Item\BasicItem $item)
    {
        $bounds = $item->getBounds();

        $this->pdf->drawLine(
            $bounds['x1'],
            $bounds['y1'],
            $bounds['x2'],
            $bounds['y2'],
            $this->normalizeGraphicStyles($item->exportStyles())
        );
    }

    /**
     * @param Item\ImageBlockItem $item
     * @return array
     */
    public function buildImageBoxItemStyles(Item\ImageBlockItem $item)
    {
        $format = $item->getFormat();

        $align  = $format['position-x'] ?: 'left';
        $valign = $format['position-y'] ?: 'top';

        if ($format['position-y'] === 'center') {
            $valign = 'middle';
        }
        return [
            'align'  => $align,
            'valign' => $valign
        ];
    }
}
