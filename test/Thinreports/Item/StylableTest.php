<?php
namespace Thinreports\Item;

use Thinreports\TestCase;
use Thinreports\Item\Style\GraphicStyle;

class TestStylableItem
{
    use Stylable;

    public function __construct(&$style)
    {
        $this->style = $style;
    }
}

class StylableTest extends TestCase
{
    private $item;
    private $style;

    function setup()
    {
        $this->style = new GraphicStyle($this->dataItemFormat('rect', 'default'));
        $this->item  = new TestStylableItem($this->style);
    }

    function test_setStyle()
    {
        $this->item->setStyle('fill_color', 'red');

        $this->assertEquals($this->style->get_fill_color(),
            $this->item->getStyle('fill_color'));
    }

    function test_getStyle()
    {
        $this->item->setStyle('fill_color', 'yellow');

        $this->assertEquals('yellow', $this->item->getStyle('fill_color'));
    }

    function test_setStyles()
    {
        $this->item->setStyles(['fill_color' => 'black', 'border_width' => 3]);

        $this->assertEquals('black', $this->style->get_fill_color());
        $this->assertEquals(3, $this->style->get_border_width());
    }

    function test_exportStyles()
    {
        $this->assertEquals($this->style->export(), $this->item->exportStyles());

        $this->item->setStyle('fill_color', 'blue');

        $this->assertEquals($this->style->export(), $this->item->exportStyles());
    }
}
