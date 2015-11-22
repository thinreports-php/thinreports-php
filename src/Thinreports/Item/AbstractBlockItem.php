<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Item;

abstract class AbstractBlockItem extends AbstractItem
{
    private $value = '';

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return $mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        $value = $this->getValue();
        return $value === null || $value === '';
    }

    /**
     * @return boolean
     */
    public function isPresent()
    {
        return !$this->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getBounds()
    {
        return $this->format['box'];
    }

    /**
     * {@inheritdoc}
     */
    public function isTypeOf($type_name)
    {
        return $type_name === 'block' || parent::isTypeOf($type_name);
    }
}
