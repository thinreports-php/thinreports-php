<?php
namespace Thinreports\Generator\PDF;

use Thinreports\TestCase;

class TextTest extends TestCase
{
    private $tcpdf;

    function setup()
    {
        $this->tcpdf = $this->getMockBuilder('TCPDF')
                            ->setMethods(array(
                                'SetFont',
                                'SetTextColorArray'
                            ))
                            ->getMock();
    }

    function test_setFontStyles()
    {
        $this->tcpdf->expects($this->once())
                    ->method('SetFont')
                    ->with('Helvetica', 'BIUD', '18');

        $this->tcpdf->expects($this->once())
                    ->method('SetTextColorArray')
                    ->with(array(0, 0, 0));

        $test_text = new Text($this->tcpdf);
        $test_text->setFontStyles(array(
            'color' => array(0, 0, 0),
            'font_family' => 'Helvetica',
            'font_style' => 'BIUD',
            'font_size' => '18'
        ));
    }

    /**
     * @dataProvider textAttributesProvider
     */
    function test_buildTextStyles($expected_styles, $attrs)
    {
        $test_text = new Text($this->tcpdf);
        $this->assertSame($expected_styles, $test_text->buildTextStyles($attrs));
    }
    function textAttributesProvider()
    {
        $case1 = array(
            array(
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'font_style' => '',
                'color' => array(),
                'align' => 'L',
                'valign' => 'T',
                'line_height' => 1,
                'letter_spacing' => 0
            ),
            array(
                'font_style' => array(),
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'color' => ''
            )
        );

        $case2 = array(
            array(
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'font_style' => 'BIUD',
                'color' => array(0, 0, 0),
                'align' => 'R',
                'valign' => 'B',
                'line_height' => 1.5,
                'letter_spacing' => 10
            ),
            array(
                'font_style' => array('bold', 'italic', 'underline', 'strikethrough'),
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'color' => '#000000',
                'align' => 'right',
                'valign' => 'bottom',
                'line_height' => 1.5,
                'letter_spacing' => 10
            )
        );

        return array($case1, $case2);
    }
}
