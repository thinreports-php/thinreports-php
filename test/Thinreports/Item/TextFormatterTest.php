<?php
namespace Thinreports\Item;

use Thinreports\TestCase;

class TextFormatterTest extends TestCase
{
    function test_number_format()
    {
        $formatter = new TextFormatter(array(
            'type' => 'number',
            'number' => array(
                'precision' => 2,
                'delimiter' => ','
            ),
            'base' => ''
        ));

        $this->assertEquals('Not a Number', $formatter->format('Not a Number'));
        $this->assertEquals('1,000.00', $formatter->format('1000'));
        $this->assertEquals('1,000.00', $formatter->format(1000));
        $this->assertEquals('1,000.00', $formatter->format(999.995));
        $this->assertEquals('999.99', $formatter->format(999.994));

        $formatter = new TextFormatter(array(
            'type' => 'number',
            'number' => array(
                'precision' => '',
                'delimiter' => ','
            ),
            'base' => ''
        ));

        $this->assertEquals('10,000', $formatter->format('9999.9'));

        $formatter = new TextFormatter(array(
            'type' => 'number',
            'number' => array(
                'precision' => 1,
                'delimiter' => ','
            ),
            'base' => '${value}'
        ));
        $this->assertEquals('$1,000.0', $formatter->format(999.99));
    }

    function test_datetime_format()
    {
        $formatter = new TextFormatter(array(
            'type' => 'datetime',
            'datetime' => array('format' => '%Y/%m/%d %H:%M:%S'),
            'base' => ''
        ));

        $this->assertEquals('', $formatter->format(''));

        $this->assertEquals('2015/06/22 13:59:00', $formatter->format('2015-6-22 13:59'));
        $this->assertEquals('Invalid datetime string', $formatter->format('Invalid datetime string'));

        $formatter = new TextFormatter(array(
            'type' => 'datetime',
            'datetime' => array('format' => ''),
            'base' => ''
        ));

        $this->assertEquals('2015-6-22', $formatter->format('2015-6-22'));

        $formatter = new TextFormatter(array(
            'type' => 'datetime',
            'datetime' => array('format' => '%A'),
            'base' => '2015/7/1 is {value}.'
        ));

        $this->assertEquals('2015/7/1 is Wednesday.', $formatter->format('2015/7/1'));
    }

    function test_padding_format()
    {
        $formatter = new TextFormatter(array(
            'type' => 'padding',
            'padding' => array(
                'direction' => 'L',
                'char' => 0,
                'length' => 5
            ),
            'base' => ''
        ));

        $this->assertEquals('00001', $formatter->format(1));
        $this->assertEquals('', $formatter->format(''));
        $this->assertEquals('123456', $formatter->format('123456'));
        $this->assertEquals('あいうえおか', $formatter->format('あいうえおか'));

        $formatter = new TextFormatter(array(
            'type' => 'padding',
            'padding' => array(
                'direction' => 'R',
                'char' => 'い',
                'length' => 3
            ),
            'base' => ''
        ));
        $this->assertEquals('あいい', $formatter->format('あ'));

        $formatter = new TextFormatter(array(
            'type' => 'padding',
            'padding' => array(
                'direction' => 'L',
                'char' => 'あい',
                'length' => 4
            ),
            'base' => ''
        ));

        $this->assertEquals('いあいお', $formatter->format('お'));

        $formatter = new TextFormatter(array(
            'type' => 'padding',
            'padding' => array(
                'direction' => 'R',
                'char' => 'あい',
                'length' => 4
            ),
            'base' => ''
        ));

        $this->assertEquals('おあいあ', $formatter->format('お'));

        $formatter = new TextFormatter(array(
            'type' => 'padding',
            'padding' => array(
                'direction' => 'L',
                'char' => ' ',
                'length' => 5
            ),
            'base' => '({value})'
        ));

        $this->assertEquals('(  999)', $formatter->format(999));
    }

    function test_format()
    {
        $formatter = new TextFormatter(array(
            'type' => 'number',
            'number' => array(
                'precision' => 0,
                'delimiter' => ','
            ),
            'base' => '- {value} -'
        ));

        $this->assertNull($formatter->format(null));
        $this->assertSame('', $formatter->format(''));

        $formatter = new TextFormatter(array(
            'type' => '',
            'base' => '-{value}-'
        ));

        $this->assertEquals('-あ-', $formatter->format('あ'));

        $formatter = new TextFormatter(array(
            'type' => '',
            'base' => 'あ{value}う'
        ));

        $this->assertEquals('あいう', $formatter->format('い'));
    }
}
