<?php

require_once __DIR__ . '/vendor/autoload.php';

function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}

function rgbtocie($rgb) {
	
}

$client = new \Phue\Client('192.168.1.100', 'deanclatworthy');

// get the bedroom light
$light = $client->getLights()[2];
if($light->getName() == 'Bedroom') {
	echo "Got bedroom light....\n";
	$light->setOn(true);
	$c = 50;
	do {
		$command = new \Phue\Command\SetLightState($light);
		$command->brightness($c % 2 ? 255 : 0)
		        ->hue(0)
		        ->saturation(0);

		// Transition time (in seconds).
		// 0 for "snapping" change
		// Any other value for gradual change between current and new state
		$command->transitionTime(0);

		// Send the command
		$client->sendCommand(
    			$command
		);
		sleep(.1);
		$c--;
	} while ($c > 0);
}
