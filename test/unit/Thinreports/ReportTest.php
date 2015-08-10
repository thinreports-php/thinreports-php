<?php
namespace Thinreports;

class ReportTest extends TestCase
{
    private $report;

    function setup()
    {
        $this->report = new Report($this->dataLayoutFile('empty.tlf'));
    }

    function test_addPage()
    {
        $report = $this->report;

        $page = $report->addPage();
        $this->assertInstanceOf('Thinreports\Page\Page', $page);
        $this->assertTrue($page->isCountable());

        $page = $report->addPage(array('count' => true));
        $this->assertTrue($page->isCountable());

        $page = $report->addPage(array('count' => false));
        $this->assertFalse($page->isCountable());
    }

    function test_addBlankPage()
    {
        $report = $this->report;

        $page = $report->addBlankPage();
        $this->assertInstanceOf('Thinreports\Page\BlankPage', $page);
        $this->assertTrue($page->isCountable());

        $page = $report->addBlankPage(array('count' => true));
        $this->assertTrue($page->isCountable());

        $page = $report->addBlankPage(array('count' => false));
        $this->assertFalse($page->isCountable());
    }

    function test_getPageCount()
    {
        $report = $this->report;

        $this->assertEquals(0, $report->getPageCount());

        $report->addPage();
        $report->addBlankPage();

        $this->assertEquals(2, $report->getPageCount());

        $report->addPage(array('count' => false));
        $report->addBlankPage(array('count' => false));

        $this->assertEquals(2, $report->getPageCount());
    }

    function test_getLastPageNumber()
    {
        $report = $this->report;

        $this->assertEquals(0, $report->getLastPageNumber());

        $report->addPage();
        $report->addBlankPage();

        $this->assertEquals(2, $report->getLastPageNumber());
    }

    function test_startPageNumberFrom()
    {
        $report = $this->report;

        $report->startPageNumberFrom(5);

        $this->assertEquals(5, $report->addPage()->getNo());
        $this->assertEquals(6, $report->addPage()->getNo());

        $this->assertEquals(2, $report->getPageCount());
        $this->assertEquals(6, $report->getLastPageNumber());
    }

    function test_getStartPageNumber()
    {
        $report = $this->report;

        $this->assertEquals(1, $report->getStartPageNumber());

        $report->startPageNumberFrom(10);

        $this->assertEquals(10, $report->getStartPageNumber());
    }

    function test_getPages()
    {
        $report = $this->report;

        $pages = array(
            $report->addPage(),
            $report->addPage(),
            $report->addBlankPage()
        );
        $this->assertEquals($pages, $report->getPages());
    }

    function test_getDefaultLayout()
    {
        $report = $this->report;
        $this->assertAttributeSame($report->getDefaultLayout(), 'layout', $report);
    }
}
