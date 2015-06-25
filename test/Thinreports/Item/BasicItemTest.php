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
        $report = new Report($this->dataLayoutFile('empty.tlf'));
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

        foreach (['line', 'rect', 'ellipse'] as $format_data_name) {
            $item = $this->newBasicItem($format_data_name);
            $this->assertAttributeInstanceOf('Thinreports\Item\Style\GraphicStyle',
                'style', $item);
        }
    }

    function test_getBounds()
    {
        $item = $this->newBasicItem('image');
        $attrs = $this->dataItemFormat('image')['svg']['attrs'];

        $this->assertEquals([
            'x'      => $attrs['x'],
            'y'      => $attrs['y'],
            'width'  => $attrs['width'],
            'height' => $attrs['height']
        ], $item->getBounds());

        $item = $this->newBasicItem('rect');
        $attrs = $this->dataItemFormat('rect')['svg']['attrs'];

        $this->assertEquals([
            'x'      => $attrs['x'],
            'y'      => $attrs['y'],
            'width'  => $attrs['width'],
            'height' => $attrs['height']
        ], $item->getBounds());

        $item = $this->newBasicItem('text');
        $box = $this->dataItemFormat('text')['box'];

        $this->assertEquals($box, $item->getBounds());

        $item = $this->newBasicItem('ellipse');
        $attrs = $this->dataItemFormat('ellipse')['svg']['attrs'];

        $this->assertEquals([
            'cx' => $attrs['cx'],
            'cy' => $attrs['cy'],
            'rx' => $attrs['rx'],
            'ry' => $attrs['ry']
        ], $item->getBounds());

        $item = $this->newBasicItem('line');
        $attrs = $this->dataItemFormat('line')['svg']['attrs'];

        $this->assertEquals([
            'x1' => $attrs['x1'],
            'y1' => $attrs['y1'],
            'x2' => $attrs['x2'],
            'y2' => $attrs['y2']
        ], $item->getBounds());
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

        $this->assertFalseIn(['rect', 'ellipse', 'line', 'text'],
            function ($item) {
                return $item->isImage();
            });
    }

    function test_isText()
    {
        $item = $this->newBasicItem('text');
        $this->assertTrue($item->isText());

        $this->assertFalseIn(['rect', 'ellipse', 'line', 'image'],
            function ($item) {
                return $item->isText();
            });
    }

    function test_isRect()
    {
        $item = $this->newBasicItem('rect');
        $this->assertTrue($item->isRect());

        $this->assertFalseIn(['ellipse', 'line', 'image', 'text'],
            function ($item) {
                return $item->isRect();
            });
    }

    function test_isEllipse()
    {
        $item = $this->newBasicItem('ellipse');
        $this->assertTrue($item->isEllipse());

        $this->assertFalseIn(['rect', 'line', 'image', 'text'],
            function ($item) {
                return $item->isEllipse();
            });
    }

    function test_isLine()
    {
        $item = $this->newBasicItem('line');
        $this->assertTrue($item->isLine());

        $this->assertFalseIn(['rect', 'ellipse', 'image', 'text'],
            function ($item) {
                return $item->isLine();
            });
    }

    private function assertFalseIn($format_data_names, callable $assertion)
    {
        foreach ($format_data_names as $format_data_name) {
            $item = $this->newBasicItem($format_data_name);
            $this->assertFalse($assertion($item));
        }
    }
}
