<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Item\Style;

use Thinreports\Exception;

/**
 * @access private
 */
class BasicStyle
{
    static protected $available_style_names = array();
    protected $styles = array();

    /**
     * @param array $item_format
     */
    public function __construct(array $item_format)
    {
        $this->initializeStyles($item_format);
    }

    /**
     * @param string $style_name
     * @param mixed $value
     */
    public function set($style_name, $value)
    {
        $this->verifyStyleName($style_name);

        $setter = "set_{$style_name}";
        $this->$setter($value);
    }

    /**
     * @param string $style_name
     * @return mixed $value
     */
    public function get($style_name)
    {
        $this->verifyStyleName($style_name);

        $getter = "get_{$style_name}";
        return $this->$getter();
    }

    /**
     * @return array
     */
    public function export()
    {
        return $this->styles;
    }

    /**
     * @param string $raw_style_name
     * @return mixed
     */
    public function readStyle($raw_style_name)
    {
        if (array_key_exists($raw_style_name, $this->styles)) {
            return $this->styles[$raw_style_name];
        } else {
            return null;
        }
    }

    /**
     * @param string $style_name
     * @throws Exception\StandardException
     */
    public function verifyStyleName($style_name)
    {
        if (!in_array($style_name, static::$available_style_names, true)) {
            throw new Exception\StandardException('Unavailable Style Name', $style_name);
        }
    }

    /**
     * @param string $style_name
     * @param mixed $value
     * @param mixed[] $allows
     * @throws Exception\UnavailableStyleValue
     */
    public function verifyStyleValue($style_name, $value, array $allows)
    {
        if (!in_array($value, $allows, true)) {
            throw new Exception\UnavailableStyleValue($style_name, $value, $allows);
        }
    }

    /**
     * @param array $item_format
     */
    protected function initializeStyles(array $item_format)
    {
        $this->styles = $item_format['svg']['attrs'] ?: array();
    }
}
