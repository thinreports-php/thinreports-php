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
trait Font
{
    /**
     * @var string[]
     */
    static private $installed_builtin_fonts = [];

    static private $builtin_unicode_fonts = [
        'IPAMincho'  => 'ipam.ttf',
        'IPAPMincho' => 'ipamp.ttf',
        'IPAGothic'  => 'ipag.ttf',
        'IPAPGothic' => 'ipagp.ttf'
    ];

    static private $builtin_font_aliases = [
        'Courier New'     => 'Courier',
        'Times New Roman' => 'Times'
    ];

    /**
     * @param string $name
     * @return string
     */
    public function getFontName($name)
    {
        if (array_key_exists($name, self::$builtin_font_aliases)) {
            return self::$builtin_font_aliases[$name];
        }

        if (array_key_exists($name, self::$builtin_unicode_fonts)) {
            if ($this->isInstalledFont($name)) {
                return static::$installed_builtin_fonts[$name];
            } else {
                return $this->installBuiltinFont($name);
            }
        }
        return $name;
    }

    /**
     * @param string $name
     * @return string
     * @see http://www.tcpdf.org/doc/code/classTCPDF__FONTS.html
     */
    public function installBuiltinFont($name)
    {
        $filename = $this->getBuiltinFontPath($name);

        $font_name = \TCPDF_FONTS::addTTFFont($filename, 'TrueTypeUnicode', '', 32);
        static::$installed_builtin_fonts[$name] = $font_name;

        return $font_name;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function isInstalledFont($name)
    {
        return array_key_exists($name, static::$installed_builtin_fonts);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getBuiltinFontPath($name)
    {
        $font_directory = realpath(__DIR__ . '/../../../../fonts');
        return $font_directory . '/' . self::$builtin_unicode_fonts[$name];
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function isBuiltinUnicodeFont($name)
    {
        return in_array($name, array_keys(static::$builtin_unicode_fonts));
    }
}
