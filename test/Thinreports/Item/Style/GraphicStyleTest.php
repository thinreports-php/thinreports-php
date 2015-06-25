<?php
namespace Thinreports\Item\Style;

use Thinreports\TestCase;

class GraphicStyleTest extends TestCase
{
    private function newGraphicStyle(array $svg_attrs = [])
    {
        return new GraphicStyle(['svg' => ['attrs' => $svg_attrs]]);
    }

    function test_available_style_names()
    {
        $this->assertAttributeEquals(
            ['border_color', 'border_width', 'border', 'fill_color'],
            'available_style_names', 'Thinreports\Item\Style\GraphicStyle'
        );
    }

    function test_set_border_width()
    {
        foreach ([null, '0', 0] as $width) {
            $test_style = $this->newGraphicStyle([
                'stroke-opacity' => '1',
                'stroke-width' => '1'
            ]);
            $test_style->set_border_width($width);

            $styles = $test_style->export();

            $this->assertEquals('1', $styles['stroke-opacity']);
            $this->assertSame($width, $styles['stroke-width']);
        }
    }

    function test_get_border_width()
    {
        $test_style = $this->newGraphicStyle(['stroke-width' => '5']);
        $this->assertEquals('5', $test_style->get_border_width());

        $test_style = $this->newGraphicStyle([]);
        $this->assertNull($test_style->get_border_width());
    }

    function test_set_border_color()
    {
        $test_style = $this->newGraphicStyle(['stroke' => 'red']);

        $test_style->set_border_color('#ff0000');
        $this->assertEquals('#ff0000', $test_style->export()['stroke']);
    }

    function test_get_border_color()
    {
        $test_style = $this->newGraphicStyle(['stroke' => '#0000ff']);
        $this->assertEquals('#0000ff', $test_style->get_border_color());
    }

    function test_set_border()
    {
        $test_style = $this->newGraphicStyle([
            'stroke-width' => '1',
            'stroke' => '#000000'
        ]);
        $test_style->set_border([1, 'red']);

        $this->assertEquals(1, $test_style->export()['stroke-width']);
        $this->assertEquals('red', $test_style->export()['stroke']);
    }

    function test_get_border()
    {
        $test_style = $this->newGraphicStyle([
            'stroke-width' => '3',
            'stroke' => 'blue'
        ]);

        $this->assertEquals(['3', 'blue'], $test_style->get_border());
    }

    function test_set_fill_color()
    {
        $test_style = $this->newGraphicStyle(['fill' => '#000000']);

        $test_style->set_fill_color('red');
        $this->assertEquals('red', $test_style->export()['fill']);
    }

    function test_get_fill_color()
    {
        $test_style = $this->newGraphicStyle(['fill' => 'blue']);
        $this->assertEquals('blue', $test_style->get_fill_color());
    }
}
