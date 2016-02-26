<?php

namespace WebNavigatorTests;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use WebNavigator\Navigator;

class TestCase extends \PHPUnit_Framework_TestCase {

    /** @var Navigator|null */
    protected $_navigator;

    public function setUp() {
        $this->_navigator = $this->_createNavigator();
    }

    public function tearDown() {
        if (isset($this->_navigator)) {
            $this->_navigator->quit();
        }
    }

    /**
     * @return Navigator
     */
    protected function _createNavigator() {
        $capabilities = new DesiredCapabilities([WebDriverCapabilityType::BROWSER_NAME => 'phantomjs']);
        $driver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
        return new Navigator($driver, 'http://localhost:1234');
    }
}
