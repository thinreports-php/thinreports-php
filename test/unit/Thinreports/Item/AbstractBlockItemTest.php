<?php
namespace Thinreports\Item;

use Thinreports\TestCase;
use Thinreports\Report;

class TestBlockItem extends AbstractBlockItem {}

class AbstractBlockItemTest extends TestCase
{
    private $test_item;

    function setup()
    {
        $report = new Report($this->dataLayoutFile('empty_A4P.tlf'));
        $parent = $report->addPage();
        $format = array(
            'type' => 'test-block',
            'display' => 'true',
            'box' => array(
                'x' => 100,
                'y' => 100,
                'width' => 100,
                'height' => 100
            )
        );
        $this->test_item = new TestBlockItem($parent, $format);
    }

    function test_setValue()
    {
        $this->test_item->setValue(1000);
        $this->assertAttributeEquals(1000, 'value', $this->test_item);
    }

    function test_getValue()
    {
        $this->test_item->setValue(9999);
        $this->assertEquals(9999, $this->test_item->getValue());
    }

    function test_isEmpty()
    {
        $this->test_item->setValue('');
        $this->assertTrue($this->test_item->isEmpty());

        $this->test_item->setValue(null);
        $this->assertTrue($this->test_item->isEmpty());

        $this->test_item->setValue(0);
        $this->assertFalse($this->test_item->isEmpty());

        $this->test_item->setValue('0');
        $this->assertFalse($this->test_item->isEmpty());

        $this->test_item->setValue(1000);
        $this->assertFalse($this->test_item->isEmpty());
    }

    function test_isPresent()
    {
        $this->test_item->setValue('');
        $this->assertFalse($this->test_item->isPresent());

        $this->test_item->setValue(null);
        $this->assertFalse($this->test_item->isPresent());

        $this->test_item->setValue(0);
        $this->assertTrue($this->test_item->isPresent());

        $this->test_item->setValue('0');
        $this->assertTrue($this->test_item->isPresent());

        $this->test_item->setValue(1000);
        $this->assertTrue($this->test_item->isPresent());
    }

    function test_getBounds()
    {
        $this->assertSame(
            array('x' => 100, 'y' => 100, 'width' => 100, 'height' => 100),
            $this->test_item->getBounds()
        );
    }

    function test_isTypeOf()
    {
        $this->assertTrue($this->test_item->isTypeOf('block'));
    }
}
