<?php

namespace PhpOrient\Protocols\Binary\Stream;

use PhpOrient\Protocols\Binary\SocketTransport;

class Writer {

    /**
     * Pack a byte.
     *
     * @param int $value
     *
     * @return string the packed byte.
     */
    public static function packByte( $value ) {
        return pack( 'C', $value );
    }

    /**
     * Pack a short.
     *
     * @param int $value
     *
     * @return string the packed short
     */
    public static function packShort( $value ) {
        return pack( 'n', $value );
    }

    /**
     * Pack a long.
     *
     * If it is a 32bit PHP we suppose that this log is treated by bcmath
     * TODO 32bit
     *
     * @param int|string $value
     *
     * @return string the packed long
     */
    public static function packLong( $value ) {

        if ( PHP_INT_SIZE > 4 ) {
            $value = (int) $value;
            $binaryString = chr( $value >> 56 & 0xFF ) .
                    chr( $value >> 48 & 0xFF ) .
                    chr( $value >> 40 & 0xFF ) .
                    chr( $value >> 32 & 0xFF ) .
                    chr( $value >> 24 & 0xFF ) .
                    chr( $value >> 16 & 0xFF ) .
                    chr( $value >> 8 & 0xFF ) .
                    chr( $value & 0xFF );

        } else {

            /*
             * To get the two's complement of a binary number,
             * the bits are inverted, or "flipped",
             * by using the bitwise NOT operation;
             * the value of 1 is then added to the resulting value
             */
            $bitString  = '';
            $isNegative = $value{0} == '-';
            if ( function_exists( "bcmod" ) ) {

                //add 1 for the two's complement
                if( $isNegative ){
                    $value = bcadd( $value, '1' );
                }

                while ( $value !== '0' ) {
                    $bitString = (string)abs( (int)bcmod( $value, '2' ) ) . $bitString;
                    $value     = bcdiv( $value, '2' );
                };

            } elseif ( function_exists( "gmp_mod" ) ) {

                //add 1 for the two's complement
                if( $isNegative ){
                    $value = gmp_strval( gmp_add( $value, '1' ) );
                }

                while ( $value !== '0' ) {
                    $bitString = gmp_strval( gmp_abs( gmp_mod( $value, '2' ) ) ) . $bitString;
                    $value = gmp_strval( gmp_div_q( $value, '2' ) );
                };

            } else {
                while ( $value != 0 ) {
                    list( $value, $remainder ) = self::str2bin( (string)$value );
                    $bitString = $remainder . $bitString;
                } ;
            }

            //Now do the logical not for the two's complement last phase
            if( $isNegative ){
                $len = strlen( $bitString );
                for( $x = 0; $x < $len; $x++ ){
                    $bitString{$x} = ( $bitString{$x} == '1' ? '0' : '1' );
                }
            }

            //pad to have 64 bit
            if( $bitString != '' && $isNegative ){
                $bitString = str_pad( $bitString, 64, '1', STR_PAD_LEFT );
            } else {
                $bitString = str_pad( $bitString, 64, '0', STR_PAD_LEFT );
            }

            $hi = substr( $bitString, 0, 32 );
            $lo = substr( $bitString, 32, 32 );
            $hiBin = pack( 'H*', str_pad( base_convert( $hi, 2, 16 ), 8, 0, STR_PAD_LEFT ) );
            $loBin = pack( 'H*', str_pad( base_convert( $lo, 2, 16 ), 8, 0, STR_PAD_LEFT ) );
            $binaryString = $hiBin . $loBin;

        }

        return $binaryString;

    }

    /**
     * String subtraction, subtract 1 from numeric string
     *
     * @param $x
     *
     * @return string
     */
    protected static function sub_1( $x ) {

        if( strlen( $x ) == 1 ) return (string)( $x - 1 );

        for( $idx = 1; $idx <= strlen($x); $idx++ ){
            $res = $x{ strlen($x) - $idx } - 1;
            if ( $res < 0 ){
                $x{ strlen($x) - $idx } = '9';
            } else {
                $x{ strlen($x) - $idx } = $res;
                break;
            }
        }

        $x = ltrim( $x, '-0' );
        return $x;

    }

    /**
     * Transform an arbitrary precision number ( string )
     * to a binary string of bits and take the remainder also
     *
     * @thanks to https://github.com/luca-mastrostefano for the precious help
     *
     * @param $value
     *
     * @return array
     */
    protected static function str2bin( $value ) {

        if( $value{0} == '-' ){
            //add 1 ( so subtract to the number modulus )
            //for the first phase of two's complement
            $value = self::sub_1( $value );
        }

        $valueLen      = strlen( $value );
        $totalQuotient = '';
        $lastRemainder = 0;
        for ( $idx = 0; $idx < $valueLen; $idx++ ) {
            //48 is the ascii value of 0
            $actualDividend = $lastRemainder * 10 + ord( $value{$idx} ) - 48;
            if ( $actualDividend < 2 ) {
                $totalQuotient .= 0;
                $idx++;

                if( $idx == $valueLen ){
                    $lastRemainder = $actualDividend;
                    break;
                }

                $actualDividend = $actualDividend * 10 + ord( $value{$idx} ) - 48;
            }

            $quotient      = (int)( $actualDividend / 2 );
            $lastRemainder = $actualDividend % 2;
            $totalQuotient .= $quotient;

        }

        if ( $totalQuotient{0} === '0' ) {
            $totalQuotient = substr( $totalQuotient, 1 );
        }

        return [ (string)$totalQuotient, (string)$lastRemainder ];
    }

    /**
     * Pack an integer.
     *
     * @param int $value
     *
     * @return string the packed integer
     */
    public static function packInt( $value ) {
        return pack( 'N', $value );
    }

    /**
     * Pack a string.
     *
     * @param string $value
     *
     * @return string the packed string.
     */
    public static function packString( $value ) {
        if ( $value === null ) {
            return self::packInt( -1 );
        } else {
            return self::packInt( strlen( $value ) ) . $value;
        }
    }

    /**
     * Pack bytes.
     *
     * @param string $value
     *
     * @return string the packed string.
     */
    public static function packBytes( $value ) {
        if ( $value === null ) {
            return self::packInt( -1 );
        } else {
            return self::packInt( strlen( $value ) ) . $value;
        }
    }

}
