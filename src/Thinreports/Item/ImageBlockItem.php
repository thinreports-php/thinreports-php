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
    const TYPE_NAME = 'image-block';

    /**
     * {@inheritdoc}
     */
    public function __construct(Page $parent, array $schema)
    {
        parent::__construct($parent, $schema);

        $this->style = new BasicStyle($schema);
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
