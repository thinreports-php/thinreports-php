<?php
namespace Thinreports\Item\Style;

use Thinreports\TestCase;

class GraphicStyleTest extends TestCase
{
    private function newGraphicStyle(array $svg_attrs = array())
    {
        return new GraphicStyle(array('svg' => array('attrs' => $svg_attrs)));
    }

    function test_available_style_names()
    {
        $this->assertAttributeEquals(
            array('border_color', 'border_width', 'border', 'fill_color'),
            'available_style_names', 'Thinreports\Item\Style\GraphicStyle'
        );
    }

    function test_set_border_width()
    {
        foreach (array(null, '0', 0) as $width) {
            $test_style = $this->newGraphicStyle(array(
                'stroke-opacity' => '1',
                'stroke-width' => '1'
            ));
            $test_style->set_border_width($width);

            $styles = $test_style->export();

            $this->assertEquals('1', $styles['stroke-opacity']);
            $this->assertSame($width, $styles['stroke-width']);
        }
    }

    function test_get_border_width()
    {
        $test_style = $this->newGraphicStyle(array('stroke-width' => '5'));
        $this->assertEquals('5', $test_style->get_border_width());

        $test_style = $this->newGraphicStyle(array());
        $this->assertNull($test_style->get_border_width());
    }

    function test_set_border_color()
    {
        $test_style = $this->newGraphicStyle(array('stroke' => 'red'));

        $test_style->set_border_color('#ff0000');

        $styles = $test_style->export();
        $this->assertEquals('#ff0000', $styles['stroke']);
    }

    function test_get_border_color()
    {
        $test_style = $this->newGraphicStyle(array('stroke' => '#0000ff'));
        $this->assertEquals('#0000ff', $test_style->get_border_color());
    }

    function test_set_border()
    {
        $test_style = $this->newGraphicStyle(array(
            'stroke-width' => '1',
            'stroke' => '#000000'
        ));
        $test_style->set_border(array(1, 'red'));

        $styles = $test_style->export();
        $this->assertEquals(1, $styles['stroke-width']);
        $this->assertEquals('red', $styles['stroke']);
    }

    function test_get_border()
    {
        $test_style = $this->newGraphicStyle(array(
            'stroke-width' => '3',
            'stroke' => 'blue'
        ));

        $this->assertEquals(array('3', 'blue'), $test_style->get_border());
    }

    function test_set_fill_color()
    {
        $test_style = $this->newGraphicStyle(array('fill' => '#000000'));
        $test_style->set_fill_color('red');

        $styles = $test_style->export();
        $this->assertEquals('red', $styles['fill']);
    }

    function test_get_fill_color()
    {
        $test_style = $this->newGraphicStyle(array('fill' => 'blue'));
        $this->assertEquals('blue', $test_style->get_fill_color());
    }
}
