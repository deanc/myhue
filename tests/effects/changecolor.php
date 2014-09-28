<?php

use Phue\Client;
use DC\MyHue\Effects\ChangeColor;
use DC\MyHue\RGBColor;

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../config.php');

$hue = new Client(HUE_IP, HUE_USER);
$flash = new ChangeColor($hue, 2, new \DC\MyHue\XYPoint(0.3,0.15), 255, 5, 1);
$flash->execute();