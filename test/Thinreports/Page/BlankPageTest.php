<?php
namespace Thinreports\Page;

use Thinreports\TestCase;

class BlankPageTest extends TestCase
{
    function test_isCountable()
    {
        $blank_page = new BlankPage(1);
        $this->assertTrue($blank_page->isCountable());

        $blank_page = new BlankPage(1, false);
        $this->assertFalse($blank_page->isCountable());
    }

    function test_isBlank()
    {
        $blank_page = new BlankPage(1);
        $this->assertTrue($blank_page->isBlank());
    }

    function test_getNo()
    {
        $blank_page = new BlankPage(5);
        $this->assertEquals(5, $blank_page->getNo());
    }
}
