<?php
namespace Thinreports\Generator\PDF;

use Thinreports\TestCase;

class FontTest extends TestCase
{
    function setup()
    {
        Font::$installed_builtin_fonts = array();
    }

    function test_init()
    {
        Font::init();

        $this->assertEquals(
            array(
                'IPAMincho',
                'IPAPMincho',
                'IPAGothic',
                'IPAPGothic'
            ),
            array_keys(Font::$installed_builtin_fonts)
        );
    }

    function test_getFontName()
    {
        $this->assertEquals('Helvetica', Font::getFontName('Helvetica'));

        $this->assertEquals('Courier', Font::getFontName('Courier New'));
        $this->assertEquals('Times', Font::getFontName('Times New Roman'));

        $this->assertFalse(Font::isInstalledFont('IPAMincho'));
        $this->assertNotContains('ipam', Font::$installed_builtin_fonts);

        $this->assertEquals('ipam', Font::getFontName('IPAMincho'));

        $this->assertTrue(Font::isInstalledFont('IPAMincho'));
        $this->assertContains('ipam', Font::$installed_builtin_fonts);

        $this->assertEquals('ipam', Font::getFontName('IPAMincho'));
    }

    /**
     * @dataProvider unicodeFontProvider
     */
    function test_installBuiltinFont($expected_result, $font_name)
    {
        $actual = Font::installBuiltinFont($font_name);

        $this->assertEquals($expected_result, $actual);
        $this->assertContains($actual, Font::$installed_builtin_fonts);
    }
    function unicodeFontProvider()
    {
        return array(
            array('ipam', 'IPAMincho'),
            array('ipag', 'IPAGothic'),
            array('ipamp', 'IPAPMincho'),
            array('ipagp', 'IPAPGothic')
        );
    }

    function test_isBuiltinUnicodeFont()
    {
        $this->assertFalse(Font::isBuiltinUnicodeFont('unknown font'));
        $this->assertFalse(Font::isBuiltinUnicodeFont('Helvetica'));
        $this->assertTrue(Font::isBuiltinUnicodeFont('IPAGothic'));
    }

    function test_isInstalledFont()
    {
        $this->assertFalse(Font::isInstalledFont('IPAMincho'));
        Font::$installed_builtin_fonts['IPAMincho'] = 'ipam';
        $this->assertTrue(Font::isInstalledFont('IPAMincho'));
    }
}
