<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Item;

use Thinreports\Page\Page;
use Thinreports\Item\Style\BasicStyle;

class ImageBlockItem extends AbstractBlockItem
{
    const TYPE_NAME = 's-iblock';

    /**
     * {@inheritdoc}
     */
    public function __construct(Page $parent, array $format)
    {
        parent::__construct($parent, $format);

        $this->style = new BasicStyle($format);
    }

    /**
     * @see self::setValue()
     */
    public function setSource()
    {
        return call_user_func_array(array($this, 'setValue'), func_get_args());
    }

    /**
     * @see self::getValue()
     */
    public function getSource()
    {
        return call_user_func(array($this, 'getValue'));
    }
}
