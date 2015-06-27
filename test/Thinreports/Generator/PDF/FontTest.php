<?php
namespace Thinreports\Generator\PDF;

use Thinreports\TestCase;

class TestFont
{
    use Font;

    static public function _getInstalledBuiltinFonts()
    {
        return static::$installed_builtin_fonts;
    }

    static public function _addInstalledBuiltinFont($font_name)
    {
        static::$installed_builtin_fonts[$font_name] = 'ipam';
    }

    static public function _resetInstalledBuiltinFonts()
    {
        static::$installed_builtin_fonts = [];
    }
}

class FontTest extends TestCase
{
    function setup()
    {
        TestFont::_resetInstalledBuiltinFonts();
    }

    function test_getFontName()
    {
        $test_font = new TestFont();

        $this->assertEquals('Courier', $test_font->getFontName('Courier New'));
        $this->assertEquals('Times', $test_font->getFontName('Times New Roman'));

        $this->assertFalse($test_font->isInstalledFont('IPAMincho'));
        $this->assertNotContains('ipam', TestFont::_getInstalledBuiltinFonts());

        $this->assertEquals('ipam', $test_font->getFontName('IPAMincho'));

        $this->assertTrue($test_font->isInstalledFont('IPAMincho'));
        $this->assertContains('ipam', TestFont::_getInstalledBuiltinFonts());

        $this->assertEquals('ipam', $test_font->getFontName('IPAMincho'));
    }

    /**
     * @dataProvider unicodeFontProvider
     */
    function test_installBuiltinFont($expected_result, $font_name)
    {
        $test_font = new TestFont();

        $actual = $test_font->installBuiltinFont($font_name);

        $this->assertEquals($expected_result, $actual);
        $this->assertContains($actual, $test_font->_getInstalledBuiltinFonts());
    }
    function unicodeFontProvider()
    {
        return [
            ['ipam', 'IPAMincho'],
            ['ipag', 'IPAGothic'],
            ['ipamp', 'IPAPMincho'],
            ['ipagp', 'IPAPGothic']
        ];
    }

    function test_isBuiltinUnicodeFont()
    {
        $test_font = new TestFont();

        $this->assertFalse($test_font->isBuiltinUnicodeFont('unknown font'));
        $this->assertFalse($test_font->isBuiltinUnicodeFont('Helvetica'));
        $this->assertTrue($test_font->isBuiltinUnicodeFont('IPAGothic'));
    }

    function test_isInstalledFont()
    {
        $test_font = new TestFont();

        $this->assertFalse($test_font->isInstalledFont('IPAMincho'));
        TestFont::_addInstalledBuiltinFont('IPAMincho');
        $this->assertTrue($test_font->isInstalledFont('IPAMincho'));
    }
}
