<?php
namespace Thinreports\Page;

use Thinreports\TestCase;
use Thinreports\Report;
use Thinreports\Layout;
use Thinreports\Item;
use Thinreports\Exception;

class PageTest extends TestCase
{
    private $report;
    private $layout;
    private $item_formats;

    function setup()
    {
        $this->item_formats = $this->dataItemFormats(array(
            array('text_block', 'default'),
            array('image_block', 'default'),
            array('text', 'default')
        ));

        $this->report = new Report($this->dataLayoutFile('empty_A4P.tlf'));
        $this->layout = new Layout('dummy.tlf', array(
            'format' => array('svg' => '<svg></svg>'),
            'item_formats' => $this->item_formats
        ));
    }

    private function newPage($is_countable = true)
    {
        return new Page($this->report, $this->layout, 1, $is_countable);
    }

    function test_isBlank()
    {
        $page = $this->newPage();
        $this->assertFalse($page->isBlank());
    }

    function test_isCountable()
    {
        $page = $this->newPage();
        $this->assertTrue($page->isCountable());

        $page = $this->newPage(false);
        $this->assertFalse($page->isCountable());
    }

    function test_item()
    {
        $page = $this->newPage();

        try {
            $page->item('unknown_id');
            $this->fail();
        } catch (Exception\StandardException $e) {
            $this->assertEquals('Item Not Found', $e->getSubject());
        }

        $this->assertAttributeCount(0, 'items', $page);

        $this->assertInstanceOf('Thinreports\Item\TextBlockItem',
            $page->item('text_block_default'));
        $this->assertInstanceOf('Thinreports\Item\ImageBlockItem',
            $page->item('image_block_default'));
        $this->assertInstanceOf('Thinreports\Item\BasicItem',
            $page->item('text_default'));

        $this->assertAttributeCount(3, 'items', $page);
    }

    function test_invoke()
    {
        $page = $this->newPage();

        try {
            $page('unknown_id');
            $this->fail();
        } catch (Exception\StandardException $e) {
            $this->assertEquals('Item Not Found', $e->getSubject());
        }

        $this->assertInstanceOf('Thinreports\Item\TextBlockItem',
            $page->item('text_block_default'));

        $this->assertSame($page->item('text_block_default'),
            $page('text_block_default'));
    }

    function test_setItemValue()
    {
        $page = $this->newPage();

        try {
            $page->setItemValue('text_default', 'content');
            $this->fail();
        } catch (Exception\StandardException $e) {
            $this->assertEquals('Unedtiable Item', $e->getSubject());
        }

        $page->setItemValue('text_block_default', 'value');
        $this->assertEquals('value', $page->item('text_block_default')->getValue());

        $page->setItemValue('image_block_default', 'value');
        $this->assertEquals('value', $page->item('image_block_default')->getValue());
    }

    function test_setItemValues()
    {
        $page = $this->newPage();

        try {
            $page->setItemValues(array('text_default' => 'value'));
            $this->fail();
        } catch (Exception\StandardException $e) {
            $this->assertEquals('Unedtiable Item', $e->getSubject());
        }

        $page->setItemValues(array(
            'text_block_default'  => 'value',
            'image_block_default' => 'value'
        ));

        $this->assertEquals('value', $page('text_block_default')->getValue());
        $this->assertEquals('value', $page('image_block_default')->getValue());
    }

    function test_hasItem()
    {
        $page = $this->newPage();

        $this->assertTrue($page->hasItem('text_block_default'));
        $this->assertTrue($page->hasItem('image_block_default'));
        $this->assertTrue($page->hasItem('text_default'));

        $this->assertFalse($page->hasItem('unknown_id'));
    }

    function test_getReport()
    {
        $page = $this->newPage();

        $this->assertSame($this->report, $page->getReport());
    }

    function test_getLayout()
    {
        $page = $this->newPage();

        $this->assertSame($this->layout, $page->getLayout());
    }

    function test_getFinalizedItems()
    {
        $page = $this->newPage();

        $expects = array(
            new Item\TextBlockItem($page, $this->item_formats['text_block_default']),
            new Item\ImageBlockItem($page, $this->item_formats['image_block_default']),
            new Item\BasicItem($page, $this->item_formats['text_default'])
        );
        $this->assertEquals($expects, $page->getFinalizedItems());
    }
}
