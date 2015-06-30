<?php
namespace Thinreports\Item;

use Thinreports\TestCase;

class TestFormattableItem
{
    use Formattable;

    public $value = '';
    public $format;

    public function __construct(array $format)
    {
        $this->format = ['format' => $format];
    }

    public function getValue()
    {
        return $this->value;
    }
}

class FormattableTest extends TestCase
{
    /**
     * Tests for:
     *      Formattable::getFormattedValue
     *      Formattable::applyNumberFormat
     */
    function test_number_format()
    {
        $test_item = new TestFormattableItem([
            'type' => 'number',
            'number' => [
                'precision' => 2,
                'delimiter' => ','
            ],
            'base' => ''
        ]);

        $test_item->value = 'Not a Number';
        $this->assertEquals('Not a Number', $test_item->getFormattedValue());

        $test_item->value = '1000';
        $this->assertEquals('1,000.00', $test_item->getFormattedValue());

        $test_item->value = 1000;
        $this->assertEquals('1,000.00', $test_item->getFormattedValue());

        $test_item->value = 999.995;
        $this->assertEquals('1,000.00', $test_item->getFormattedValue());

        $test_item->value = 999.994;
        $this->assertEquals('999.99', $test_item->getFormattedValue());

        $test_item = new TestFormattableItem([
            'type' => 'number',
            'number' => [
                'precision' => '',
                'delimiter' => ','
            ],
            'base' => ''
        ]);

        $test_item->value = '9999.9';
        $this->assertEquals('10,000', $test_item->getFormattedValue());

        $test_item = new TestFormattableItem([
            'type' => 'number',
            'number' => [
                'precision' => 1,
                'delimiter' => ','
            ],
            'base' => '${value}'
        ]);

        $test_item->value = 999.99;
        $this->assertEquals('$1,000.0', $test_item->getFormattedValue());
    }

    function test_datetime_format()
    {
        $test_item = new TestFormattableItem([
            'type' => 'datetime',
            'datetime' => ['format' => '%Y/%m/%d %H:%M:%S'],
            'base' => ''
        ]);

        $test_item->value = '';
        $this->assertEquals('', $test_item->getFormattedValue());

        $test_item->value = '2015-6-22 13:59';
        $this->assertEquals('2015/06/22 13:59:00', $test_item->getFormattedValue());

        $test_item->value = 'Invalid datetime string';
        $this->assertEquals('Invalid datetime string', $test_item->getFormattedValue());

        $test_item = new TestFormattableItem([
            'type' => 'datetime',
            'datetime' => ['format' => ''],
            'base' => ''
        ]);

        $test_item->value = '2015-6-22';
        $this->assertEquals('2015-6-22', $test_item->getFormattedValue());

        $test_item = new TestFormattableItem([
            'type' => 'datetime',
            'datetime' => ['format' => '%A'],
            'base' => '2015/7/1 is {value}.'
        ]);

        $test_item->value = '2015/7/1';
        $this->assertEquals('2015/7/1 is Wednesday.', $test_item->getFormattedValue());
    }

    function test_padding_format()
    {
        $test_item = new TestFormattableItem([
            'type' => 'padding',
            'padding' => [
                'direction' => 'L',
                'char' => 0,
                'length' => 5
            ],
            'base' => ''
        ]);

        $test_item->value = 1;
        $this->assertEquals('00001', $test_item->getFormattedValue());

        $test_item->value = '';
        $this->assertEquals('', $test_item->getFormattedValue());

        $test_item->value = '123456';
        $this->assertEquals('123456', $test_item->getFormattedValue());

        $test_item->value = 'あいうえおか';
        $this->assertEquals('あいうえおか', $test_item->getFormattedValue());

        $test_item = new TestFormattableItem([
            'type' => 'padding',
            'padding' => [
                'direction' => 'R',
                'char' => 'い',
                'length' => 3
            ],
            'base' => ''
        ]);

        $test_item->value = 'あ';
        $this->assertEquals('あいい', $test_item->getFormattedValue());

        $test_item = new TestFormattableItem([
            'type' => 'padding',
            'padding' => [
                'direction' => 'L',
                'char' => 'あい',
                'length' => 4
            ],
            'base' => ''
        ]);

        $test_item->value = 'お';
        $this->assertEquals('いあいお', $test_item->getFormattedValue());

        $test_item = new TestFormattableItem([
            'type' => 'padding',
            'padding' => [
                'direction' => 'R',
                'char' => 'あい',
                'length' => 4
            ],
            'base' => ''
        ]);

        $test_item->value = 'お';
        $this->assertEquals('おあいあ', $test_item->getFormattedValue());

        $test_item = new TestFormattableItem([
            'type' => 'padding',
            'padding' => [
                'direction' => 'L',
                'char' => ' ',
                'length' => 5
            ],
            'base' => '({value})'
        ]);

        $test_item->value = 999;
        $this->assertEquals('(  999)', $test_item->getFormattedValue());
    }

    function test_getFormattedValue()
    {
        $test_item = new TestFormattableItem([
            'type' => 'number',
            'number' => [
                'precision' => 0,
                'delimiter' => ','
            ],
            'base' => '- {value} -'
        ]);

        $test_item->value = null;
        $this->assertNull($test_item->getFormattedValue());

        $test_item->value = '';
        $this->assertSame('', $test_item->getFormattedValue());

        $test_item = new TestFormattableItem([
            'type' => '',
            'base' => '- {value} -'
        ]);

        $test_item->value = 'value';
        $this->assertEquals('- value -', $test_item->getFormattedValue());
    }
}
