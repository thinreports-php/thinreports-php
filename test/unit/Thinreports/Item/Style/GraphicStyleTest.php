<?php
namespace Thinreports\Item\Style;

use Thinreports\TestCase;

class GraphicStyleTest extends TestCase
{
    function test_available_style_names()
    {
        $this->assertAttributeEquals(
            array('border_color', 'border_width', 'border', 'fill_color'),
            'available_style_names', 'Thinreports\Item\Style\GraphicStyle'
        );
    }

    function test_set_border_width()
    {
        $test_style = new GraphicStyle(array('border-width' => 1));
        $test_style->set_border_width(999.9);

        $this->assertAttributeEquals(array('border-width' => 999.9), 'styles', $test_style);
    }

    function test_get_border_width()
    {
        $test_style = new GraphicStyle(array('border-width' => 999));

        $this->assertEquals(999, $test_style->get_border_width());
    }

    function test_set_border_color()
    {
        $test_style = new GraphicStyle(array('border-color' => 'none'));
        $test_style->set_border_color('#000000');

        $this->assertAttributeEquals(array('border-color' => '#000000'), 'styles', $test_style);
    }

    function test_get_border_color()
    {
        $test_style = new GraphicStyle(array('border-color' => 'red'));

        $this->assertEquals('red', $test_style->get_border_color());
    }

    function test_set_border()
    {
        $test_style = new GraphicStyle(array('border-color' => 'none', 'border-width' => 1));
        $test_style->set_border(array(9, '#ffffff'));

        $this->assertAttributeEquals(array('border-color' => '#ffffff', 'border-width' => 9), 'styles', $test_style);
    }

    function test_get_border()
    {
        $test_style = new GraphicStyle(array('border-color' => 'none', 'border-width' => 1.0));

        $this->assertEquals(array(1.0, 'none'), $test_style->get_border());
    }

    function test_set_fill_color()
    {
        $test_style = new GraphicStyle(array('fill-color' => 'none'));
        $test_style->set_fill_color('#000000');

        $this->assertAttributeEquals(array('fill-color' => '#000000'), 'styles', $test_style);
    }

    function test_get_fill_color()
    {
        $test_style = new GraphicStyle(array('fill-color' => 'blue'));

        $this->assertEquals('blue', $test_style->get_fill_color());
    }
}
