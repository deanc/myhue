<?php

namespace DC\MyHue\Effects;

use DC\MyHue\XYPoint;

abstract class SimpleEffect {

    protected $hue;
    protected $light;
    protected $color;
    protected $brightness;
    protected $interval;
    protected $repeat;

    public function __construct(\Phue\Client $hue, $light, XYPoint $color, $brightness, $repeat = 0, $interval = 0) {
        $this->hue = $hue;
        $this->light = $light;
        $this->color = $color;
        $this->brightness = $brightness;
        $this->repeat = $repeat;
        $this->interval = $interval;
    }

    abstract public function execute();
}
