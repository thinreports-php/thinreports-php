<?php
namespace Thinreports;

use Thinreports\Exception;
use Thinreports\Item;

class LayoutTest extends TestCase
{
    /**
     * @covers Layout::loadFile
     */
    function test_loadFile()
    {
        try {
            Layout::loadFile('nonexistent.tlf');
            $this->fail();
        } catch (Exception\StandardException $e) {
            $this->assertEquals('Layout File Not Found', $e->getSubject());
        }

        $layout = Layout::loadFile($this->dataLayoutFile('empty.tlf'));
        $this->assertInstanceOf('Thinreports\Layout', $layout);

        $layout = Layout::loadFile($this->dataLayoutFile('all_items'));
        $this->assertInstanceOf('Thinreports\Layout', $layout);

        $this->assertCount(9, $layout->getItemFormats());
    }

    /**
     * @covers Layout::parse
     */
    function test_parse()
    {
        try {
            Layout::parse('{"version":"0.8.1"}');
            $this->fail();
        } catch (Exception\IncompatibleLayout $e) {
            // OK
        }

        try {
            Layout::parse('{"version":"1.0.0"}');
            $this->fail();
        } catch (Exception\IncompatibleLayout $e) {
            // OK
        }

        $layout = Layout::parse('{"version":"0.8.2", "svg":"<svg></svg>"}');
        $this->assertInstanceOf('Thinreports\Layout', $layout);
    }

    /**
     * @covers Layout::cleanFormat
     */
    function test_cleanFormat()
    {
        $svg = '<!--SHAPE{"type":"rect"}-->' .
               '<!--LAYOUT<rect class="s-rect"/>-->' .
               '<ellipse class="s-ellipse"/>';
        $format = [
            'svg'   => $svg,
            'state' => []
        ];
        Layout::cleanFormat($format);

        $this->assertEquals('<ellipse class="s-ellipse"/>', $format['svg']);
        $this->assertArrayNotHasKey('state', $format);
    }

    /**
     * @covers Layout::extractItemFormats
     */
    function test_extractItemFormats()
    {
        $layout = <<<'SVG'
<svg width="100.0" height="100.0" xmlns="http://www.w3.org/2000/svg">
  <g class="canvas">
    <!--SHAPE{"type":"s-tblock","id":"text_block1"}SHAPE-->
    <!--SHAPE{"type":"s-tblock","id":"text_block2"}SHAPE-->
    <!--SHAPE{"type":"s-list"}SHAPE-->
    <!--SHAPE{"type":"s-pageno","id":""}SHAPE-->
    <!--SHAPE{"type":"s-pageno","id":"page_no"}SHAPE-->
  </g>
</svg>
SVG;
        $formats = Layout::extractItemFormats($layout);

        $this->assertCount(4, $formats);
        $this->assertEquals(['type' => 's-tblock', 'id' => 'text_block1'], $formats['text_block1']);
        $this->assertEquals(['type' => 's-tblock', 'id' => 'text_block2'], $formats['text_block2']);
        $this->assertEquals(['type' => 's-pageno', 'id' => 'page_no'], $formats['page_no']);
        $this->assertCount(1, preg_grep('/^__page_no_/', array_keys($formats)));

        $this->assertArrayNotHasKey('s-list', $formats);
    }

    /**
     * @covers Layout::hasItem
     */
    function test_hasItem()
    {
        $item_formats = ['foo_id' => []];
        $layout = new Layout(['svg' => '<svg></svg>'], $item_formats);

        $this->assertTrue($layout->hasItem('foo_id'));
        $this->assertFalse($layout->hasItem('unknown_id'));
    }

