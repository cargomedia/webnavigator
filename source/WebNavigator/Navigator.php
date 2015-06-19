<?php

namespace WebNavigator;

class Navigator {

    /** @var \WebDriver|\JavaScriptExecutor */
    private $_webDriver;

    /** @var string */
    private $_baseUrl;

    /** @var int */
    private $_waitTimeout;

    /**
     * @param \WebDriver $driver
     * @param string     $baseUrl
     * @param array|null $options
     */
    public function __construct(\WebDriver $driver, $baseUrl, array $options = null) {
        $this->_webDriver = $driver;
        $this->_baseUrl = (string) $baseUrl;

        $options = array_merge([
            'waitTimeout' => 5,
        ], (array) $options);
        $this->_waitTimeout = (int) $options['waitTimeout'];
    }

    /**
     * @return \WebDriver
     */
    public function getWebDriver() {
        return $this->_webDriver;
    }

    public function quit() {
        $this->_webDriver->quit();
    }

    /**
     * @param string $path
     */
    public function get($path) {
        $url = $this->_baseUrl . $path;
        $this->_webDriver->get($url);
    }

    /**
     * @param string $locator
     */
    public function click($locator) {
        $this->_findElement($locator)->click();
    }

    /**
     * @param string $locator
     */
    public function sendReturn($locator) {
        $this->_findElement($locator)->sendKeys(\WebDriverKeys::RETURN_KEY);
    }

    /**
     * @param string $locator
     * @throws \Exception
     * @throws \NoSuchElementException
     * @throws \TimeOutException
     */
    public function waitForElement($locator) {
        $this->_waitUntil(\WebDriverExpectedCondition::presenceOfElementLocated($this->_getLocator($locator)));
    }

    /**
     * @param string $javascript
     */
    public function waitForJs($javascript) {
        $this->_waitUntil(function (\JavaScriptExecutor $driver) use ($javascript) {
            return $driver->executeScript($javascript);
        });
    }

    public function waitForAjax() {
        $this->waitForJs('return (0 === $.active);');
    }

    /**
     * @param string $javascript
     * @return mixed
     */
    public function executeJs($javascript) {
        return $this->_webDriver->executeScript($javascript);
    }

    /**
     * @param string $locator
     * @return string
     */
    public function getText($locator) {
        return $this->_findElement($locator)->getText();
    }

    /**
     * @param string $locator
     * @return string
     */
    public function getHtml($locator) {
        return $this->getAttribute($locator, 'innerHTML');
    }

    /**
     * @param string $locator
     * @param string $attribute
     * @return string
     */
    public function getAttribute($locator, $attribute) {
        return $this->_findElement($locator)->getAttribute($attribute);
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->_webDriver->getCurrentURL();
    }

    /**
     * @param string $locator
     * @return bool
     */
    public function isDisplayed($locator) {
        return $this->_findElement($locator)->isDisplayed();
    }

    /**
     * @param string $path
     */
    public function takeScreenshot($path) {
        $this->_webDriver->takeScreenshot($path);
    }

    /**
     * @param string               $locator
     * @param string|string[]|bool $value
     * @throws \Exception
     */
    public function setField($locator, $value) {
        $element = $this->_findElement($locator);
        $tagName = $element->getTagName();
        switch ($tagName) {
            case 'select':
                $this->_setFieldSelect(new \WebDriverSelect($element), $value);
                break;
            case 'textarea':
                $this->_setFieldText($element, $value);
                break;
            case 'input':
                if ('radio' === $element->getAttribute('type') || 'checkbox' === $element->getAttribute('type')) {
                    if ($element->isSelected() != $value) {
                        $element->click();
                    }
                } else {
                    $this->_setFieldText($element, $value);
                }
                break;
            default:
                throw new \Exception("Cannot set field with tagName `{$tagName}`");
                break;
        }
    }

    /**
     * @param \WebDriverElement $element
     * @param string            $value
     */
    protected function _setFieldText(\WebDriverElement $element, $value) {
        $element->clear();
        $element->sendKeys($value);
    }

    /**
     * @param \WebDriverSelect $select
     * @param string|string[]  $value
     * @throws \Exception
     */
    protected function _setFieldSelect(\WebDriverSelect $select, $value) {
        $valueList = (array) $value;

        if ($select->isMultiple()) {
            $select->deselectAll();
        }

        $matched = false;
        foreach ($valueList as $value) {
            try {
                $select->selectByVisibleText($value);
                $matched = true;
            } catch (\NoSuchElementException $e) {
            }
        }
        if ($matched) {
            return;
        }
        foreach ($valueList as $value) {
            try {
                $select->selectByValue($value);
                $matched = true;
            } catch (\NoSuchElementException $e) {
            }
        }
        if (false === $matched) {
            throw new \Exception("Cannot select option `{$value}`");
        }
    }

    /**
     * @param \Closure|\WebDriverExpectedCondition $functionOrCondition
     */
    protected function _waitUntil($functionOrCondition) {
        $this->_webDriver->wait($this->_waitTimeout)->until($functionOrCondition);
    }

    /**
     * @param string $locator
     * @return \WebDriverElement
     */
    protected function _findElement($locator) {
        return $this->_webDriver->findElement($this->_getLocator($locator));
    }

    /**
     * @param string $locator
     * @return \WebDriverBy
     */
    protected function _getLocator($locator) {
        return \WebDriverBy::cssSelector($locator);
    }
}
