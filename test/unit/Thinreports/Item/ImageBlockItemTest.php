<?php
namespace Thinreports\Item;

use Thinreports\TestCase;
use Thinreports\Report;

class ImageBlockItemTest extends TestCase
{
    private $page;

    function setup()
    {
        $report = new Report($this->dataLayoutFile('empty_A4P.tlf'));
        $this->page = $report->addPage();
    }

    private function newImageBlock()
    {
        $format = $this->dataItemFormat('image_block', 'default');
        return new ImageBlockItem($this->page, $format);
    }

    function test_initialize()
    {
        $test_item = $this->newImageBlock();
        $this->assertAttributeInstanceOf('Thinreports\Item\Style\BasicStyle',
            'style', $test_item);
    }

    function test_setSource()
    {
        $test_item = $this->newImageBlock();

        $test_item->setSource('/path/to/image.png');
        $this->assertEquals('/path/to/image.png', $test_item->getValue());
    }

    function test_getSource()
    {
        $test_item = $this->newImageBlock();
        $this->assertSame('', $test_item->getSource());

        $test_item->setValue('/path/to/image.png');
        $this->assertEquals('/path/to/image.png', $test_item->getSource());
    }
}
