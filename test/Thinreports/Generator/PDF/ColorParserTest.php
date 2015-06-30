<?php
namespace Thinreports\Generator\PDF;

use Thinreports\TestCase;

class TestColorParser
{
    use ColorParser;
}

class ColorParserTest extends TestCase
{
    /**
     * @dataProvider colorPatternProvider
     */
    function test_parseColor($expected_result, $color)
    {
        $test_parser = new TestColorParser();

        $actual = $test_parser->parseColor($color);
        $this->assertSame($expected_result, $actual);
    }

    function colorPatternProvider()
    {
        return [
            [[], ''],
            [[], null],
            [[0, 0, 0], '#000000'],
            [[0, 0, 0], '000000'],
            [[199, 184, 55], '#c7b837'],
            [[255, 0, 0], 'red'],
            [[255, 240, 0], 'yellow'],
            [[0, 0, 255], 'blue'],
            [[128, 128, 0], 'olive'],
            [[0, 0, 0], 'black'],
            [[192, 192, 192], 'silver'],
            [[255, 255, 255], 'white']
        ];
    }
}
