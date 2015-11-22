<?php
namespace Thinreports\Item;

use Thinreports\TestCase;
use Thinreports\Report;
use Thinreports\Page\Page;

class BasicItemTest extends TestCase
{
    private $page;

    function setup()
    {
        $report = new Report($this->dataLayoutFile('empty_A4P.tlf'));
        $this->page = $report->addPage();
    }

    private function newBasicItem($format_data_name)
    {
        return new BasicItem($this->page, $this->dataItemFormat($format_data_name));
    }

    function test_initialize()
    {
        $item = $this->newBasicItem('image');
        $this->assertAttributeInstanceOf('Thinreports\Item\Style\BasicStyle',
            'style', $item);

        $item = $this->newBasicItem('text');
        $this->assertAttributeInstanceOf('Thinreports\Item\Style\TextStyle',
            'style', $item);

        foreach (array('line', 'rect', 'ellipse') as $format_data_name) {
            $item = $this->newBasicItem($format_data_name);
            $this->assertAttributeInstanceOf('Thinreports\Item\Style\GraphicStyle',
                'style', $item);
        }
    }

    function test_getBounds()
    {
        $item = $this->newBasicItem('image');
        $item_format = $this->dataItemFormat('image');
        $attrs = $item_format['svg']['attrs'];

        $this->assertEquals(array(
            'x'      => $attrs['x'],
            'y'      => $attrs['y'],
            'width'  => $attrs['width'],
            'height' => $attrs['height']
        ), $item->getBounds());

        $item = $this->newBasicItem('rect');
        $item_format = $this->dataItemFormat('rect');
        $attrs = $item_format['svg']['attrs'];

        $this->assertEquals(array(
            'x'      => $attrs['x'],
            'y'      => $attrs['y'],
            'width'  => $attrs['width'],
            'height' => $attrs['height']
        ), $item->getBounds());

        $item = $this->newBasicItem('text');
        $item_format = $this->dataItemFormat('text');
        $box = $item_format['box'];

        $this->assertEquals($box, $item->getBounds());

        $item = $this->newBasicItem('ellipse');
        $item_format = $this->dataItemFormat('ellipse');
        $attrs = $item_format['svg']['attrs'];

        $this->assertEquals(array(
            'cx' => $attrs['cx'],
            'cy' => $attrs['cy'],
            'rx' => $attrs['rx'],
            'ry' => $attrs['ry']
        ), $item->getBounds());

        $item = $this->newBasicItem('line');
        $item_format = $this->dataItemFormat('line');
        $attrs = $item_format['svg']['attrs'];

        $this->assertEquals(array(
            'x1' => $attrs['x1'],
            'y1' => $attrs['y1'],
            'x2' => $attrs['x2'],
            'y2' => $attrs['y2']
        ), $item->getBounds());
    }

    function test_isTypeOf()
    {
        $item = $this->newBasicItem('rect');

        $this->assertTrue($item->isTypeOf('basic'));
        $this->assertTrue($item->isTypeOf('s-rect'));
    }

    function test_isImage()
    {
        $item = $this->newBasicItem('image');
        $this->assertTrue($item->isImage());

        $this->assertFalseIn(array('rect', 'ellipse', 'line', 'text'), 'isImage');
    }

    function test_isText()
    {
        $item = $this->newBasicItem('text');
        $this->assertTrue($item->isText());

        $this->assertFalseIn(array('rect', 'ellipse', 'line', 'image'), 'isText');
    }

    function test_isRect()
    {
        $item = $this->newBasicItem('rect');
        $this->assertTrue($item->isRect());

        $this->assertFalseIn(array('ellipse', 'line', 'image', 'text'), 'isRect');
    }

    function test_isEllipse()
    {
        $item = $this->newBasicItem('ellipse');
        $this->assertTrue($item->isEllipse());

        $this->assertFalseIn(array('rect', 'line', 'image', 'text'), 'isEllipse');
    }

    function test_isLine()
    {
        $item = $this->newBasicItem('line');
        $this->assertTrue($item->isLine());

        $this->assertFalseIn(array('rect', 'ellipse', 'image', 'text'), 'isLine');
    }

    private function assertFalseIn($format_data_names, $method_name)
    {
        foreach ($format_data_names as $format_data_name) {
            $item = $this->newBasicItem($format_data_name);
            $this->assertFalse($item->$method_name());
        }
    }
}
