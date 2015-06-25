<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Generator;

use Thinreports\Report;
use Thinreports\Layout;
use Thinreports\Page\Page;
use Thinreports\Generator\PDF;

/**
 * @access private
 */
class PDFGenerator
{
    use ItemRenderers;

    private $report;
    private $layout_renderers = [];

    /**
     * @param Report $report
     * @return string
     */
    static public function generate(Report $report)
    {
        $generator = new self($report);
        return $generator->render();
    }

    /**
     * @param Report $report
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
        $this->pdf = new PDF\Document($report->getDefaultLayout());
    }

    /**
     * @return string
     */
    public function render()
    {
        foreach ($this->report->getPages() as $page) {
            if ($page->isBlank()) {
                $this->pdf->addBlankPage();
            } else {
                $this->renderPage($page);
            }
        }
        return $this->pdf->render();
    }

    /**
     * @param Page $page
     */
    public function renderPage(Page $page)
    {
        $layout = $page->getLayout();

        $this->pdf->addPage($layout);

        $this->renderLayout($layout);
        $this->renderItems($page);
    }

    /**
     * @param Layout $layout
     */
    public function renderLayout(Layout $layout)
    {
        $layout_identifier = $layout->getIdentifier();

        if (array_key_exists($layout_identifier, $this->layout_renderers)) {
            $renderer = $this->layout_renderers[$layout_identifier];
        } else {
            $renderer = LayoutRenderer::parse($layout);
            $this->layout_renderers[$layout_identifier] = $renderer;
        }
        $renderer->renderTo($this->pdf);
    }

    /**
     * @param Page $page
     */
    public function renderItems(Page $page)
    {
        $layout = $page->getLayout();

        foreach ($layout->getItemFormats() as $id => $format) {
            $item = $page->item($id);

            if (!$item->isVisible()) {
                continue;
            }

            switch (true) {
                case $item->isTypeOf('s-tblock'):
                    if ($item->hasReference() || $item->isPresent()) {
                        $this->renderTextBlockItem($item);
                    }
                    break;
                case $item->isTypeOf('s-iblock'):
                    if ($item->isPresent()) {
                        $this->renderImageBlockItem($item);
                    }
                    break;
                case $item->isTypeOf('s-pageno'):
                    if ($page->isCountable() && $item->isForReport()) {
                        $this->renderPageNumberItem($item);
                    }
                    break;
                case $item->isImage():
                    $this->renderImageItem($item);
                    break;
                case $item->isText():
                    $this->renderTextItem($item);
                    break;
                case $item->isRect():
                    $this->renderRectItem($item);
                    break;
                case $item->isEllipse():
                    $this->renderEllipseItem($item);
                    break;
                case $item->isLine():
                    $this->renderLineItem($item);
                    break;
            }
        }
    }
}
