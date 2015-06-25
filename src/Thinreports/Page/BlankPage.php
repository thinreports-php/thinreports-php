<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Page;

class BlankPage
{
    protected $number;
    protected $is_blank = true;
    protected $is_countable = true;

    /**
     * @param integer $page_number
     * @param boolean $countable
     */
    public function __construct($page_number, $countable = true)
    {
        $this->number = $page_number;
        $this->is_countable = $countable;
    }

    /**
     * @return boolean
     */
    public function isCountable()
    {
        return $this->is_countable;
    }

    /**
     * @return boolean
     */
    public function isBlank()
    {
        return $this->is_blank;
    }

    /**
     * @return integer
     */
    public function getNo()
    {
        return $this->number;
    }
}
