<?php

namespace WebNavigatorTests;

use WebNavigator\Navigator;

class NavigatorTest extends TestCase {

    public function testGetText() {
        $this->_navigator->get('/test1.html');

        var_dump($this->_navigator->getHtml('html'));

        $this->assertSame('Hello', $this->_navigator->getText('#id1'));
    }
}
