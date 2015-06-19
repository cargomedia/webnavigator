<?php

namespace WebNavigatorTests;

use WebNavigator\Navigator;

class NavigatorTest extends TestCase {

    public function testGetUrl() {
        $this->_navigator->get('/foo');
        $this->assertStringEndsWith('/foo', $this->_navigator->getUrl());
    }

    public function testGetText() {
        $this->_navigator->get('/test1.html');
        $this->assertSame('Hello World', $this->_navigator->getText('#id-hello'));
    }

    public function testGetHtml() {
        $this->_navigator->get('/test1.html');
        $this->assertSame('Hello <span>World</span>', $this->_navigator->getHtml('#id-hello'));
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
        $this->assertSame('my-value-1', $this->_navigator->executeJs('return document.getElementById("id-select").value'));
        $this->_navigator->setField('#id-select', 'my-value-2');
        $this->assertSame('my-value-2', $this->_navigator->executeJs('return document.getElementById("id-select").value'));
    }

    public function testSetFieldTextarea() {
        $this->_navigator->get('/test1.html');
        $this->assertSame('my-text', $this->_navigator->executeJs('return document.getElementById("id-textarea").value'));
        $this->_navigator->setField('#id-textarea', 'my-text-2');
        $this->assertSame('my-text-2', $this->_navigator->executeJs('return document.getElementById("id-textarea").value'));
    }

    public function testSetFieldInputText() {
        $this->_navigator->get('/test1.html');
        $this->assertSame('my-text', $this->_navigator->executeJs('return document.getElementById("id-input-text").value'));
        $this->_navigator->setField('#id-input-text', 'my-text-2');
        $this->assertSame('my-text-2', $this->_navigator->executeJs('return document.getElementById("id-input-text").value'));
    }

    public function testSetFieldInputRadio() {
        $this->_navigator->get('/test1.html');
        $this->_navigator->setField('#id-input-radio-2', null);
        $this->_navigator->click('#id-input-submit');
        $this->assertContains('my-radio=my-value-2', $this->_navigator->getUrl());
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

    public function testWaitForAjax() {
        $this->_navigator->get('/test-jquery.html');

        $this->_navigator->executeJs('$.ajax("http://echo.jsontest.com/");');
        $this->assertSame(1, $this->_navigator->executeJs('return $.active'));

        $this->_navigator->waitForAjax();
        $this->assertSame(0, $this->_navigator->executeJs('return $.active'));
    }
}
