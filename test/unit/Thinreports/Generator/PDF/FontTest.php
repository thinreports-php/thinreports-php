<?php
namespace Thinreports\Generator\PDF;

use Thinreports\TestCase;

class FontTest extends TestCase
{
    function test_getFontName()
    {
        $this->assertEquals('Helvetica', Font::getFontName('Helvetica'));

        $this->assertEquals('Courier', Font::getFontName('Courier New'));
        $this->assertEquals('Times', Font::getFontName('Times New Roman'));

        $this->assertEquals('ipam', Font::getFontName('IPAMincho'));
        $this->assertEquals('ipamp', Font::getFontName('IPAPMincho'));
        $this->assertEquals('ipag', Font::getFontName('IPAGothic'));
        $this->assertEquals('ipagp', Font::getFontName('IPAPGothic'));
    }

    function test_isBuiltinUnicodeFont()
    {
        $this->assertFalse(Font::isBuiltinUnicodeFont('unknown font'));
        $this->assertFalse(Font::isBuiltinUnicodeFont('Helvetica'));
        $this->assertTrue(Font::isBuiltinUnicodeFont('IPAGothic'));
    }
}
