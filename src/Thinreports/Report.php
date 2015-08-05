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
    private $layout;

    private $pages = array();
    private $page_count = 0;
    private $start_page_number = 1;

    /**
     * @param string $layout_filename
     */
    public function __construct($layout_filename)
    {
        $this->layout = Layout::loadFile($layout_filename);
    }

    /**
     * @param array $options {
     *      @option boolean "count" optional
     * }
     * @return Page\Page
     *
     * Usage example:
     *
     *  $page->addPage();
     *  $page->addPage(array('count' => false));
     */
    public function addPage(array $options = null)
    {
        $options     = $this->pageOptionValues($options);
        $page_number = $this->getNextPageNumber($options['count']);

        $new_page = new Page\Page($this, $this->layout, $page_number, $options['count']);
        $this->pages[] = $new_page;

        return $new_page;
    }

    /**
     * @param array|null $options {
     *      @option boolean "count" optional
     * }
     * @return Page\BlankPage
     */
    public function addBlankPage(array $options = null)
    {
        $options     = $this->pageOptionValues($options);
        $page_number = $this->getNextPageNumber($options['count']);

        $blank_page = new Page\BlankPage($page_number, $options['count']);
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

        if (is_null($filename)) {
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
        return $this->layout;
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
     * @param array|null $options {
     *      @option string "count" optional
     * }
     * @return array
     */
    private function pageOptionValues(array $options = null)
    {
        $values = array('count' => true);

        if (is_array($options) && array_key_exists('count', $options)) {
            $values['count'] = $options['count'] === true;
        }
        return $values;
    }
}
