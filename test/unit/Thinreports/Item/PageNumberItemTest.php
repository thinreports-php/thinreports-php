<?php
namespace Thinreports\Item;

use Thinreports\TestCase;
use Thinreports\Report;
use Thinreports\Layout;

class PageNumberItemTest extends TestCase
{
    private $page;
    private $report;

    function setup()
    {
        $this->report = new Report($this->dataLayoutFile('empty_A4P.tlf'));
        $this->page = $this->report->addPage();
    }

    private function newPageNumber($data_format_key)
    {
        $schema = $this->dataItemFormat('page_number', $data_format_key);
        return new PageNumberItem($this->page, $schema);
    }

    function test_initialize()
    {
        $test_item = $this->newPageNumber('default');

        $this->assertAttributeInstanceOf('Thinreports\Item\Style\TextStyle',
            'style', $test_item);
        $this->assertAttributeEquals('{page}', 'number_format', $test_item);
        $this->assertAttributeEquals(true, 'is_dynamic', $test_item);
    }

    function test_setNumberFormat()
    {
        $test_item = $this->newPageNumber('default');
        $test_item->setNumberFormat('{page} / {total}');

        $this->assertAttributeEquals('{page} / {total}', 'number_format', $test_item);
    }

    function test_getNumberFormat()
    {
        $test_item = $this->newPageNumber('default');
        $this->assertEquals('{page}', $test_item->getNumberFormat());

        $test_item->setNumberFormat('-- {page} --');
        $this->assertEquals('-- {page} --', $test_item->getNumberFormat());
    }

    function test_resetNumberFormat()
    {
        $test_item = $this->newPageNumber('default');

        $test_item->setNumberFormat('-- {page} --');
        $this->assertEquals('-- {page} --', $test_item->getNumberFormat());

        $test_item->resetNumberFormat();

        $this->assertEquals('{page}', $test_item->getNumberFormat());
    }

    function test_getFormattedPageNumber()
    {
        $test_item = $this->newPageNumber('default');
        $test_item->setNumberFormat('');

        $this->assertSame('', $test_item->getFormattedPageNumber());

        $test_item = $this->newPageNumber('target_list');

        $this->assertSame('', $test_item->getFormattedPageNumber());

        $test_item = $this->newPageNumber('default');
        $test_item->setNumberFormat('{page} / {total}');

        $this->report->addPage();
        $this->report->addpage();

        $this->assertEquals('1 / 3', $test_item->getFormattedPageNumber());
    }

    function test_isForReport()
    {
        $test_item = $this->newPageNumber('default');
        $this->assertTrue($test_item->isForReport());

        $test_item = $this->newPageNumber('target_blank');
        $this->assertTrue($test_item->isForReport());

        $test_item = $this->newPageNumber('target_list');
        $this->assertFalse($test_item->isForReport());
    }

    function test_getBounds()
    {
        $test_item = $this->newPageNumber('default');

        $this->assertEquals(
            array('x' => 100, 'y' => 100, 'width' => 100, 'height' => 100),
            $test_item->getBounds()
        );
    }
}
