<?php

namespace WebNavigatorTests;

use WebNavigator\Navigator;

class NavigatorTest extends TestCase {

    public function testGetText() {
        $this->_navigator->get('/test1.html');

        $this->assertSame('Hello', $this->_navigator->getText('#id1'));
    }
}
