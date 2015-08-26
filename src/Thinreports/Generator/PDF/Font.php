<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Generator\PDF;

/**
 * @access private
 */
class Font
{
    static public $builtin_unicode_fonts = array(
        'IPAMincho'  => 'ipam',
        'IPAPMincho' => 'ipamp',
        'IPAGothic'  => 'ipag',
        'IPAPGothic' => 'ipagp'
    );

    static public $builtin_font_aliases = array(
        'Courier New'     => 'Courier',
        'Times New Roman' => 'Times'
    );

    /**
     * @param string $name
     * @return string
     */
    static public function getFontName($name)
    {
        if (array_key_exists($name, self::$builtin_font_aliases)) {
            return self::$builtin_font_aliases[$name];
        }
        if (self::isBuiltinUnicodeFont($name)) {
            return self::$builtin_unicode_fonts[$name];
        }
        return $name;
    }

    /**
     * @param string $name
     * @return boolean
     */
    static public function isBuiltinUnicodeFont($name)
    {
        return in_array($name, array_keys(static::$builtin_unicode_fonts));
    }
}
