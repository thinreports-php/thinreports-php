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
        $item = new TestItem($this->page, array('display' => true));
        $this->assertTrue($item->isVisible());

        $item = new TestItem($this->page, array('display' => false));
        $this->assertFalse($item->isVisible());
    }

    /**
     * Tests for:
     *      AbstractItem::isVisible
     *      AbstractItem::setVisible
     *      AbstractItem::show
     *      AbstractItem::hide
     */
    function test_methods_for_visibility()
    {
        $item = new TestItem($this->page, array('display' => true));

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
        $this->assertEquals('red', $item->style->get_fill_color());
    }

    function test_getStyle()
    {
        $item = new TestGraphicsItem($this->page, $this->dataItemFormat('rect'));
        $this->assertEquals('#ffffff', $item->getStyle('fill_color'));
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
        $item = new TestItem($this->page, array('display' => true));
        $this->assertSame($this->page, $item->getParent());
    }

    function test_getIsDynamic()
    {
        $item = new TestItem($this->page, array('id' => '', 'display' => true));
        $this->assertFalse($item->isDynamic());

        $item = new TestItem($this->page, array('id' => 'foo', 'display' => true));
        $this->assertTrue($item->isDynamic());
    }

    function test_getSchema()
    {
        $schema = array('display' => true);
        $item = new TestItem($this->page, $schema);
        $this->assertSame($schema, $item->getSchema());
    }

    function test_getId()
    {
        $item = new TestItem($this->page, array(
            'display' => true,
            'id' => 'foo_id'
        ));

        $this->assertEquals('foo_id', $item->getId());
    }

    function test_isTypeOf()
    {
        $item = new TestItem($this->page, array(
            'display' => true,
            'type' => 'foo_type'
        ));
        $this->assertTrue($item->isTypeOf('foo_type'));
    }
}
