<?php

namespace WebNavigator;

use Facebook\WebDriver\WebDriverDimension;

class Options {

    /** @var array */
    private $_options;

    /**
     * @param array|null $options
     */
    public function __construct(array $options = null) {
        $defaults = [
            'waitTimeout' => 5,
            'windowSize'  => new WebDriverDimension(1024, 768),
        ];
        $options = array_merge($defaults, (array) $options);

        $this->setWaitTimeout($options['waitTimeout']);
        $this->setWindowSize($options['windowSize']);
    }

    /**
     * @return int
     */
    public function getWaitTimeout() {
        return (int) $this->_options['waitTimeout'];
    }

    /**
     * @param int $waitTimeout
     */
    public function setWaitTimeout($waitTimeout) {
        $waitTimeout = (int) $waitTimeout;
        $this->_options['waitTimeout'] = $waitTimeout;
    }

    /**
     * @return WebDriverDimension
     */
    public function getWindowSize() {
        return $this->_options['windowSize'];
    }

    /**
     * @param WebDriverDimension $windowSize
     */
    public function setWindowSize(WebDriverDimension $windowSize) {
        $this->_options['windowSize'] = $windowSize;
    }
}
