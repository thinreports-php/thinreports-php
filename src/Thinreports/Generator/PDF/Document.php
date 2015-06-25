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
    use Font, ColorParser, Graphics, Text;

    /**
     * @var \TCPDF
     */
    private $pdf;
    private $default_page_format;

    /**
     * @param Layout $default_layout
     */
    public function __construct(Layout $default_layout)
    {
        $this->pdf = new \TCPDF('P', 'pt', 'A4', true, 'UTF-8');

        $this->pdf->SetTitle($default_layout->getReportTitle());
        $this->pdf->SetCreator('Thinreports Generator');

        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->SetMargins(0, 0, 0, true);
        $this->pdf->SetCellPadding(0);
        $this->pdf->SetCellMargins(0, 0, 0, 0);
        $this->pdf->SetPrintHeader(false);
        $this->pdf->SetPrintFooter(false);

        $this->default_layout = $default_layout;
        $this->default_page_format = $this->buildPageFormat($default_layout);
    }

    /**
     * @param Layout $layout
     */
    public function addPage(Layout $layout)
    {
        if ($layout->getIdentifier() === $this->default_layout->getIdentifier()) {
            $page_format = $this->default_page_format;
        } else {
            $page_format = $this->buildPageFormat($layout);
        }

        $this->pdf->AddPage($page_format['orientation'], $page_format['size']);
    }

    public function addBlankPage()
    {
        $this->addPage($this->default_layout);
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

        return [
            'orientation' => $orientation,
            'size' => $size
        ];
    }

    public function __destruct()
    {
        $this->clearRegisteredImages();
    }
}
