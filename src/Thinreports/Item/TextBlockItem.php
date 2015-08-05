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
use Thinreports\Item\TextFormatter;
use Thinreports\Exception;

class TextBlockItem extends AbstractBlockItem
{
    const TYPE_NAME = 's-tblock';

    private $format_enabled = null;
    private $reference_item = null;
    private $formatter;

    /**
     * {@inheritdoc}
     */
    public function __construct(Page $parent, array $format)
    {
        parent::__construct($parent, $format);

        $this->style = new TextStyle($format);
        $this->formatter = new TextFormatter($format['format']);

        $this->format_enabled = $this->hasFormatSettings();

        parent::setValue($format['value']);

        if ($this->hasReference()) {
            $this->reference_item = $parent->item($format['ref-id']);
        }
    }

    /**
     * {@inheritdoc}
     * @throws Exception\StandardException
     */
    public function setValue($value)
    {
        if ($this->hasReference()) {
            throw new Exception\StandardException('Readonly Item', $this->getId(),
                "It can't be overwritten, because it has references to the other.");
        } else {
            parent::setValue($value);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if ($this->hasReference()) {
            return $this->reference_item->getValue();
        } else {
            return parent::getValue();
        }
    }

    /**
     * @param boolean $enable
     * @return $this
     */
    public function setFormatEnabled($enable)
    {
        if ($enable) {
            if ($this->isMultiple()) {
                throw new Exception\StandardException('Not Formattable',
                    $this->getId(), 'It is multiple-line Text Block.');
            }
            if (!$this->hasFormatSettings()) {
                throw new Exception\StandardException('Not Formattable',
                    $this->getId(), 'It has no formatting configuration.');
            }
        }
        $this->format_enabled = $enable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFormatEnabled()
    {
        return $this->format_enabled;
    }

    /**
     * @access private
     *
     * @return mixed
     */
    public function getRealValue()
    {
        if ($this->isFormatEnabled()) {
            return $this->formatter->format($this->getValue());
        } else {
            return $this->getValue();
        }
    }

    /**
     * @access private
     *
     * @return boolean
     */
    public function isMultiple()
    {
        return $this->format['multiple'] === 'true';
    }

    /**
     * @access private
     *
     * @return boolean
     */
    public function hasFormatSettings()
    {
        $text_format = $this->format['format'];
        return $text_format['type'] !== '' || $text_format['base'] !== '';
    }

    /**
     * @access private
     *
     * @return boolean
     */
    public function hasReference()
    {
        return $this->format['ref-id'] !== '';
    }
}
