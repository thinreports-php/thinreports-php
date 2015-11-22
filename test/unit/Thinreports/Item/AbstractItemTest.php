<?php
namespace Thinreports\Item;

use Thinreports\TestCase;
use Thinreports\Report;
use Thinreports\Page\Page;
use Thinreports\Item\Style\GraphicStyle;

class TestItem extends AbstractItem
{
    public function getBounds() {}
}

class TestGraphicsItem extends AbstractItem
{
    // make public for testing
    public $style;

    public function __construct(Page $parent, array $format)
    {
        parent::__construct($parent, $format);
        $this->style = new GraphicStyle($format);
    }

    public function getBounds() {}
}

class AbstractItemTest extends TestCase
{
    private $page;

    function setup()
    {
        $report = new Report($this->dataLayoutFile('empty_A4P.tlf'));
        $this->page = $report->addPage();
    }

    function test_initialize()
    {
        $item = new TestItem($this->page, array('display' => 'true'));
        $this->assertTrue($item->isVisible());

        $item = new TestItem($this->page, array('display' => 'false'));
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
        $item = new TestItem($this->page, array('display' => 'true'));

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

    function test_setStyle()
    {
        $item = new TestGraphicsItem($this->page, $this->dataItemFormat('rect'));

        $item->setStyle('fill_color', 'red');
        $this->assertEquals('red', $item->getStyle('fill_color'));
    }

    function test_getStyle()
    {
        $item = new TestGraphicsItem($this->page, $this->dataItemFormat('rect'));
        $this->assertEquals('#ffffff', $item->style->get_fill_color());
    }

    function test_setStyles()
    {
        $item = new TestGraphicsItem($this->page, $this->dataItemFormat('rect'));

        $item->setStyles(array('fill_color' => 'blue', 'border_width' => 999));
        $this->assertEquals('blue', $item->style->get_fill_color());
        $this->assertEquals(999, $item->style->get_border_width());
    }

    function test_exportStyles()
    {
        $item = new TestGraphicsItem($this->page, $this->dataItemFormat('rect'));
        $item->style->set_fill_color('#0000ff');

        $this->assertEquals($item->style->export(), $item->exportStyles());
    }

    function test_getParent()
    {
        $item = new TestItem($this->page, array('display' => 'true'));
        $this->assertSame($this->page, $item->getParent());
    }

    function test_getFormat()
    {
        $item = new TestItem($this->page, array('display' => 'true'));
        $this->assertSame(array('display' => 'true'), $item->getFormat());
    }

    /**
     * Tests for:
     *      AbstractItem::getId
     *      AbstractItem::getType
     *      AbstractItem::getSVGAttributes
     */
    function test_getters_for_Item_attribute()
    {
        $item = new TestItem($this->page, array(
            'display' => 'true',
            'id' => 'foo_id',
            'type' => 'foo_type',
            'svg' => array('attrs' => array('attr' => 'value'))
        ));

        $this->assertEquals('foo_id', $item->getId());
        $this->assertEquals('foo_type', $item->getType());
        $this->assertEquals(array('attr' => 'value'), $item->getSVGAttributes());
    }

    function test_isTypeOf()
    {
        $item = new TestItem($this->page, array(
            'display' => 'true',
            'type' => 'foo_type'
        ));
        $this->assertTrue($item->isTypeOf('foo_type'));
    }
}
