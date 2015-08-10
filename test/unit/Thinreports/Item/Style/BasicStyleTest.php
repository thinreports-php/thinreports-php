<?php
namespace Thinreports\Item\Style;

use Thinreports\TestCase;
use Thinreports\Exception;

class TestStyle extends BasicStyle
{
    static protected $available_style_names = array('style_a');

    public function set_style_a($value)
    {
        $this->styles['style_a'] = $value;
    }

    public function get_style_a()
    {
        return $this->styles['style_a'];
    }
}

class BasicStyleTest extends TestCase
{
    private $test_style;
    private $item_format;

    function setup()
    {
        $item_format = array(
            'svg' => array(
                'attrs' => array(
                    'style_a' => 'style_a_value',
                )
            )
        );
        $this->test_style = new TestStyle($item_format);
    }

    function test_initialize()
    {
        $this->assertAttributeSame(array('style_a' => 'style_a_value'),
            'styles', $this->test_style);
    }

    /**
     * Tests for:
     *      BasicStyle::set
     *      BasicStyle::verifyStyleName
     */
    function test_set()
    {
        try {
            $this->test_style->set('unknown_style', 'value');
            $this->fail();
        } catch (Exception\StandardException $e) {
            $this->assertEquals('Unavailable Style Name', $e->getSubject());
        }

        $this->test_style->set('style_a', 'new value');
        $this->assertAttributeSame(array('style_a' => 'new value'),
            'styles', $this->test_style);
    }

    /**
     * Tests for:
     *      BasicStyle::get
     *      BasicStyle::verifyStyleName
     */
    function test_get()
    {
        try {
            $this->test_style->get('unknown_style');
            $this->fail();
        } catch (Exception\StandardException $e) {
            $this->assertEquals('Unavailable Style Name', $e->getSubject());
        }

        $this->assertEquals('style_a_value', $this->test_style->get('style_a'));
    }

    function test_export()
    {
        $this->assertSame(array('style_a' => 'style_a_value'), $this->test_style->export());

        $this->test_style->set('style_a', 'new value');
        $this->assertSame(array('style_a' => 'new value'), $this->test_style->export());
    }

    function test_readStyle()
    {
        $this->assertEquals('style_a_value', $this->test_style->readStyle('style_a'));
        $this->assertNull($this->test_style->readStyle('nonexistent_style'));
    }

    function test_verifyStyleValue()
    {
        try {
            $this->test_style->verifyStyleValue('style_a', 'Unavailable_value',
                array('available_value1', 'available_value2'));
            $this->fail();
        } catch (Exception\UnavailableStyleValue $e) {
            // OK
        }

        try {
            $this->test_style->verifyStyleValue('style_a', 'available_value1',
                array('available_value1', 'available_value2'));
        } catch (\Exception $e) {
            $this->fail();
        }
    }
}
