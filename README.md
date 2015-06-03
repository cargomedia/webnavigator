webnavigator
============
Wrapper for [facebook/php-webdriver](https://github.com/facebook/php-webdriver) for simple automated acceptance tests.

PhantomJS
---------
*WebNavigator* can connect to the *WebDriver* server of *PhantomJS*.
Start PhantomJS in a console like this:
```sh
phantomjs --webdriver=4444 --ssl-protocol=tlsv1 --ignore-ssl-errors=true
```

Example
-------
Setting up a *WebNavigator* instance in a *PHPUnit* test case and doing some basic tests:

```php
class MyTest extends \PHPUnit_Framework_TestCase {

    /** @var \WebNavigator\Navigator */
    private $_navigator;

    protected function setUp() {
        $capabilities = new \DesiredCapabilities([\WebDriverCapabilityType::BROWSER_NAME => 'phantomjs']);
        $driver = \RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
        $this->_navigator = new \WebNavigator\Navigator($driver, 'https://www.denkmal.org');
    }

    protected function tearDown() {
        $this->_navigator->quit();
    }

    public function testAddPage() {
        $this->_navigator->get('/events');

        $this->_navigator->click('.addButton a');
        $this->_navigator->waitForElement('.Denkmal_Page_Add');
        $this->assertContains('Event hinzufügen', $this->_navigator->getText('h1'));
        $this->assertContains('/add', $this->_navigator->getUrl());

        $this->_navigator->takeScreenshot('screenshot.png');
    }
}

```
