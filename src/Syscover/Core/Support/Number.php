<?php namespace Syscover\Core\Support;

class Number
{
    public static function roundUp($number, $precision = 2)
    {
        $fig = pow(10, $precision);
        return (ceil($number * $fig) / $fig);
    }
}
