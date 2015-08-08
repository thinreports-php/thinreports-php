<?php
require_once __DIR__ . '/../test_helper.php';

class ReportAndPageFeature extends FeatureTest
{
    function test_reportProperties()
    {
        $report = new Thinreports\Report(__DIR__ . '/layouts/report_with_title.tlf');
        $report->addPage();

        $analyzer = $this->analyzePDF($report->generate());

        $this->assertEquals('Report Title', $analyzer->getPropertyTitle());
        $this->assertEquals('Thinreports Generator', $analyzer->getPropertyCreator());
    }

    function test_multiplePages()
    {
        $report = new Thinreports\Report(__DIR__ . '/layouts/normal_page.tlf');
        $report->addPage();
        $report->addPage();
        $report->addBlankPage();

        $analyzer = $this->analyzePDF($report->generate());

        $this->assertEquals(3, $analyzer->getPageCount());
        $this->assertContains('Normal Page', $analyzer->getTextsInPage(1));
        $this->assertContains('Normal Page', $analyzer->getTextsInPage(2));
        $this->assertTrue($analyzer->isEmptyPage(3));
    }

    /**
     * @dataProvider pageFormatPatternProvider
     */
    function test_basicPageFormats($layout_file, $page_width, $page_height)
    {
        $report = new Thinreports\Report(__DIR__ . "/layouts/{$layout_file}");
        $report->addPage();

        $analyzer = $this->analyzePDF($report->generate());
        $page_size = $analyzer->getSizeOfPage(1);

        $this->assertEquals($page_width, $page_size['width']);
        $this->assertEquals($page_height, $page_size['height']);
    }
    function pageFormatPatternProvider()
    {
        return array(
            array('A4_portrait.tlf',  595.276, 841.89),
            array('A4_landscape.tlf', 841.89,  595.276),
            array('user_400x400.tlf', 400.0,   400.0)
        );
    }
}
