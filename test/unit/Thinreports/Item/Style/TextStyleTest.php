<?php
namespace Thinreports\Item\Style;

use Thinreports\TestCase;
use Thinreports\Exception;

class TextStyleTest extends TestCase
{
    function test_available_style_names()
    {
        $this->assertAttributeEquals(
            array(
                'bold', 'italic', 'underline', 'linethrough',
                'align', 'valign', 'color', 'font_size'
            ),
            'available_style_names', 'Thinreports\Item\Style\TextStyle'
        );
    }

    function test_set_color()
    {
        $test_style = new TextStyle(array('color' => 'none'));
        $test_style->set_color('#ff0000');

        $this->assertAttributeEquals(array('color' => '#ff0000'), 'styles', $test_style);
    }

    function test_get_color()
    {
        $test_style = new TextStyle(array('color' => 'none'));

        $this->assertEquals('none', $test_style->get_color());
    }

    function test_set_font_size()
    {
        $test_style = new TextStyle(array('font-size' => 1));
        $test_style->set_font_size(15.0);

        $this->assertAttributeEquals(array('font-size' => 15.0), 'styles', $test_style);
    }

    function test_get_font_size()
    {
        $test_style = new TextStyle(array('font-size' => 18.0));

        $this->assertEquals(18.0, $test_style->get_font_size());
    }

    function test_set_bold()
    {
        $test_style = new TextStyle(array('font-style' => array('italic')));
        $test_style->set_bold(true);

        $this->assertAttributeEquals(array('font-style' => array('italic', 'bold')), 'styles', $test_style);

        $test_style->set_bold(false);

        $this->assertAttributeEquals(array('font-style' => array('italic')), 'styles', $test_style);
    }

    function test_get_bold()
    {
        $test_style = new TextStyle(array('font-style' => array('bold')));
        $this->assertTrue($test_style->get_bold());

        $test_style = new TextStyle(array('font-style' => array()));
        $this->assertFalse($test_style->get_bold());
    }

    function test_set_italic()
    {
        $test_style = new TextStyle(array('font-style' => array()));
        $test_style->set_italic(true);

        $this->assertAttributeEquals(array('font-style' => array('italic')), 'styles', $test_style);

        $test_style->set_italic(false);

        $this->assertAttributeEquals(array('font-style' => array()), 'styles', $test_style);
    }

    function test_get_italic()
    {
        $test_style = new TextStyle(array('font-style' => array('bold', 'italic')));
        $this->assertTrue($test_style->get_italic());

        $test_style = new TextStyle(array('font-style' => array('bold')));
        $this->assertFalse($test_style->get_italic());
    }

    function test_set_underline()
    {
        $test_style = new TextStyle(array('font-style' => array('bold')));
        $test_style->set_underline(true);

        $this->assertAttributeEquals(array('font-style' => array('bold', 'underline')), 'styles', $test_style);

        $test_style->set_underline(false);

        $this->assertAttributeEquals(array('font-style' => array('bold')), 'styles', $test_style);
    }

    function test_get_underline()
    {
        $test_style = new TextStyle(array('font-style' => array('underline')));
        $this->assertTrue($test_style->get_underline());

        $test_style = new TextStyle(array('font-style' => array()));
        $this->assertFalse($test_style->get_underline());
    }

    function test_set_linethrough()
    {
        $test_style = new TextStyle(array('font-style' => array('bold')));
        $test_style->set_linethrough(true);

        $this->assertAttributeEquals(array('font-style' => array('bold', 'linethrough')), 'styles', $test_style);

        $test_style->set_linethrough(false);

        $this->assertAttributeEquals(array('font-style' => array('bold')), 'styles', $test_style);
    }

    function test_get_linethrough()
    {
        $test_style = new TextStyle(array('font-style' => array('linethrough')));
        $this->assertTrue($test_style->get_linethrough());

        $test_style = new TextStyle(array('font-style' => array()));
        $this->assertFalse($test_style->get_linethrough());
    }

    function test_set_align()
    {
        $test_style = new TextStyle(array('text-align' => ''));

        try {
            $test_style->set_align('unavailable_value');
            $this->fail();
        } catch (Exception\UnavailableStyleValue $e) {
            // OK
        }

        $test_style->set_align('right');

        $this->assertAttributeEquals(array('text-align' => 'right'), 'styles', $test_style);
    }

    function test_get_align()
    {
        $test_style = new TextStyle(array('text-align' => ''));

        $this->assertEquals('left', $test_style->get_align());

        $test_style = new TextStyle(array('text-align' => 'right'));

        $this->assertEquals('right', $test_style->get_align());
    }

    function test_set_valign()
    {
        $test_style = new TextStyle(array('vertical-align' => ''));

        try {
            $test_style->set_valign('unavailable_value');
            $this->fail();
        } catch (Exception\UnavailableStyleValue $e) {
            // OK
        }

        $test_style->set_valign('top');

        $this->assertAttributeEquals(array('vertical-align' => 'top'), 'styles', $test_style);
    }

    function test_get_valign()
    {
        $test_style = new TextStyle(array('vertical-align' => ''));

        $this->assertEquals('top', $test_style->get_valign());

        $test_style = new TextStyle(array('vertical-align' => 'bottom'));

        $this->assertEquals('bottom', $test_style->get_valign());
    }
}