    /**
     * @covers Layout::createItem
     */
    function test_createItem()
    {
        $item_formats = $this->dataItemFormats([
            ['text_block', 'default'],
            ['image_block', 'default'],
            ['page_number', 'default'],
            ['rect', 'default'],
            ['ellipse', 'default'],
            ['line', 'default'],
            ['image', 'default'],
            ['text', 'default']
        ]);

        $layout = new Layout(['svg' => '<svg></svg>'], $item_formats);

        $dummy_report = new Report($this->dataLayoutFile('empty.tlf'));
        $dummy_page   = $dummy_report->addPage();

        $this->assertInstanceOf(
            'Thinreports\Item\TextBlockItem',
            $layout->createItem($dummy_page, 'text_block_default')
        );
        $this->assertInstanceOf(
            'Thinreports\Item\ImageBlockItem',
            $layout->createItem($dummy_page, 'image_block_default')
        );
        $this->assertInstanceOf(
            'Thinreports\Item\PageNumberItem',
            $layout->createItem($dummy_page, '__page_no_1__')
        );

        $graphic_ids = [
            'rect_default',
            'ellipse_default',
            'line_default',
            'image_default',
            'text_default'
        ];
        foreach ($graphic_ids as $id) {
            $this->assertInstanceOf(
                'Thinreports\Item\BasicItem',
                $layout->createItem($dummy_page, $id)
            );
        }

        try {
            $layout->createItem($dummy_page, 'unknown_id');
            $this->fail();
        } catch (Exception\StandardException $e) {
            $this->assertEquals('Item Not Found', $e->getSubject());
        }
    }

    /**
     * @covers Layout::getLastVersion
     * @covers Layout::getReportTitle
     * @covers Layout::getPagePaperType
     * @covers Layout::isUserPaperType
     * @covers Layout::isPortraitPage
     * @covers Layout::getPageSize
     * @covers Layout::getSVG
     */
    function test_getters_for_Layout_configuration()
    {
        $regular_paper_type_format = [
            'version' => '0.8.2',
            'config' => [
                'title' => 'Report Title',
                'page'  => [
                    'paper-type'  => 'A4',
                    'orientation' => 'landscape',
                ]
            ],
            'svg' => '<svg></svg>'
        ];

        $layout = new Layout($regular_paper_type_format, []);

        $this->assertEquals('0.8.2', $layout->getLastVersion());
        $this->assertEquals('Report Title', $layout->getReportTitle());
        $this->assertEquals('A4', $layout->getPagePaperType());
        $this->assertFalse($layout->isUserPaperType());
        $this->assertFalse($layout->isPortraitPage());
        $this->assertNull($layout->getPageSize());
        $this->assertEquals('<svg></svg>', $layout->getSVG());

        $user_paper_type_format = [
            'version' => '0.8.2',
            'config' => [
                'title' => 'Report Title',
                'page'  => [
                    'paper-type'  => 'user',
                    'orientation' => 'landscape',
                    'width'       => 100.9,
                    'height'      => 999.9
                ]
            ],
            'svg' => '<svg></svg>'
        ];

        $layout = new Layout($user_paper_type_format, []);

        $this->assertEquals('user', $layout->getPagePaperType());
        $this->assertTrue($layout->isUserPaperType());
        $this->assertEquals([100.9, 999.9], $layout->getPageSize());
    }

    /**
     * @covers Layout::getIdentifier
     */
    function test_getIdentifier()
    {
        $format = [
            'svg' => '<svg></svg>'
        ];

        $layout = new Layout($format, []);
        $this->assertEquals(md5('<svg></svg>'), $layout->getIdentifier());
    }

    /**
     * @covers Layout::getFormat
     */
    function test_getFormat()
    {
        $format = [
            'version' => '0.8.2',
            'svg'     => '<svg></svg>'
        ];

        $layout = new Layout($format, []);
        $this->assertSame($format, $layout->getFormat());
    }

    /**
     * @covers Layout::getItemFormats
     */
    function test_getItemFormats()
    {
        $item_formats = [
            'rect_id' => ['type' => 's-rect']
        ];

        $layout = new Layout(['svg' => '<svg></svg>'], $item_formats);
        $this->assertSame($item_formats, $layout->getItemFormats());
    }
}
