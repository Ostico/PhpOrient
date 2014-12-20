<?php

namespace PhpOrient\Protocols\Binary\Stream;

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

            $bitString  = '';
            if ( function_exists( "gmp_mod" ) ) {
                while ( $value !== '0' ) {
                    $bitString = gmp_strval( gmp_mod( $value,'2') ) . $bitString;
                    $value = gmp_strval( gmp_div_q( $value, '2' ) );
                };
            } elseif ( function_exists( "bcmod" ) ) {
                while ($value !== '0') {
                    $bitString = bcmod($value, '2') . $bitString;
                    $value = bcdiv($value, '2');
                };
            } else {
                while ( $value != 0 ) {
                    list( $value, $remainder ) = self::str_div( $value );
                    $bitString = $remainder . $bitString;
                } ;
            }
            $bitString = str_pad( $bitString, 64, '0', STR_PAD_LEFT );
            $hi = substr( $bitString, 0, 32 );
            $lo = substr( $bitString, 32, 32 );
            $hiBin = pack( 'H*', str_pad( base_convert( $hi, 2, 16 ), 8, 0, STR_PAD_LEFT ) );
            $loBin = pack( 'H*', str_pad( base_convert( $lo, 2, 16 ), 8, 0, STR_PAD_LEFT ) );
            $binaryString = $hiBin . $loBin;

        }

        return $binaryString;

    }

    /**
     * Divide an arbitrary precision number by 2
     * and take the remainder also
     *
     * @thanks to https://github.com/luca-mastrostefano for the precious help
     *
     * @param $value
     *
     * @return array
     */
    protected static function str_div( $value ) {

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
