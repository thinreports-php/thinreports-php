<?php
require_once __DIR__ . '/../test_helper.php';

class ImageRenderingFeature extends FeatureTest
{
    function test_imageRendering()
    {
        $report = new Thinreports\Report(__DIR__ . '/layouts/images.tlf');
        $page = $report->addPage();

        $page('image_jpeg')->setSource(__DIR__ . '/files/image-block-jpeg.jpg');
        $page('image_png')->setSource(__DIR__ . '/files/image-block-png.png');

        $analyzer = $this->analyzePDF($report->generate());

        $this->assertEquals(4, $analyzer->getImageCountInPage(1));
    }
}
