<?php
namespace Thinreports\Item\Style;

use Thinreports\TestCase;
use Thinreports\Exception;

class TextStyleTest extends TestCase
{
    private function newTextStyle($valign = null, array $svg_attrs = array())
    {
        return new TextStyle(array(
            'valign' => $valign,
            'svg'    => array('attrs' => $svg_attrs)
        ));
    }

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

    function test_initialize()
    {
        $test_style = $this->newTextStyle('');
        $this->assertAttributeEquals('top', 'vertical_align', $test_style);

        $test_style = $this->newTextStyle('bottom');
        $this->assertAttributeEquals('bottom', 'vertical_align', $test_style);
    }

    function test_set_color()
    {
        $test_style = $this->newTextStyle(null, array('fill' => 'red'));
        $test_style->set_color('#ff0000');

        $styles = $test_style->export();
        $this->assertEquals('#ff0000', $styles['fill']);
    }

    function test_get_color()
    {
        $test_style = $this->newTextStyle(null, array('fill' => 'blue'));
        $this->assertEquals('blue', $test_style->get_color());
    }

    function test_set_font_size()
    {
        $test_style = $this->newTextStyle(null, array('font-size' => '18'));
        $test_style->set_font_size(20);

        $styles = $test_style->export();
        $this->assertEquals(20, $styles['font-size']);
    }

    function test_get_font_size()
    {
        $test_style = $this->newTextStyle(null, array('font-size' => '12'));
        $this->assertEquals('12', $test_style->get_font_size());
    }

    /**
     * Tests for:
     *      TextStyle::set_bold
     *      TextStyle::get_bold
     */
    function test_bold()
    {
        $test_style = $this->newTextStyle(null, array('font-weight' => 'normal'));
        $this->assertFalse($test_style->get_bold());

        $test_style->set_bold(true);

        $styles = $test_style->export();
        $this->assertEquals('bold', $styles['font-weight']);
        $this->assertTrue($test_style->get_bold());

        $test_style->set_bold(false);

        $styles = $test_style->export();
        $this->assertEquals('normal', $styles['font-weight']);
        $this->assertFalse($test_style->get_bold());
    }

    function test_italic()
    {
        $test_style =$this->newTextStyle(null, array('font-style' => 'normal'));
        $this->assertFalse($test_style->get_italic());

        $test_style->set_italic(true);

        $styles = $test_style->export();
        $this->assertEquals('italic', $styles['font-style']);
        $this->assertTrue($test_style->get_italic());

        $test_style->set_italic(false);

        $styles = $test_style->export();
        $this->assertEquals('normal', $styles['font-style']);
        $this->assertFalse($test_style->get_italic());
    }

    function test_underline()
    {
        $test_style = $this->newTextStyle(null,
                        array('text-decoration' => 'underline'));
        $this->assertTrue($test_style->get_underline());

        $test_style->set_underline(false);

        $styles = $test_style->export();
        $this->assertEquals('none', $styles['text-decoration']);
        $this->assertFalse($test_style->get_underline());

        $test_style = $this->newTextStyle(null,
                        array('text-decoration' => 'line-through underline'));
        $this->assertTrue($test_style->get_underline());

        $test_style->set_underline(false);

        $styles = $test_style->export();
        $this->assertEquals('line-through', $styles['text-decoration']);
        $this->assertFalse($test_style->get_underline());

        $test_style = $this->newTextStyle(null,
                        array('text-decoration' => 'line-through'));
        $this->assertFalse($test_style->get_underline());

        $test_style->set_underline(true);

        $styles = $test_style->export();
        $this->assertEquals('underline line-through', $styles['text-decoration']);
        $this->assertTrue($test_style->get_underline());

        $test_style = $this->newTextStyle(null,
                        array('text-decoration' => 'none'));
        $this->assertFalse($test_style->get_underline());

        $test_style->set_underline(true);

        $styles = $test_style->export();
        $this->assertEquals('underline', $styles['text-decoration']);
        $this->assertTrue($test_style->get_underline());
    }

    function test_linethrough()
    {
        $test_style = $this->newTextStyle(null,
                        array('text-decoration' => 'line-through'));
        $this->assertTrue($test_style->get_linethrough());

        $test_style->set_linethrough(false);

        $styles = $test_style->export();
        $this->assertFalse($test_style->get_linethrough());
        $this->assertEquals('none', $styles['text-decoration']);
    }

    function test_set_align()
    {
        $test_style = $this->newTextStyle(null, array('text-anchor' => 'start'));

        try {
            $test_style->set_align('unavailable_value');
            $this->fail();
        } catch (Exception\UnavailableStyleValue $e) {
            // OK
        }

        $test_style->set_align('left');

        $styles = $test_style->export();
        $this->assertEquals('start', $styles['text-anchor']);

        $test_style->set_align('center');

        $styles = $test_style->export();
        $this->assertEquals('middle', $styles['text-anchor']);

        $test_style->set_align('right');

        $styles = $test_style->export();
        $this->assertEquals('end', $styles['text-anchor']);
    }

    function test_get_align()
    {
        $test_style = $this->newTextStyle(null, array('text-anchor' => ''));
        $this->assertEquals('left', $test_style->get_align());

        $test_style = $this->newTextStyle(null, array('text-anchor' => 'start'));
        $this->assertEquals('left', $test_style->get_align());

        $test_style = $this->newTextStyle(null, array('text-anchor' => 'middle'));
        $this->assertEquals('center', $test_style->get_align());

        $test_style = $this->newTextStyle(null, array('text-anchor' => 'end'));
        $this->assertEquals('right', $test_style->get_align());
    }

    function test_set_valign()
    {
        $test_style = $this->newTextStyle('top');

        try {
            $test_style->set_valign('unavailable_value');
            $this->fail();
        } catch (Exception\UnavailableStyleValue $e) {
            // OK
        }

        $test_style->set_valign('bottom');
        $this->assertAttributeEquals('bottom', 'vertical_align', $test_style);
    }

    function test_get_valign()
    {
        $test_style = $this->newTextStyle('center');
        $this->assertEquals('center', $test_style->get_valign());
    }
}
