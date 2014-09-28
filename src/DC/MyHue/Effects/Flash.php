<?php

namespace DC\MyHue\Effects;

use DC\MyHue\Effects\SimpleEffect;
use DC\MyHue\RGBColor;

class Flash extends SimpleEffect  {

    private $currentXY;


    public function __construct(\Phue\Client $hue, $light, RGBColor $color, $brightness, $repeat = 0, $interval = 0) {
        parent::__construct($hue, $light, $color, $brightness, $repeat, $interval);

        $light = $this->hue->getLights()[$this->light];
        $this->currentXY = $light->getXY();

    }

    public final function execute() {

        $c = $this->repeat * 2;

        $light = $this->hue->getLights()[$this->light];
        $targetXY = $this->color->toCIE();

        do {

            $command = new \Phue\Command\SetLightState($light);
            $command->brightness($this->brightness)
                ->saturation(0);

            if($c % 2) {
                echo "Flashing target\n";
                $command->xy($targetXY[0], $targetXY[1]);
            }
            else {
                echo "Flashing current\n";
                $command->xy($this->currentXY['x'], $this->currentXY['y']);
            }

            // Transition time (in seconds).
            // 0 for "snapping" change
            // Any other value for gradual change between current and new state
            $command->transitionTime(0);

            // Send the command
            $this->hue->sendCommand(
                $command
            );

            sleep($this->interval);
            $c--;
        } while ( $c > 0);

    }

}
