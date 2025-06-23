<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberToWord extends Model {
    public static $hyphen      = '-';
    public static $conjunction = ' and ';
    public static $separator   = ', ';
    public static $negative    = 'negative ';
    public static $decimal     = ' point ';
    public static $dictionary  = [
        0       => 'zero',
        1       => 'one',
        2       => 'two',
        3       => 'three',
        4       => 'four',
        5       => 'five',
        6       => 'six',
        7       => 'seven',
        8       => 'eight',
        9       => 'nine',
        10      => 'ten',
        11      => 'eleven',
        12      => 'twelve',
        13      => 'thirteen',
        14      => 'fourteen',
        15      => 'fifteen',
        16      => 'sixteen',
        17      => 'seventeen',
        18      => 'eighteen',
        19      => 'nineteen',
        20      => 'twenty',
        30      => 'thirty',
        40      => 'fourty',
        50      => 'fifty',
        60      => 'sixty',
        70      => 'seventy',
        80      => 'eighty',
        90      => 'ninety',
        100     => 'hundred',
        1000    => 'thousand',
        1000000 => 'million',
    ];

    public static function convert_number_to_words( int $number ) {
        if ( !is_numeric( $number ) ) {
            return null;
        }

        $string = '';
        switch ( true ) {
        case $number < 21:
            $string = self::$dictionary[$number];
            break;
        case $number < 100:
            $tens   = ( (int) ( $number / 10 ) ) * 10;
            $units  = $number % 10;
            $string = self::$dictionary[$tens];
            if ( $units ) {
                $string .= self::$hyphen . self::$dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string    = self::$dictionary[$hundreds] . ' ' . self::$dictionary[100];
            if ( $remainder ) {
                $string .= self::$conjunction . self::convert_number_to_words( $remainder );
            }
            break;
        default:
            $baseUnit     = pow( 1000, floor( log( $number, 1000 ) ) );
            $numBaseUnits = (int) ( $number / $baseUnit );
            $remainder    = $number % $baseUnit;
            $string       = self::convert_number_to_words( $numBaseUnits ) . ' ' . self::$dictionary[$baseUnit];
            if ( $remainder ) {
                $string .= $remainder < 100 ? self::$conjunction : self::$separator;
                $string .= self::convert_number_to_words( $remainder );
            }
            break;
        }
        return $string;
    }
}
