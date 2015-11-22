<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports;

use Thinreports\Page;
use Thinreports\Generator;

class Report
{
    private $default_layout = null;
    private $layouts = array();

    private $pages = array();
    private $page_count = 0;
    private $start_page_number = 1;

    /**
     * @param string|null $default_layout_filename
     */
    public function __construct($default_layout_filename = null)
    {
        if ($default_layout_filename !== null) {
            $this->default_layout = $this->buildLayout($default_layout_filename);
        }
    }

    /**
     * @param string|null $layout_filename
     * @param boolean $countable
     * @return Page\Page
     *
     * Usage example:
     *
     *  # Use default layout, count number of pages
     *  $page->addPage();
     *
     *  # Use other_layout.tlf, count number of pages
     *  $page->addPage('other_layout.tlf');
     *
     *  # Use default layout, don't count number of pages
     *  $page->addPage(null, false);
     */
    public function addPage($layout_filename = null, $countable = true)
    {
        $layout = $this->loadLayout($layout_filename);
        $page_number = $this->getNextPageNumber($countable);

        $new_page = new Page\Page($this, $layout, $page_number, $countable);
        $this->pages[] = $new_page;

        return $new_page;
    }

    /**
     * @param boolean $countable
     * @return Page\BlankPage
     */
    public function addBlankPage($countable = true)
    {
        $page_number = $this->getNextPageNumber($countable);

        $blank_page = new Page\BlankPage($page_number, $countable);
        $this->pages[] = $blank_page;

        return $blank_page;
    }

    /**
     * @return integer
     */
    public function getPageCount()
    {
        return $this->page_count;
    }

    /**
     * @return integer
     */
    public function getLastPageNumber()
    {
        return ($this->start_page_number - 1) + $this->page_count;
    }

    /**
     * @param integer $number
     */
    public function startPageNumberFrom($number)
    {
        $this->start_page_number = $number;
    }

    /**
     * @return integer
     */
    public function getStartPageNumber()
    {
        return $this->start_page_number;
    }

    /**
     * @return (Page\Page|Page\BlankPage)[]
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param string|null $filename
     *
     * @return boolean|string
     */
    public function generate($filename = null)
    {
        $pdf_data = Generator\PDFGenerator::generate($this);

        if ($filename === null) {
            return $pdf_data;
        } else {
            return file_put_contents($filename, $pdf_data) !== false;
        }
    }

    /**
     * @access private
     *
     * @return Layout
     */
    public function getDefaultLayout()
    {
        return $this->default_layout;
    }

    /**
     * @access private
     *
     * @param boolean $count
     * @return integer|null
     */
    private function getNextPageNumber($count = true)
    {
        if ($count) {
            $this->page_count ++;
            return ($this->start_page_number - 1) + $this->page_count;
        } else {
            return null;
        }
    }

    /**
     * @access private
     *
     * @param string|null $layout_filename
     * @return Layout
     */
    public function loadLayout($layout_filename = null)
    {
        if ($layout_filename !== null) {
            return $this->buildLayout($layout_filename);
        }

        if ($this->default_layout === null) {
            throw new Exception\StandardException('Layout Not Specified');
        } else {
            return $this->default_layout;
        }
    }

    /**
     * @access private
     *
     * @param string $layout_filename
     * @return Layout
     */
    public function buildLayout($layout_filename)
    {
        if (!array_key_exists($layout_filename, $this->layouts)) {
            $this->layouts[$layout_filename] = Layout::loadFile($layout_filename);
        }
        return $this->layouts[$layout_filename];
    }
}
