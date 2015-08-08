<?php
require_once __DIR__ . '/../test_helper.php';

class TextRenderingFeature extends FeatureTest
{
    function test_staticTextsRenderingWithProperlyFont()
    {
        $report = new Thinreports\Report(__DIR__ . '/layouts/static_texts.tlf');
        $report->addPage();

        $this->assertRenderingTextAndFont($report);
    }

    function test_dynamicTextRenderingWithProperlyFont()
    {
        $report = new Thinreports\Report(__DIR__ . '/layouts/dynamic_texts.tlf');
        $page = $report->addPage();

        $page->setItemValues(array(
            'helvetica' => 'Helvetica',
            'courier_new' => 'Courier New',
            'times_new_roman' => 'Times New Roman',
            'ipa_m' => 'IPA 明朝'
        ));

        $this->assertRenderingTextAndFont($report);
    }

    private function assertRenderingTextAndFont($report)
    {
        $analyzer = $this->analyzePDF($report->generate());

        $page_texts = $analyzer->getTextsInPage(1);
        $page_fonts = $analyzer->getFontsInPage(1);

        $expected_texts = array(
            'Helvetica',
            'Courier New',
            'Times New Roman',
            'IPA 明朝'
        );
        foreach ($expected_texts as $text) {
            $this->assertContains($text, $page_texts);
        }

        $expected_fonts = array(
            'Helvetica',
            'Courier',
            'Times-Roman',
            'IPAMincho'
        );
        foreach ($expected_fonts as $font) {
            $this->assertContains($font, $page_fonts);
        }
    }
}
