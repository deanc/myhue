<?php

namespace DC\MyHue\Effects;

use DC\MyHue\Effects\SimpleEffect;
use DC\MyHue\RGBColor;

class ChangeColor extends SimpleEffect  {

    public final function execute() {

        $light = $this->hue->getLights()[$this->light];
        var_dump($this->color);

        $command = new \Phue\Command\SetLightState($light);
        $command->brightness($this->brightness)
            ->xy($this->color->x, $this->color->y)
            ->saturation(0);


        // Transition time (in seconds).
        // 0 for "snapping" change
        // Any other value for gradual change between current and new state
        $command->transitionTime(5);

        // Send the command
        $this->hue->sendCommand(
            $command
        );

    }

}