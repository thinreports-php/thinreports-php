<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Page;

use Thinreports\Report;
use Thinreports\Layout;
use Thinreports\Exception;

class Page extends BlankPage
{
    private $report;
    private $layout;
    private $items = array();

    /**
     * @param Report $report
     * @param Layout $layout
     * @param integer $page_number
     * @param boolean $countable
     */
    public function __construct(Report $report, Layout $layout, $page_number, $countable = true)
    {
        parent::__construct($page_number, $countable);

        $this->report = $report;
        $this->layout = $layout;
        $this->is_blank = false;
    }

    /**
     * @param string $id
     * @return \Thinreports\Item\AbstractItem
     */
    public function item($id)
    {
        if (array_key_exists($id, $this->items)) {
            return $this->items[$id];
        }

        $item = $this->layout->createItem($this, $id);
        $this->items[$id] = $item;

        return $item;
    }

    /**
     * @see self::item()
     */
    public function __invoke($id)
    {
        return $this->item($id);
    }

    /**
     * @param string $id
     * @param mixed $value
     * @throws Exception\StandardException
     */
    public function setItemValue($id, $value)
    {
        $item = $this->item($id);

        if (!$item->isTypeOf('block')) {
            throw new Exception\StandardException('Unedtiable Item', $id);
        }
        $item->setValue($value);
    }

    /**
     * @param array $values
     */
    public function setItemValues(array $values)
    {
        foreach ($values as $id => $value) {
            $this->setItemValue($id, $value);
        }
    }

    /**
     * @param string $id
     * @return boolean
     */
    public function hasItem($id)
    {
        return $this->layout->hasItem($id);
    }

    /**
     * @return string[]
     */
    public function getItemIds()
    {
        return array_keys($this->layout->getItemFormats());
    }

    /**
     * @access private
     *
     * @return Report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @access private
     *
     * @return Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @access private
     *
     * @return Thinreports\Item\AbstractItem[]
     */
    public function getFinalizedItems()
    {
        $items = array();

        foreach ($this->getItemIds() as $id) {
            $items[] = $this->item($id);
        }
        return $items;
    }
}
