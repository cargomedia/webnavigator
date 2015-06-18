<?php

namespace WebNavigatorTests;

use Cocur\BackgroundProcess\BackgroundProcess;
use WebNavigator\Navigator;

class TestCase extends \PHPUnit_Framework_TestCase {

    /** @var BackgroundProcess */
    protected $_webServer;

    /** @var Navigator|null */
    protected $_navigator;

    public function setUp() {
        $capabilities = new \DesiredCapabilities([\WebDriverCapabilityType::BROWSER_NAME => 'phantomjs']);
        $driver = \RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
        $this->_navigator = new Navigator($driver, 'http://localhost:1234');
    }

    public function tearDown() {
        if (isset($this->_navigator)) {
            $this->_navigator->quit();
        }
    }
}
