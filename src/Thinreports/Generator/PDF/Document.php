<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Generator\PDF;

use Thinreports\Layout;

class Document
{
    /**
     * @var \TCPDF
     */
    private $pdf;

    /**
     * @var Graphics
     * @access public
     */
    public $graphics;

    /**
     * @var Text
     * @access public
     */
    public $text;

    /**
     * @var array
     */
    private $page_formats = array();

    /**
     * @var Layout The layout that inserted at last.
     */
    private $last_page_layout = null;

    /**
     * @param Layout|null $default_layout
     */
    public function __construct(Layout $default_layout = null)
    {
        $this->pdf = new \TCPDF('P', 'pt', 'A4', true, 'UTF-8');

        $this->pdf->SetCreator('Thinreports Generator');
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->SetMargins(0, 0, 0, true);
        $this->pdf->SetCellPadding(0);
        $this->pdf->SetCellMargins(0, 0, 0, 0);
        $this->pdf->SetPrintHeader(false);
        $this->pdf->SetPrintFooter(false);

        if ($default_layout !== null) {
            $this->pdf->SetTitle($default_layout->getReportTitle());
            $this->registerPageFormat($default_layout);
        }

        $this->initDrawer();
    }

    /**
     * @param Layout $layout
     */
    public function addPage(Layout $layout)
    {
        $page_format = $this->registerPageFormat($layout);
        $this->pdf->AddPage($page_format['orientation'], $page_format['size']);

        $this->last_page_layout = $layout;
    }

    public function addBlankPage()
    {
        if ($this->last_page_layout !== null) {
            $page_format = $this->getRegisteredPageFormat($this->last_page_layout->getIdentifier());
        } else {
            $page_format = array('orientation' => 'P', 'size' => 'A4');
        }
        $this->pdf->AddPage($page_format['orientation'], $page_format['size']);
    }

    /**
     * @return string PDF data
     */
    public function render()
    {
        return $this->pdf->getPDFData();
    }

    /**
     * @param Layout $layout
     * @return array
     */
    public function buildPageFormat(Layout $layout)
    {
        $orientation = $layout->isPortraitPage() ? 'P' : 'L';

        if ($layout->isUserPaperType()) {
            $size = $layout->getPageSize();
        } else {
            switch ($layout->getPagePaperType()) {
                case 'B4_ISO':
                    $size = 'B4';
                    break;
                case 'B5_ISO':
                    $size = 'B5';
                    break;
                case 'B4':
                    $size = 'B4_JIS';
                    break;
                case 'B5':
                    $size = 'B5_JIS';
                    break;
                default:
                    $size = $layout->getPagePaperType();
                    break;
            }
        }

        return array(
            'orientation' => $orientation,
            'size' => $size
        );
    }

    /**
     * @param Layout $layout
     * @return array
     */
    public function registerPageFormat(Layout $layout)
    {
        $layout_identifier = $layout->getIdentifier();

        if (!array_key_exists($layout_identifier, $this->page_formats)) {
            $this->page_formats[$layout_identifier] = $this->buildPageFormat($layout);
        }
        return $this->getRegisteredPageFormat($layout_identifier);
    }

    /**
     * @param string $layout_identifier
     * @return array
     */
    public function getRegisteredPageFormat($layout_identifier)
    {
        return $this->page_formats[$layout_identifier];
    }

    public function initDrawer()
    {
        $this->graphics = new Graphics($this->pdf);
        $this->text     = new Text($this->pdf);
    }

    public function __destruct()
    {
        $this->graphics->clearRegisteredImages();
    }
}
