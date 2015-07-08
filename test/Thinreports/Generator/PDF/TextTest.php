<?php
namespace Thinreports\Generator\PDF;

use Thinreports\TestCase;

class TestText
{
    use Text, ColorParser, Font;

    public function __construct($tcpdf = null)
    {
        $this->pdf = $tcpdf;
    }
}

class TextTest extends TestCase
{
    private $tcpdf;

    function setup()
    {
        $this->tcpdf = $this->getMockBuilder('MockTCPDF')
                            ->setMethods([
                                'SetFont',
                                'SetTextColorArray'
                            ])
                            ->getMock();
    }

    function test_setFontStyles()
    {
        $this->tcpdf->expects($this->once())
                    ->method('SetFont')
                    ->with('Helvetica', 'BIUD', '18');

        $this->tcpdf->expects($this->once())
                    ->method('SetTextColorArray')
                    ->with([0, 0, 0]);

        $test_text = new TestText($this->tcpdf);
        $test_text->setFontStyles([
            'color' => [0, 0, 0],
            'font_family' => 'Helvetica',
            'font_style' => 'BIUD',
            'font_size' => '18'
        ]);
    }

    /**
     * @dataProvider textAttributesProvider
     */
    function test_buildTextStyles($expected_styles, $attrs)
    {
        $test_text = new TestText();
        $this->assertSame($expected_styles, $test_text->buildTextStyles($attrs));
    }
    function textAttributesProvider()
    {
        $case1 = [
            [
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'font_style' => '',
                'color' => [],
                'align' => 'L',
                'valign' => 'T',
                'line_height' => 1,
                'letter_spacing' => 0
            ],
            [
                'font_style' => [],
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'color' => ''
            ]
        ];

        $case2 = [
            [
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'font_style' => 'BIUD',
                'color' => [0, 0, 0],
                'align' => 'R',
                'valign' => 'B',
                'line_height' => 1.5,
                'letter_spacing' => 10
            ],
            [
                'font_style' => ['bold', 'italic', 'underline', 'strikethrough'],
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'color' => '#000000',
                'align' => 'right',
                'valign' => 'bottom',
                'line_height' => 1.5,
                'letter_spacing' => 10
            ]
        ];

        return [$case1, $case2];
    }
}
