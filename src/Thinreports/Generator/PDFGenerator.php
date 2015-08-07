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
use Thinreports\Generator\Renderer;
use Thinreports\Generator\PDF;

/**
 * @access private
 */
class PDFGenerator
{
    /**
     * @var Report
     */
    private $report;

    /**
     * @var Renderer\LayoutRenderer[]
     */
    private $layout_renderers = array();

    /**
     * @var Renderer\ItemRenderer
     */
    private $item_renderer;

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
        $this->doc = new PDF\Document($report->getDefaultLayout());
        $this->item_renderer = new Renderer\ItemRenderer($this->doc);
    }

    /**
     * @return string
     */
    public function render()
    {
        foreach ($this->report->getPages() as $page) {
            if ($page->isBlank()) {
                $this->doc->addBlankPage();
            } else {
                $this->renderPage($page);
            }
        }
        return $this->doc->render();
    }

    /**
     * @param Page $page
     */
    public function renderPage(Page $page)
    {
        $layout = $page->getLayout();

        $this->doc->addPage($layout);

        $this->renderLayout($layout);
        $this->renderItems($page->getFinalizedItems());
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
            $renderer = new Renderer\LayoutRenderer($this->doc, $layout);
            $this->layout_renderers[$layout_identifier] = $renderer;
        }
        $renderer->render();
    }

    /**
     * @param Thinreports\Item\AbstractItem[] $items
     */
    public function renderItems(array $items)
    {
        foreach ($items as $item) {
            $this->item_renderer->render($item);
        }
    }
}
