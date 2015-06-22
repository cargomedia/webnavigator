<?php

namespace WebNavigator;

class Dimension {

    /** @var int */
    private $_height;

    /** @var int */
    private $_width;

    /**
     * @param int $width
     * @param int $height
     */
    public function __construct($width, $height) {
        $this->_width = $width;
        $this->_height = $height;
    }

    /**
     * @return int
     */
    public function getWidth() {
        return $this->_width;
    }

    /**
     * @return int
     */
    public function getHeight() {
        return $this->_height;
    }
}
