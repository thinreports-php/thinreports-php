<?php
namespace Thinreports\Generator\PDF;

use Thinreports\TestCase;

class TestGraphics
{
    use Graphics, ColorParser;

    public $pdf;

    public function __construct($tcpdf = null)
    {
        $this->pdf = $tcpdf;
    }

    public function _getImageRegistry()
    {
        return $this->image_registry;
    }
}

class GraphicsTest extends TestCase
{
    private $tcpdf;

    function setup()
    {
        $this->tcpdf = $this->getMockBuilder('MockTCPDF')
                            ->setMethods(['Line', 'Rect', 'RoundedRect', 'Image', 'Ellipse'])
                            ->getMock();
    }

    function test_drawLine()
    {
        $this->tcpdf->expects($this->once())
                    ->method('Line')
                    ->with(
                        100.0,
                        200.0,
                        300.0,
                        400.0,
                        [
                            'width' => '1',
                            'color' => [0, 0, 0],
                            'dash' => null
                        ]
                    );

        $test_graphics = new TestGraphics($this->tcpdf);
        $test_graphics->drawLine(
            100.0,
            200.0,
            300.0,
            400.0,
            [
                'stroke_width' => '1',
                'stroke_color' => '#000000',
                'stroke_dash' => 'none'
            ]
        );
    }

    function test_drawRect()
    {
        $this->tcpdf->expects($this->once())
                    ->method('Rect')
                    ->with(
                        100.0,
                        200.0,
                        300.0,
                        400.0,
                        null,
                        ['all' => [
                            'width' => '2',
                            'color' => [255, 255, 255],
                            'dash' => '1,2'
                        ]],
                        [255, 0, 0]
                    );

        $test_graphics = new TestGraphics($this->tcpdf);
        $test_graphics->drawRect(
            100.0,
            200.0,
            300.0,
            400.0,
            [
                'stroke_width' => '2',
                'stroke_color' => 'ffffff',
                'stroke_dash' => '1,2',
                'fill_color' => 'red'
            ]
        );

        $this->tcpdf->expects($this->once())
                    ->method('RoundedRect')
                    ->with(
                        100.0,
                        200.0,
                        300.0,
                        400.0,
                        1,
                        '1111',
                        null,
                        [
                            'width' => '2',
                            'color' => [255, 255, 255],
                            'dash' => '1,2'
                        ],
                        [255, 0, 0]
                    );

        $test_graphics->drawRect(
            100.0,
            200.0,
            300.0,
            400.0,
            [
                'stroke_width' => '2',
                'stroke_color' => 'ffffff',
                'stroke_dash' => '1,2',
                'fill_color' => 'red',
                'radius' => 1
            ]
        );
    }

    function test_drawEllipse()
    {
        $this->tcpdf->expects($this->once())
                    ->method('Ellipse')
                    ->with(
                        100.0,
                        200.0,
                        300.0,
                        400.0,
                        0,
                        0,
                        360,
                        null,
                        [
                            'width' => '3',
                            'color' => [0, 0, 255],
                            'dash' => null
                        ]
                    );

        $test_graphics = new TestGraphics($this->tcpdf);
        $test_graphics->drawEllipse(
            100.0,
            200.0,
            300.0,
            400.0,
            [
                'stroke_width' => '3',
                'stroke_color' => 'blue',
                'stroke_dash' => 'none',
                'fill_color' => '0000ff'
            ]
        );
    }

    function test_drawImage()
    {
        $this->tcpdf->expects($this->once())
                    ->method('Image')
                    ->with(
                        '/path/to/image.png',
                        100.0,
                        200.0,
                        300.0,
                        400.0,
                        null,
                        null,
                        null,
                        true,
                        300,
                        null,
                        false,
                        false,
                        0,
                        'CM'
                    );

        $test_graphics = new TestGraphics($this->tcpdf);
        $test_graphics->drawImage(
            '/path/to/image.png',
            100.0,
            200.0,
            300.0,
            400.0,
            [
                'align' => 'center',
                'valign' => 'middle'
            ]
        );
    }

    function test_drawBase64Image()
    {
        $base64_image = file_get_contents($this->dataDir() . '/image.png.base64');
        $base64_image_key = md5($base64_image);

        $test_graphics = new TestGraphics($this->tcpdf);

        $filename_expectation = function ($actual_filename)
            use (&$base64_image_key, &$test_graphics) {

            $expected_filename = $test_graphics->_getImageRegistry()[$base64_image_key];
            return $expected_filename === $actual_filename;
        };

        $this->tcpdf->expects($this->exactly(2))
                    ->method('Image')
                    ->with(
                        $this->callback($filename_expectation),
                        100.0, 200.0, 300.0, 400.0,
                        null, null, null, true, 300, null, false, false, 0, 'LT'
                    );

        $test_graphics->drawBase64Image(
            $base64_image, 100.0, 200.0, 300.0, 400.0
        );
        $test_graphics->drawBase64Image(
            $base64_image, 100.0, 200.0, 300.0, 400.0
        );

        $image_path = $test_graphics->_getImageRegistry()[$base64_image_key];

        $this->assertFileExists($image_path);
        $this->assertEquals(
            $base64_image,
            base64_encode(file_get_contents($image_path))
        );

        $test_graphics->clearRegisteredImages();
        $this->assertFileNotExists($image_path);
    }

    /**
     * @dataProvider graphicStyleProvider
     */
    function test_buildGraphicStyles($expected_result, $attrs)
    {
        $test_graphics = new TestGraphics();

        $this->assertEquals(
            $expected_result,
            $test_graphics->buildGraphicStyles($attrs)
        );
    }
    function graphicStyleProvider()
    {
        return [
            [['stroke' => null, 'fill' => []], []],
            [['stroke' => null, 'fill' => []], ['fill_color' => 'none']],
            [
                [
                    'stroke' => [
                        'width' => '1',
                        'color' => [],
                        'dash' => null
                    ],
                    'fill' => [0, 0, 0]
                ],
                [
                    'stroke_width' => '1',
                    'stroke_color' => '',
                    'stroke_dash' => 'none',
                    'fill_color' => '#000000'
                ]
            ],
            [
                [
                    'stroke' => [
                        'width' => 1.5,
                        'color' => [0, 0, 0],
                        'dash' => '1,2'
                    ],
                    'fill' => []
                ],
                [
                    'stroke_width' => 1.5,
                    'stroke_color' => 'black',
                    'stroke_dash' => '1,2'
                ]
            ]
        ];
    }

    function test_buildImagePosition()
    {
        $test_graphics = new TestGraphics();

        $this->assertEquals('LT', $test_graphics->buildImagePosition([]));
        $this->assertEquals('CM', $test_graphics->buildImagePosition([
            'align' => 'center', 'valign' => 'middle'
        ]));
    }
}
