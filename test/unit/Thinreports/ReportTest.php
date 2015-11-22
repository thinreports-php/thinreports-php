<?php
namespace Thinreports;

class ReportTest extends TestCase
{
    function createReport($layout_filename = null)
    {
        return new Report($layout_filename);
    }

    function test_construct()
    {
        $default_layout_filename = $this->dataLayoutFile('empty_A4P.tlf');
        $report = $this->createReport($default_layout_filename);

        $this->assertNotNull($report->getDefaultLayout());
        $this->assertEquals($default_layout_filename, $report->getDefaultLayout()->getFilename());
    }

    function test_addPage_with_default_layout()
    {
        $default_layout_filename = $this->dataLayoutFile('empty_A4P.tlf');
        $report = $this->createReport($default_layout_filename);

        $page = $report->addPage();
        $this->assertInstanceOf('Thinreports\Page\Page', $page);
        $this->assertTrue($page->isCountable());
        $this->assertEquals($default_layout_filename, $page->getLayout()->getFilename());

        $page = $report->addPage(null, true);
        $this->assertTrue($page->isCountable());

        $page = $report->addPage(null, false);
        $this->assertFalse($page->isCountable());

        # Use other layout
        $other_layout_filename = $this->dataLayoutFile('empty_A4L.tlf');
        $page = $report->addPage($other_layout_filename);

        $this->assertInstanceOf('Thinreports\Page\Page', $page);
        $this->assertTrue($page->isCountable());
        $this->assertEquals($other_layout_filename, $page->getLayout()->getFilename());
    }

    function test_addPage_without_default_layout()
    {
        $report = $this->createReport();

        # Not specify any layout
        try {
            $report->addPage();
            $this->fail();
        } catch (Exception\StandardException $e) {
            $this->assertEquals('Layout Not Specified', $e->getMessage());
        }

        $layout_filename1 = $this->dataLayoutFile('empty_A4P.tlf');
        $page = $report->addPage($layout_filename1);

        $this->assertInstanceOf('Thinreports\Page\Page', $page);
        $this->assertEquals($layout_filename1, $page->getLayout()->getFilename());

        $layout_filename2 = $this->dataLayoutFile('empty_A4L.tlf');
        $page = $report->addPage($layout_filename2);

        $this->assertEquals($layout_filename2, $page->getLayout()->getFilename());
    }

    function test_addBlankPage()
    {
        $report = $this->createReport();

        $page = $report->addBlankPage();
        $this->assertInstanceOf('Thinreports\Page\BlankPage', $page);
        $this->assertTrue($page->isCountable());

        $page = $report->addBlankPage(true);
        $this->assertTrue($page->isCountable());

        $page = $report->addBlankPage(false);
        $this->assertFalse($page->isCountable());
    }

    function test_getPageCount()
    {
        $report = $this->createReport($this->dataLayoutFile('empty_A4P.tlf'));

        $this->assertEquals(0, $report->getPageCount());

        $report->addPage();
        $report->addBlankPage();

        $this->assertEquals(2, $report->getPageCount());

        $report->addPage(null, false);
        $report->addBlankPage(false);

        $this->assertEquals(2, $report->getPageCount());
    }

    function test_getLastPageNumber()
    {
        $report = $this->createReport($this->dataLayoutFile('empty_A4P.tlf'));

        $this->assertEquals(0, $report->getLastPageNumber());

        $report->addPage();
        $report->addBlankPage();

        $this->assertEquals(2, $report->getLastPageNumber());
    }

    function test_startPageNumberFrom()
    {
        $report = $this->createReport($this->dataLayoutFile('empty_A4P.tlf'));

        $report->startPageNumberFrom(5);

        $this->assertEquals(5, $report->addPage()->getNo());
        $this->assertEquals(6, $report->addPage()->getNo());

        $this->assertEquals(2, $report->getPageCount());
        $this->assertEquals(6, $report->getLastPageNumber());
    }

    function test_getStartPageNumber()
    {
        $report = $this->createReport($this->dataLayoutFile('empty_A4P.tlf'));

        $this->assertEquals(1, $report->getStartPageNumber());

        $report->startPageNumberFrom(10);

        $this->assertEquals(10, $report->getStartPageNumber());
    }

    function test_getPages()
    {
        $report = $this->createReport($this->dataLayoutFile('empty_A4P.tlf'));

        $pages = array(
            $report->addPage(),
            $report->addPage(),
            $report->addBlankPage()
        );
        $this->assertEquals($pages, $report->getPages());
    }

    function test_getDefaultLayout()
    {
        $report = $this->createReport($this->dataLayoutFile('empty_A4P.tlf'));
        $this->assertAttributeSame($report->getDefaultLayout(), 'default_layout', $report);
    }

    function test_buildLayout()
    {
        $report = $this->createReport();
        $layout_filename = $this->dataLayoutFile('empty_A4P.tlf');

        $this->assertAttributeCount(0, 'layouts', $report);

        $layout1st = $report->buildLayout($layout_filename);
        $this->assertAttributeCount(1, 'layouts', $report);

        $layout2nd = $report->buildLayout($layout_filename);
        $this->assertAttributeCount(1, 'layouts', $report);
        $this->assertSame($layout1st, $layout2nd);
    }
}
