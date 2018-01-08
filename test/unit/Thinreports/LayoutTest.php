<?php
namespace Thinreports;

use Thinreports\Exception;
use Thinreports\Item;

class LayoutTest extends TestCase
{
    function test_loadFile()
    {
        try {
            Layout::loadFile('nonexistent.tlf');
            $this->fail();
        } catch (Exception\StandardException $e) {
            $this->assertEquals('Layout File Not Found', $e->getSubject());
        }

        $layout = Layout::loadFile($this->dataLayoutFile('empty_A4P.tlf'));
        $this->assertInstanceOf('Thinreports\Layout', $layout);
    }

    function test_loadData()
    {
        $schema_data = '{"version":"0.10.1", "items":[]}';

        $layout = Layout::loadData($schema_data);

        $this->assertInstanceOf('Thinreports\Layout', $layout);
        $this->assertAttributeEquals(md5($schema_data), 'identifier', $layout);
    }

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

        $schema = Layout::parse('{"version":"0.9.0", "items":[]}');

        $this->assertEquals(array('version' => '0.9.0', 'items' => array()), $schema);
    }

    function test_initialize()
    {
        $schema = array(
            'version' => '0.10.1',
            'items' => array(
                array('id' => '', 'type' => 'rect'),
                array('id' => 'foo', 'type' => 'text-block'),
                array('id' => 'bar', 'type' => 'text'),
                array('id' => '', 'type' => 'line')
            )
        );

        $layout = new Layout($schema, 'layout_identifier');

        $this->assertAttributeSame($schema, 'schema', $layout);
        $this->assertAttributeEquals('layout_identifier', 'identifier', $layout);
        $this->assertAttributeEquals(
            array(
                'with_id' => array(
                    'foo' => array('id' => 'foo', 'type' => 'text-block'),
                    'bar' => array('id' => 'bar', 'type' => 'text')
                ),
                'without_id' => array(
                    array('id' => '', 'type' => 'rect'),
                    array('id' => '', 'type' => 'line')
                )
            ),
            'item_schemas',
            $layout
        );
    }

    function test_hasItemById()
    {
        $item_schemas = array(
            array('id' => 'foo', 'type' => 'rect'),
            array('id' => 'bar', 'type' => 'text-block')
        );

        $layout = new Layout(array('items' => $item_schemas), 'identifier');

        $this->assertTrue($layout->hasItemById('bar'));
        $this->assertFalse($layout->hasItemById('unknown'));
    }

    function test_createItem()
    {
        $this->markTestSkipped('Item classes are not supported yet');

        $item_formats = $this->dataItemFormats(array(
            array('text_block', 'default'),
            array('image_block', 'default'),
            array('page_number', 'default'),
            array('rect', 'default'),
            array('ellipse', 'default'),
            array('line', 'default'),
            array('image', 'default'),
            array('text', 'default')
        ));

        $layout = new Layout('dummy.tlf', array(
            'format' => array('svg' => '<svg></svg>'),
            'item_formats' => $item_formats
        ));

        $dummy_report = new Report($this->dataLayoutFile('empty_A4P.tlf'));
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

        $graphic_ids = array(
            'rect_default',
            'ellipse_default',
            'line_default',
            'image_default',
            'text_default'
        );
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
     * Tests for:
     *      Layout::getLastVersion
     *      Layout::getReportTitle
     *      Layout::getPagePaperType
     *      Layout::isUserPaperType
     *      Layout::isPortraitPage
     *      Layout::getPageSize
     */
    function test_schema_attribute_getters()
    {
        $schema = array(
            'version' => '0.10.1',
            'title' => 'Report Title',
            'report' => array(
                'paper-type' => 'A4',
                'orientation' => 'landscape',
            ),
            'items' => array()
        );

        $layout = new Layout($schema, 'identifier');

        $this->assertEquals('0.10.1', $layout->getLastVersion());
        $this->assertEquals('Report Title', $layout->getReportTitle());
        $this->assertEquals('A4', $layout->getPagePaperType());
        $this->assertFalse($layout->isUserPaperType());
        $this->assertFalse($layout->isPortraitPage());
        $this->assertNull($layout->getPageSize());

        $schema = array(
            'report' => array(
                'paper-type' => 'user',
                'orientation' => 'portrait',
                'width' => 100.9,
                'height' => 999.9
            ),
            'items' => array()
        );

        $layout = new Layout($schema, 'identifier');

        $this->assertEquals('user', $layout->getPagePaperType());
        $this->assertTrue($layout->isUserPaperType());
        $this->assertEquals(array(100.9, 999.9), $layout->getPageSize());
    }

    function test_getIdentifier()
    {
        $layout = new Layout(array('items' => array()), 'identifier');
        $this->assertEquals('identifier', $layout->getIdentifier());
    }

    function test_getSchema()
    {
        $schema = array('version' => '0.10.1', 'items' => array());
        $layout = new Layout($schema, 'identifier');

        $this->assertSame($schema, $layout->getSchema());
    }

    function test_getItemSchemas()
    {
        $item_schemas = array(
            array('id' => 'text1', 'type' => 'text-block'),
            array('id' => '', 'type' => 'rect')
        );

        $layout = new Layout(array('items' => $item_schemas), 'identifier');

        $this->assertSame($item_schemas, $layout->getItemSchemas());
        $this->assertSame($item_schemas, $layout->getItemSchemas('all'));
        $this->assertEquals(
            array('text1' => array('id' => 'text1', 'type' => 'text-block')),
            $layout->getItemSchemas('with_id')
        );
        $this->assertEquals(
            array(array('id' => '', 'type' => 'rect')),
            $layout->getItemSchemas('without_id')
        );
    }
}
