<?php
namespace Thinreports\Item;

use Thinreports\TestCase;
use Thinreports\Report;

class TestItem extends AbstractItem
{
    public function getBounds() {}
}

class AbstractItemTest extends TestCase
{
    private $page;

    function setup()
    {
        $report = new Report($this->dataLayoutFile('empty.tlf'));
        $this->page = $report->addPage();
    }

    function test_initialize()
    {
        $item = new TestItem($this->page, ['display' => 'true']);
        $this->assertTrue($item->isVisible());

        $item = new TestItem($this->page, ['display' => 'false']);
        $this->assertFalse($item->isVisible());
    }

    /**
     * Tests for:
     *      AbstractItem::isVisible
     *      AbstractItem::setVisible
     *      AbstractItem::show
     *      AbstractItem::hide
     */
    function test_isVisible()
    {
        $item = new TestItem($this->page, ['display' => 'true']);

        $item->setVisible(false);
        $this->assertFalse($item->isVisible());

        $item->setVisible(true);
        $this->assertTrue($item->isVisible());

        $item->hide();
        $this->assertFalse($item->isVisible());

        $item->show();
        $this->assertTrue($item->isVisible());

        $this->assertSame($item, $item->setVisible(true));
    }

    function test_getParent()
    {
        $item = new TestItem($this->page, ['display' => 'true']);
        $this->assertSame($this->page, $item->getParent());
    }

    function test_getFormat()
    {
        $item = new TestItem($this->page, ['display' => 'true']);
        $this->assertSame(['display' => 'true'], $item->getFormat());
    }

    /**
     * Tests for:
     *      AbstractItem::getId
     *      AbstractItem::getType
     *      AbstractItem::getSVGAttributes
     */
    function test_getters_for_Item_attribute()
    {
        $item = new TestItem($this->page, [
            'display' => 'true',
            'id' => 'foo_id',
            'type' => 'foo_type',
            'svg' => ['attrs' => ['attr' => 'value']]
        ]);

        $this->assertEquals('foo_id', $item->getId());
        $this->assertEquals('foo_type', $item->getType());
        $this->assertEquals(['attr' => 'value'], $item->getSVGAttributes());
    }

    function test_isTypeOf()
    {
        $item = new TestItem($this->page, [
            'display' => 'true',
            'type' => 'foo_type'
        ]);
        $this->assertTrue($item->isTypeOf('foo_type'));
    }
}
