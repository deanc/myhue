<?php

use Phue\Client;
use DC\MyHue\Effects\Flash;
use DC\MyHue\RGBColor;

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../config.php');

$hue = new Client(HUE_IP, HUE_USER);
$flash = new Flash($hue, 2, new RGBColor(215,40,40), 255, 5, 1);
$flash->execute();