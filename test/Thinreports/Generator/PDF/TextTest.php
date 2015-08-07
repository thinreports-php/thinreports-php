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
                                'SetTextColorArray',
                                'setFontSpacing',
                                'setCellHeightRatio',
                                'MultiCell'
                            ))
                            ->getMock();
    }

    function test_drawTextBox()
    {
        $this->tcpdf->expects($this->once())
                    ->method('SetFont')
                    ->with('Helvetica', '', '18');

        $this->tcpdf->expects($this->once())
                    ->method('SetTextColorArray')
                    ->with(array(0, 0, 0));

        $this->tcpdf->expects($this->once())
                    ->method('setFontSpacing')
                    ->with(0);

        $this->tcpdf->expects($this->once())
                    ->method('setCellHeightRatio')
                    ->with(1);

        $this->tcpdf->expects($this->once())
                    ->method('MultiCell')
                    ->with(
                        300,
                        400,
                        "row1\nrow2",
                        0,
                        'L',
                        false,
                        1,
                        100,
                        200,
                        true,
                        0,
                        false,
                        true,
                        400,
                        'T',
                        false
                    );

        $test_text = new Text($this->tcpdf);
        $test_text->drawTextBox(
            "row1\nrow2", 100, 200, 300, 400,
            array(
                'font_style' => array(),
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'color' => '#000000'
            )
        );
    }

    function test_drawText()
    {
        $this->tcpdf->expects($this->once())
                    ->method('MultiCell')
                    ->with(300, 400, 'row1 row2 row3');

        $this->tcpdf->expects($this->once())
                    ->method('setCellHeightRatio')
                    ->with(1);

        $test_text = new Text($this->tcpdf);
        $test_text->drawText(
            "row1\nrow2\nrow3", 100, 200, 300, 400,
            array(
                'font_style' => array(),
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'color' => 'red'
            )
        );
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
     * @dataProvider boxAttributesProvider
     */
    function test_buildTextBoxStyles($box_attrs, $expected_styles)
    {
        $test_text = new Text($this->tcpdf);
        $this->assertEquals(
            $expected_styles,
            $test_text->buildTextBoxStyles(100, $box_attrs)
        );
    }
    function boxAttributesProvider()
    {
        $correct_text_attrs = array(
            'attrs' => array(
                'font_style' => array(),
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'color' => ''
            ),
            'result' => array(
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'font_style' => '',
                'color' => array(),
                'align' => 'L',
                'valign' => 'T',
                'line_height' => 1,
                'letter_spacing' => 0
            )
        );

        $case_single_row_is_true = array(
            array_merge($correct_text_attrs['attrs'], array(
                'single_row' => true,
                'line_height' => '20',
                'overflow' => 'truncate',
            )),
            array_merge($correct_text_attrs['result'], array(
                'line_height' => 1,
                'overflow' => array(
                    'fit_cell' => false,
                    'max_height' => 100
                )
            ))
        );

        $case_single_row_is_omitted = array(
            array_merge($correct_text_attrs['attrs'], array(
                'line_height' => '999',
                'overflow' => 'truncate'
            )),
            array_merge($correct_text_attrs['result'], array(
                'line_height' => '999',
                'overflow' => array(
                    'fit_cell' => false,
                    'max_height' => 100
                )
            ))
        );

        $case_single_row_is_false = array(
            array_merge($correct_text_attrs['attrs'], array(
                'single_row' => false,
                'line_height' => '999',
                'overflow' => 'truncate'
            )),
            array_merge($correct_text_attrs['result'], array(
                'line_height' => '999',
                'overflow' => array(
                    'fit_cell' => false,
                    'max_height' => 100
                )
            ))
        );

        $case_overflow_is_omitted = array(
            array_merge($correct_text_attrs['attrs'], array(
                'single_row' => true,
                'line_height' => '20'
            )),
            array_merge($correct_text_attrs['result'], array(
                'overflow' => array(
                    'fit_cell' => false,
                    'max_height' => 100
                )
            ))
        );

        $case_overflow_is_fit = array(
            array_merge($correct_text_attrs['attrs'], array(
                'overflow' => 'fit',
                'single_row' => true,
                'line_height' => '20'
            )),
            array_merge($correct_text_attrs['result'], array(
                'overflow' => array(
                    'fit_cell' => true,
                    'max_height' => 100
                )
            ))
        );

        $case_overflow_is_expand = array(
            array_merge($correct_text_attrs['attrs'], array(
                'overflow' => 'expand',
                'single_row' => true,
                'line_height' => '20'
            )),
            array_merge($correct_text_attrs['result'], array(
                'overflow' => array(
                    'fit_cell' => false,
                    'max_height' => 0
                )
            ))
        );

        return array(
            $case_single_row_is_true,
            $case_single_row_is_omitted,
            $case_single_row_is_false,
            $case_overflow_is_omitted,
            $case_overflow_is_fit,
            $case_overflow_is_expand
        );
    }

    /**
     * @dataProvider textAttributesProvider
     */
    function test_buildTextStyles($text_attrs, $expected_styles)
    {
        $test_text = new Text($this->tcpdf);
        $this->assertSame($expected_styles, $test_text->buildTextStyles($text_attrs));
    }
    function textAttributesProvider()
    {
        $case1 = array(
            array(
                'font_style' => array(),
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'color' => ''
            ),
            array(
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'font_style' => '',
                'color' => array(),
                'align' => 'L',
                'valign' => 'T',
                'line_height' => 1,
                'letter_spacing' => 0
            )
        );

        $case2 = array(
            array(
                'font_style' => array('bold', 'italic', 'underline', 'strikethrough'),
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'color' => '#000000',
                'align' => 'right',
                'valign' => 'bottom',
                'line_height' => 1.5,
                'letter_spacing' => 10
            ),
            array(
                'font_size' => '18',
                'font_family' => 'Helvetica',
                'font_style' => 'BIUD',
                'color' => array(0, 0, 0),
                'align' => 'R',
                'valign' => 'B',
                'line_height' => 1.5,
                'letter_spacing' => 10
            )
        );

        return array($case1, $case2);
    }
}
