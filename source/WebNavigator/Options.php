<?php

namespace WebNavigator;

class Options {

    /** @var array */
    private $_options;

    /**
     * @param array|null $options
     */
    public function __construct(array $options = null) {
        $defaults = [
            'waitTimeout' => 5,
        ];
        $this->_options = array_merge($defaults, (array) $options);
    }

    /**
     * @return int
     */
    public function getWaitTimeout() {
        return (int) $this->_options['waitTimeout'];
    }
}
