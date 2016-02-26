<?php

namespace WebNavigatorTests;

use WebNavigator\Dimension;
use WebNavigator\Navigator;

class NavigatorTest extends TestCase {

    public function testScope() {
        $this->_navigator->get('/test-scope.html');
        $this->assertSame(null, $this->_navigator->getLocatorPrefix());
        $this->assertSame('Test1', $this->_navigator->getText('.text'));

        $this->_navigator->scope('#id-scope');
        $this->assertSame('#id-scope', $this->_navigator->getLocatorPrefix());
        $this->assertSame('Test2', $this->_navigator->getText('.text'));
    }

    public function testScopeWithBlock() {
        $this->_navigator->scope('.foo', function(Navigator $navigator) {
            $this->assertSame('.foo', $navigator->getLocatorPrefix());
        });
        $this->assertSame(null, $this->_navigator->getLocatorPrefix());
    }

    public function testGetUrl() {
        $this->_navigator->get('/foo');
        $this->assertStringEndsWith('/foo', $this->_navigator->getUrl());
    }

    public function testSetWindowSize() {
        $this->_navigator->get('/test1.html');
        $this->_navigator->setWindowSize(new Dimension(123, 321));
        $this->assertSame(123, $this->_navigator->executeJs('return window.innerWidth'));
        $this->assertSame(321, $this->_navigator->executeJs('return window.innerHeight'));
    }

    public function testConstructorWindowSize() {
        $this->assertSame(1024, $this->_navigator->executeJs('return window.innerWidth'));
        $this->assertSame(768, $this->_navigator->executeJs('return window.innerHeight'));
    }

    public function testGetText() {
        $this->_navigator->get('/test1.html');
        $this->assertSame('Hello World', $this->_navigator->getText('#id-hello'));
    }

    public function testGetHtml() {
        $this->_navigator->get('/test1.html');
        $this->assertSame('Hello <span>World</span>', $this->_navigator->getHtml('#id-hello'));
    }

    public function testGetAttribute() {
        $this->_navigator->get('/test1.html');
        $this->assertSame('id-hello', $this->_navigator->getAttribute('#id-hello', 'id'));
    }

    public function testIsDisplayed() {
        $this->_navigator->get('/test1.html');
        $this->assertSame(true, $this->_navigator->isDisplayed('#id-hello'));
        $this->assertSame(false, $this->_navigator->isDisplayed('#id-hidden'));
    }

    public function testClick() {
        $this->_navigator->get('/test1.html');
        $this->_navigator->click('#id-link-foo');
        $this->assertStringEndsWith('/my-link', $this->_navigator->getUrl());
    }

    public function testSendReturn() {
        $this->_navigator->get('/test1.html');
        $this->_navigator->sendReturn('#id-input-text');
        $this->assertContains('/my-form-action', $this->_navigator->getUrl());
    }

    public function testSetFieldSelect() {
        $this->_navigator->get('/test1.html');
        $this->assertSame('my-value-1', $this->_navigator->getAttribute('#id-select', 'value'));
        $this->_navigator->setField('#id-select', 'my-value-2');
        $this->assertSame('my-value-2', $this->_navigator->getAttribute('#id-select', 'value'));
    }

    public function testSetFieldTextarea() {
        $this->_navigator->get('/test1.html');
        $this->assertSame('my-text', $this->_navigator->getAttribute('#id-textarea', 'value'));
        $this->_navigator->setField('#id-textarea', 'my-text-2');
        $this->assertSame('my-text-2', $this->_navigator->getAttribute('#id-textarea', 'value'));
    }

    public function testSetFieldInputText() {
        $this->_navigator->get('/test1.html');
        $this->assertSame('my-text', $this->_navigator->getAttribute('#id-input-text', 'value'));
        $this->_navigator->setField('#id-input-text', 'my-text-2');
        $this->assertSame('my-text-2', $this->_navigator->getAttribute('#id-input-text', 'value'));
    }

    public function testSetFieldInputRadio() {
        $this->_navigator->get('/test1.html');
        $this->assertSame(null, $this->_navigator->getAttribute('#id-input-radio-2', 'selected'));
        $this->_navigator->setField('#id-input-radio-2', true);
        $this->assertSame('true', $this->_navigator->getAttribute('#id-input-radio-2', 'selected'));

        $this->_navigator->click('#id-input-submit');
        $this->assertContains('my-radio=my-value-2', $this->_navigator->getUrl());
    }

    public function testSetFieldInputCheckbox() {
        $this->_navigator->get('/test1.html');
        $this->assertSame(null, $this->_navigator->getAttribute('#id-input-checkbox-1', 'selected'));
        $this->assertSame(null, $this->_navigator->getAttribute('#id-input-checkbox-2', 'selected'));

        $this->_navigator->setField('#id-input-checkbox-1', true);
        $this->_navigator->setField('#id-input-checkbox-2', true);
        $this->_navigator->setField('#id-input-checkbox-1', false);
        $this->assertSame(null, $this->_navigator->getAttribute('#id-input-checkbox-1', 'selected'));
        $this->assertSame('true', $this->_navigator->getAttribute('#id-input-checkbox-2', 'selected'));

        $this->_navigator->click('#id-input-submit');
        $this->assertNotContains('my-checkbox=my-value-1', $this->_navigator->getUrl());
        $this->assertContains('my-checkbox=my-value-2', $this->_navigator->getUrl());
    }

    public function testWaitForElement() {
        $this->_navigator->get('/test1.html');

        $this->_navigator->executeJs('
            setTimeout(function(){
                var el = document.createElement("div");
                el.id = "id-new";
                el.textContent = "My New Element";
                document.body.appendChild(el);
            }, 100);'
        );
        $this->_navigator->waitForElement('#id-new');
        $this->assertSame('My New Element', $this->_navigator->getText('#id-new'));
    }

    public function testWaitForJs() {
        $this->_navigator->get('/test1.html');

        $this->_navigator->executeJs('
            setTimeout(function(){
                window.myVariable = "foo";
            }, 100);'
        );
        $this->_navigator->waitForJs('return (window.myVariable === "foo")');
        $this->assertSame('foo', $this->_navigator->executeJs('return window.myVariable'));
    }

    /**
     * @expectedException \Facebook\WebDriver\Exception\TimeOutException
     */
    public function testWaitForJsWithTimeout() {
        $this->_navigator->get('/test1.html');

        $this->_navigator->executeJs('
            setTimeout(function(){
                window.myVariable = "foo";
            }, 2000);'
        );
        $this->_navigator->waitForJs('return (window.myVariable === "foo")', 1);
    }

    public function testWaitForAjax() {
        $this->_navigator->get('/test-jquery.html');

        $this->_navigator->executeJs('$.ajax("/php/sleep.php?duration=0.1");');
        $this->assertSame(1, $this->_navigator->executeJs('return $.active'));

        $this->_navigator->waitForAjax();
        $this->assertSame(0, $this->_navigator->executeJs('return $.active'));
    }
}
