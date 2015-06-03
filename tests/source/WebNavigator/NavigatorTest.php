<?php

namespace WebNavigatorTests;

use WebNavigator\Navigator;

class NavigatorTest extends \PHPUnit_Framework_TestCase {

    public function testConstruct() {
        $capabilities = new \DesiredCapabilities([\WebDriverCapabilityType::BROWSER_NAME => 'phantomjs']);
        $driver = \RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
        $navigator = new Navigator($driver, 'http://www.example.com');
    }
}
