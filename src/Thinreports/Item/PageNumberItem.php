<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Item;

use Thinreports\Page\Page;
use Thinreports\Item\Style\TextStyle;

class PageNumberItem extends AbstractItem
{
    const TYPE_NAME = 's-pageno';

    static protected $serial_number = 1;

    private $number_format;

    /**
     * @access private
     *
     * @return string
     */
    static public function generateUniqueId()
    {
        return '__page_no_' . self::$serial_number++ . '__';
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(Page $parent, array $format)
    {
        parent::__construct($parent, $format);

        $this->style = new TextStyle($format);
        $this->number_format = $this->format['format'];
    }

    /**
     * @param string $new_format
     * @return $this
     */
    public function setNumberFormat($new_format)
    {
        $this->number_format = $new_format;
        return $this;
    }

    /**
     * @return $this
     */
    public function resetNumberFormat()
    {
        $this->setNumberFormat($this->format['format']);
        return $this;
    }

    /**
     * @return string
     */
    public function getNumberFormat()
    {
        return $this->number_format;
    }

    /**
     * @access private
     *
     * @return mixed
     */
    public function getFormattedPageNumber()
    {
        $format = $this->getNumberFormat();

        if ($format === '' || !$this->isForReport()) {
            return '';
        }

        $page   = $this->getParent();
        $report = $page->getReport();

        return str_replace(array('{page}', '{total}'),
                           array($page->getNo(), $report->getLastPageNumber()),
                           $format);
    }

    /**
     * @access private
     *
     * @return boolean
     */
    public function isForReport()
    {
        return empty($this->format['target']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBounds()
    {
        return $this->format['box'];
    }
}
