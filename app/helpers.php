<?php

/**
 * Retrieve our Locale instance
 *
 * @return App\Locale
 */
function locale()
{
    return app()->make(App\Locale::class);
}
/**
 * RoundEXP - round exp values to facebook like floats
 *
 * @param float $num
 * @return float
 */
function roundExp($num)
{
    if ($num > 1000) {
        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('k', 'm', 'b', 't');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int)$x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];
        return $x_display;
    }
    return $num;
}
/**
 * Returns a human readable file size
 *
 * @param integer $bytes
 * Bytes contains the size of the bytes to convert
 *
 * @param integer $decimals
 * Number of decimal places to be returned
 *
 * @return string a string in human readable format
 *
 * */
function human_file_size($bytes, $decimals = 2)
{
    $sz = 'BKMGTPE';
    $factor = (int)floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . $sz[$factor];

}

function getLevel($xp)
{
    $i = 0;
    $exp = 0;
    do {
        $i++;
        $exp = ($i ** 1.5) * 100;
    } while ($exp < $xp);
    return $i;
}


function isHomepage()
{
    return url()->current() == env('APP_URL') . "home";
}