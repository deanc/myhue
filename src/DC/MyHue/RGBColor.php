<?php

namespace DC\MyHue;

use DC\MyHue\XYPoint;

class RGBColor {

    private $red = 0;
    private $green = 0;
    private $blue = 0;

    public function __construct($red, $green, $blue) {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;

        // CIE defs
        $this->cie = array();
        $this->cie['red'] = new XYPoint(0.675, 0.322);
        $this->cie['lime'] = new XYPoint(0.4091, 0.518);
        $this->cie['blue'] = new XYPoint(0.167, 0.04);
    }

    private function _crossProduct($p1, $p2) {
        return ($p1->x * $p2->y - $p1->y * $p2->x);
    }

    private function _checkPointInLampsReach($p) {
        $v1 = new XYPoint($this->cie['lime']->x - $this->cie['red']->x, $this->cie['lime']->y - $this->cie['red']->y);
        $v2 = new XYPoint($this->cie['blue']->x - $this->cie['red']->x, $this->cie['blue']->y - $this->cie['red']->y);

        $q = new XYPoint($p->x - $this->cie['red']->x, $p->y - $this->cie['red']->y);

        $s = $this->_crossProduct($q, $v2) / $this->_crossProduct($v1, $v2);
        $t = $this->_crossProduct($v1, $q) / $this->_crossProduct($v1, $v2);

        return ($s >= 0.0) && ($t >= 0.0) && ($s + $t <= 1.0);
    }

    private function _getClosestPointToPoint($A, $B, $P) {
        $AP = new XYPoint($P->x - $A->x, $P->y - $A.y);
        $AB = new XYPoint($B->x - $A->x, $B->y - $A->y);
        $ab2 = $AB->x * $AB->x + $AB->y * $AB->y;
        $ap_ab = $AP->x * $AB->x + $AP->y * $AB->y;
        $t = ap_ab / ab2;

        if ($t < 0.0) {
            $t = 0.0;
        } else if ($t > 1.0) {
            $t = 1.0;
        }

        return new XYPoint($A->x + $AB->x * $t, $A->y + $AB->y * $t);
    }

    private function _getDistanceBetweenTwoPoints ($one, $two) {
        $dx = $one->x - $two->x; // horizontal difference
        $dy = $one->y - $two->y; // vertical difference

        return sqrt($dx * $dx + $dy * $dy);
    }

    public function toCIE() {

        $r = ($this->red > 0.04045) ? pow(($this->red + 0.055) / (1.0 + 0.055), 2.4) : ($this->red / 12.92);
        $g = ($this->green > 0.04045) ? pow(($this->green + 0.055) / (1.0 + 0.055), 2.4) : ($this->green / 12.92);
        $b = ($this->blue > 0.04045) ? pow(($this->blue + 0.055) / (1.0 + 0.055), 2.4) : ($this->blue / 12.92);

        $X = $r * 0.4360747 + $g * 0.3850649 + $b * 0.0930804;
        $Y = $r * 0.2225045 + $g * 0.7168786 + $b * 0.0406169;
        $Z = $r * 0.0139322 + $g * 0.0971045 + $b * 0.7141733;

        $cx = $X / ($X + $Y + $Z);
        $cy = $Y / ($X + $Y + $Z);

        $cx = is_nan(cx) ? 0.0 : $cx;
        $cy = is_nan(cy) ? 0.0 : $cy;

        //Check if the given XY value is within the colourreach of our lamps.
        $xyPoint = new XYPoint($cx, $cy);
        $inReachOfLamps = $this->_checkPointInLampsReach($xyPoint);

        if (!$inReachOfLamps) {

            //Color is unreproducible, find the closest point on each line in the CIE 1931 'triangle'.
            $pAB = $this->getClosestPointToPoint($this->cie['red'], $this->cie['lime'], $xyPoint);
            $pAC = $this->getClosestPointToPoint($this->cie['blue'], $this->cie['red'], $xyPoint);
            $pBC = $this->getClosestPointToPoint($this->cie['lime'], $this->cie['blue'], $xyPoint);

            // Get the distances per point and see which point is closer to our Point.
            $dAB = $this->getDistanceBetweenTwoPoints($xyPoint, $pAB);
            $dAC = $this->getDistanceBetweenTwoPoints($xyPoint, $pAC);
            $dBC = $this->getDistanceBetweenTwoPoints($xyPoint, $pBC);

            $lowest = $dAB;
            $closestPoint = $pAB;

            if ($dAC < $lowest) {
                $lowest = $dAC;
                $closestPoint = $pAC;
            }
            if ($dBC < $lowest) {
                $lowest = $dBC;
                $closestPoint = $pBC;
            }

            // Change the xy value to a value which is within the reach of the lamp.
            $cx = $closestPoint->x;
            $cy = $closestPoint->y;
        }

        return array(
            'x' => $cx, 'y' => $cy
        );

//        // 1) normalize rgb
//        $red = $this->red / 255;
//        $green = $this->green / 255;
//        $blue = $this->blue / 255;
//
//        // 2) Apply gamma correction
//        $redf = ($red > 0.04045) ? pow(($red + 0.055) / (1.0 + 0.055), 2.4) : ($red / 12.92);
//        $greenf = ($green > 0.04045) ? pow(($green + 0.055) / (1.0 + 0.055), 2.4) : ($green / 12.92);
//        $bluef = ($blue > 0.04045) ? pow(($blue + 0.055) / (1.0 + 0.055), 2.4) : ($blue / 12.92);
//
//        // 3) Convert the RGB values to XYZ using the Wide RGB D65 conversion formula
//        $x = $redf * 0.649926 + $green * 0.103455 + $blue * 0.197109;
//        $y = $redf * 0.234327 + $green * 0.743075 + $blue * 0.022598;
//        $z = $redf * 0.0000000 + $green * 0.053077 + $blue * 1.035763;
//
//        // Calculate final xy values
//        $x = $x / ($x + $y + $z);
//        $y = $y / ($x + $y + $z);

//        return array(
//            'x' => $x
//            ,'y' => $y
//        );
    }
}