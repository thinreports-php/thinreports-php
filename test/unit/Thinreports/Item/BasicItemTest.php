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

    private function newBasicItem($schema_data_name)
    {
        return new BasicItem($this->page, $this->dataItemFormat($schema_data_name));
    }

    function test_initialize()
    {
        $item = $this->newBasicItem('image');
        $this->assertAttributeInstanceOf('Thinreports\Item\Style\BasicStyle',
            'style', $item);

        $item = $this->newBasicItem('text');
        $this->assertAttributeInstanceOf('Thinreports\Item\Style\TextStyle',
            'style', $item);

        foreach (array('line', 'rect', 'ellipse') as $schema_data_name) {
            $item = $this->newBasicItem($schema_data_name);
            $this->assertAttributeInstanceOf('Thinreports\Item\Style\GraphicStyle',
                'style', $item);
        }
    }

    function test_getBounds()
    {
        $item = $this->newBasicItem('image');
        $item_schema = $this->dataItemFormat('image');

        $this->assertEquals(array(
            'x' => $item_schema['x'],
            'y' => $item_schema['y'],
            'width' => $item_schema['width'],
            'height' => $item_schema['height']
        ), $item->getBounds());

        $item = $this->newBasicItem('rect');
        $item_schema = $this->dataItemFormat('rect');

        $this->assertEquals(array(
            'x' => $item_schema['x'],
            'y' => $item_schema['y'],
            'width' => $item_schema['width'],
            'height' => $item_schema['height']
        ), $item->getBounds());

        $item = $this->newBasicItem('text');
        $item_schema = $this->dataItemFormat('text');

        $this->assertEquals(array(
            'x' => $item_schema['x'],
            'y' => $item_schema['y'],
            'width' => $item_schema['width'],
            'height' => $item_schema['height']
        ), $item->getBounds());

        $item = $this->newBasicItem('ellipse');
        $item_schema = $this->dataItemFormat('ellipse');

        $this->assertEquals(array(
            'cx' => $item_schema['cx'],
            'cy' => $item_schema['cy'],
            'rx' => $item_schema['rx'],
            'ry' => $item_schema['ry']
        ), $item->getBounds());

        $item = $this->newBasicItem('line');
        $item_schema = $this->dataItemFormat('line');

        $this->assertEquals(array(
            'x1' => $item_schema['x1'],
            'y1' => $item_schema['y1'],
            'x2' => $item_schema['x2'],
            'y2' => $item_schema['y2']
        ), $item->getBounds());
    }

    function test_isTypeOf()
    {
        $item = $this->newBasicItem('rect');

        $this->assertTrue($item->isTypeOf('basic'));
        $this->assertTrue($item->isTypeOf('rect'));
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

    private function assertFalseIn($schema_data_names, $method_name)
    {
        foreach ($schema_data_names as $schema_data_name) {
            $item = $this->newBasicItem($schema_data_name);
            $this->assertFalse($item->$method_name());
        }
    }
}
