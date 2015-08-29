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
    const STORE_PATH = '/../../../../fonts';

    /**
     * @var string[]
     */
    static public $installed_builtin_fonts = array();

    static public $builtin_unicode_fonts = array(
        'IPAMincho'  => 'ipam.ttf',
        'IPAPMincho' => 'ipamp.ttf',
        'IPAGothic'  => 'ipag.ttf',
        'IPAPGothic' => 'ipagp.ttf'
    );

    static public $builtin_font_aliases = array(
        'Courier New'     => 'Courier',
        'Times New Roman' => 'Times'
    );

    static public function init()
    {
        foreach (array_keys(self::$builtin_unicode_fonts) as $name) {
            self::installBuiltinFont($name);
        }
    }

    /**
     * @param string $name
     * @return string
     */
    static public function getFontName($name)
    {
        if (array_key_exists($name, self::$builtin_font_aliases)) {
            return self::$builtin_font_aliases[$name];
        }

        if (array_key_exists($name, self::$builtin_unicode_fonts)) {
            if (self::isInstalledFont($name)) {
                return static::$installed_builtin_fonts[$name];
            } else {
                return self::installBuiltinFont($name);
            }
        }
        return $name;
    }

    /**
     * @param string $name
     * @return string
     * @see http://www.tcpdf.org/doc/code/classTCPDF__FONTS.html
     */
    static public function installBuiltinFont($name)
    {
        $filename = self::getBuiltinFontPath($name);

        $font_name = \TCPDF_FONTS::addTTFFont($filename, 'TrueTypeUnicode', '', 32);
        static::$installed_builtin_fonts[$name] = $font_name;

        return $font_name;
    }

    /**
     * @param string $name
     * @return boolean
     */
    static public function isInstalledFont($name)
    {
        return array_key_exists($name, static::$installed_builtin_fonts);
    }

    /**
     * @param string $name
     * @return string
     */
    static public function getBuiltinFontPath($name)
    {
        $font_directory = realpath(__DIR__ . self::STORE_PATH);
        return $font_directory . '/' . self::$builtin_unicode_fonts[$name];
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
