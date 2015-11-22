<?php
require_once __DIR__ . '/../test_helper.php';

class ReportAndPageFeature extends FeatureTest
{
    private $layout_geometries = array(
        'A3_portrait'  => array('width' => 841.89,  'height' => 1190.551),
        'A4_portrait'  => array('width' => 595.276, 'height' => 841.89),
        'A4_landscape' => array('width' => 841.89,  'height' => 595.276),
        'user_400x400' => array('width' => 400.0,   'height' => 400.0)
    );

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
    function test_basicPageFormats($layout_filename, $layout_page_size)
    {
        $report = new Thinreports\Report(__DIR__ . "/layouts/{$layout_filename}.tlf");
        $report->addPage();

        $analyzer = $this->analyzePDF($report->generate());
        $page_size = $analyzer->getSizeOfPage(1);

        $this->assertEquals($layout_page_size['width'], $page_size['width']);
        $this->assertEquals($layout_page_size['height'], $page_size['height']);
    }
    function pageFormatPatternProvider()
    {
        $page_formats = array();

        foreach ($this->layout_geometries as $filename => $size) {
            $page_formats[] = array($filename, $size);
        }
        # It will returns like this:
        #   array(
        #       array('A3_portrait', array('width' => 841.89, 'height' => 1190.55),
        #       array('A4_landscape', ...),
        #          :
        #   )
        return $page_formats;
    }

    function test_multipleLayouts()
    {
        $report = new Thinreports\Report(__DIR__ . '/layouts/A4_landscape.tlf');

        foreach (array_keys($this->layout_geometries) as $filename) {
            $report->addPage(__DIR__ . "/layouts/{$filename}.tlf");
            # Insert a blank page
            $report->addBlankPage();
        }
        # Finally, insert a page without specifing layout
        $report->addPage();

        $analyzer = $this->analyzePDF($report->generate());

        $expected_page_formats = array(
            array('size' => $this->layout_geometries['A3_portrait'],  'is_blank' => false),
            array('size' => $this->layout_geometries['A3_portrait'],  'is_blank' => true),
            array('size' => $this->layout_geometries['A4_portrait'],  'is_blank' => false),
            array('size' => $this->layout_geometries['A4_portrait'],  'is_blank' => true),
            array('size' => $this->layout_geometries['A4_landscape'], 'is_blank' => false),
            array('size' => $this->layout_geometries['A4_landscape'], 'is_blank' => true),
            array('size' => $this->layout_geometries['user_400x400'], 'is_blank' => false),
            array('size' => $this->layout_geometries['user_400x400'], 'is_blank' => true),
            array('size' => $this->layout_geometries['A4_landscape'], 'is_blank' => false)
        );

        foreach ($expected_page_formats as $index => $expected_page_format) {
            $page_no = $index + 1;

            $expected_page_size     = $expected_page_format['size'];
            $expected_page_is_blank = $expected_page_format['is_blank'];

            $actual_page_size = $analyzer->getSizeOfPage($page_no);
            $actual_page_is_blank = $analyzer->isEmptyPage($page_no);

            $subject = "At page {$page_no}";

            $this->assertEquals($expected_page_size['width'], $actual_page_size['width'], $subject);
            $this->assertEquals($expected_page_size['height'], $actual_page_size['height'], $subject);
            $this->assertEquals($expected_page_is_blank, $actual_page_is_blank, $subject);
        }
    }
}
