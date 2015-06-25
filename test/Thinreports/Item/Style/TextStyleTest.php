<?php
namespace Thinreports\Item\Style;

use Thinreports\TestCase;
use Thinreports\Exception;

class TextStyleTest extends TestCase
{
    private function newTextStyle($valign = null, array $svg_attrs = [])
    {
        return new TextStyle([
            'valign' => $valign,
            'svg'    => ['attrs' => $svg_attrs]
        ]);
    }

    function test_available_style_names()
    {
        $this->assertAttributeEquals(
            ['bold', 'italic', 'underline', 'linethrough',
             'align', 'valign', 'color', 'font_size'],
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
        $test_style = $this->newTextStyle(null, ['fill' => 'red']);

        $test_style->set_color('#ff0000');
        $this->assertEquals('#ff0000', $test_style->export()['fill']);
    }

    function test_get_color()
    {
        $test_style = $this->newTextStyle(null, ['fill' => 'blue']);
        $this->assertEquals('blue', $test_style->get_color());
    }

    function test_set_font_size()
    {
        $test_style = $this->newTextStyle(null, ['font-size' => '18']);

        $test_style->set_font_size(20);
        $this->assertEquals(20, $test_style->export()['font-size']);
    }

    function test_get_font_size()
    {
        $test_style = $this->newTextStyle(null, ['font-size' => '12']);
        $this->assertEquals('12', $test_style->get_font_size());
    }

    /**
     * @covers TextStyle::set_bold
     * @covers TextStyle::get_bold
     */
    function test_bold()
    {
        $test_style = $this->newTextStyle(null, ['font-weight' => 'normal']);
        $this->assertFalse($test_style->get_bold());

        $test_style->set_bold(true);

        $this->assertEquals('bold', $test_style->export()['font-weight']);
        $this->assertTrue($test_style->get_bold());

        $test_style->set_bold(false);

        $this->assertEquals('normal', $test_style->export()['font-weight']);
        $this->assertFalse($test_style->get_bold());
    }

    function test_italic()
    {
        $test_style =$this->newTextStyle(null, ['font-style' => 'normal']);
        $this->assertFalse($test_style->get_italic());

        $test_style->set_italic(true);

        $this->assertEquals('italic', $test_style->export()['font-style']);
        $this->assertTrue($test_style->get_italic());

        $test_style->set_italic(false);

        $this->assertEquals('normal', $test_style->export()['font-style']);
        $this->assertFalse($test_style->get_italic());
    }

    function test_underline()
    {
        $test_style = $this->newTextStyle(null,
                        ['text-decoration' => 'underline']);
        $this->assertTrue($test_style->get_underline());

        $test_style->set_underline(false);
        $this->assertEquals('none', $test_style->export()['text-decoration']);
        $this->assertFalse($test_style->get_underline());

        $test_style = $this->newTextStyle(null,
                        ['text-decoration' => 'line-through underline']);
        $this->assertTrue($test_style->get_underline());

        $test_style->set_underline(false);
        $this->assertEquals('line-through', $test_style->export()['text-decoration']);
        $this->assertFalse($test_style->get_underline());

        $test_style = $this->newTextStyle(null,
                        ['text-decoration' => 'line-through']);
        $this->assertFalse($test_style->get_underline());

        $test_style->set_underline(true);
        $this->assertEquals('underline line-through', $test_style->export()['text-decoration']);
        $this->assertTrue($test_style->get_underline());

        $test_style = $this->newTextStyle(null,
                        ['text-decoration' => 'none']);
        $this->assertFalse($test_style->get_underline());

        $test_style->set_underline(true);
        $this->assertEquals('underline', $test_style->export()['text-decoration']);
        $this->assertTrue($test_style->get_underline());
    }

    function test_linethrough()
    {
        $test_style = $this->newTextStyle(null,
                        ['text-decoration' => 'line-through']);
        $this->assertTrue($test_style->get_linethrough());

        $test_style->set_linethrough(false);
        $this->assertFalse($test_style->get_linethrough());
        $this->assertEquals('none', $test_style->export()['text-decoration']);
    }

    function test_set_align()
    {
        $test_style = $this->newTextStyle(null, ['text-anchor' => 'start']);

        try {
            $test_style->set_align('unavailable_value');
            $this->fail();
        } catch (Exception\UnavailableStyleValue $e) {
            // OK
        }

        $test_style->set_align('left');
        $this->assertEquals('start', $test_style->export()['text-anchor']);

        $test_style->set_align('center');
        $this->assertEquals('middle', $test_style->export()['text-anchor']);

        $test_style->set_align('right');
        $this->assertEquals('end', $test_style->export()['text-anchor']);
    }

    function test_get_align()
    {
        $test_style = $this->newTextStyle(null, ['text-anchor' => '']);
        $this->assertEquals('left', $test_style->get_align());

        $test_style = $this->newTextStyle(null, ['text-anchor' => 'start']);
        $this->assertEquals('left', $test_style->get_align());

        $test_style = $this->newTextStyle(null, ['text-anchor' => 'middle']);
        $this->assertEquals('center', $test_style->get_align());

        $test_style = $this->newTextStyle(null, ['text-anchor' => 'end']);
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
