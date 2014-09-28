<?php

namespace DC\MyHue;

class XYPoint {
    public $x;
    public $y;

    function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }
}