<?php

namespace App;


class ColorTool
{
  /**
   * Converts a string to a random color.
   *
   * @return string
   */
  static public function stringToColorCode($str)
  {
    $code = dechex(crc32($str) + 39);
    $htmlCode = substr($code, 0, 6);
    $r = hexdec($htmlCode[0] . $htmlCode[1]);
    $g = hexdec($htmlCode[2] . $htmlCode[3]);
    $b = hexdec($htmlCode[4] . $htmlCode[5]);

    return [$b + ($g << 0x8) + ($r << 0x10), $htmlCode];
  }
  /**
   * Converts an RGB color to HSL.
   *
   * @return string
   */
  static public function RGBToHSL($RGB)
  {
    $r = 0xFF & ($RGB >> 0x10);
    $g = 0xFF & ($RGB >> 0x8);
    $b = 0xFF & $RGB;

    $r = ((float)$r) / 255.0;
    $g = ((float)$g) / 255.0;
    $b = ((float)$b) / 255.0;

    $maxC = max($r, $g, $b);
    $minC = min($r, $g, $b);

    $l = ($maxC + $minC) / 2.0;

    if ($maxC == $minC) {
      $s = 0;
      $h = 0;
    } else {
      if ($l < .5) {
        $s = ($maxC - $minC) / ($maxC + $minC);
      } else {
        $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
      }
      if ($r == $maxC)
        $h = ($g - $b) / ($maxC - $minC);
      if ($g == $maxC)
        $h = 2.0 + ($b - $r) / ($maxC - $minC);
      if ($b == $maxC)
        $h = 4.0 + ($r - $g) / ($maxC - $minC);

      $h = $h / 6.0;
    }

    $h = (int)round(255.0 * $h);
    $s = (int)round(255.0 * $s);
    $l = (int)round(255.0 * $l);
    return (object)array('hue' => $h, 'saturation' => $s, 'lightness' => $l);
  }


  /**
   * Checks if a color is light
   *
   * @return bool
   */
  public static function isLight($string)
  {
    $RGB = self::stringToColorCode($string);
    $HSL = self::RGBToHSL($RGB[0]);
    return ($HSL->lightness > 200);
  }

}
