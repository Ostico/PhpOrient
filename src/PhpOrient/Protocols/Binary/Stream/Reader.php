<?php

namespace PhpOrient\Protocols\Binary\Stream;

class Reader {

    /**
     * Unpack a byte.
     *
     * @param mixed $value
     *
     * @return int the byte unpacked
     */
    public static function unpackByte( $value ) {
        return current( unpack( 'C', $value ) );
    }

    /**
     * Unpack a short.
     *
     * @param mixed $value
     *
     * @return int the short unpacked
     */
    public static function unpackShort( $value ) {
        $signed = current( unpack( 'n', $value ) );
        /**
         * If the bit sign is set,
         * because unpack treat all as unsigned, -1 equals to 65535 .
         * So, remove the sign and subtract -1
         * Ex:
         *    -4 equals 65532 as unsigned short
         *    65532 ^ 0x8000 = 32764
         *    32764 - 0x8000 = -4
         */
        if ( $signed & 0x8000 ){
            $signed = ( $signed ^ 0x8000 ) - 0x8000;
        }
        return $signed;
    }

    /**
     * Unpack an integer.
     *
     * @param mixed $value the value to unpack
     *
     * @return int the integer unpacked
     */
    public static function unpackInt( $value ) {
        $signed = current( unpack( 'N', $value ) );
        if (PHP_INT_SIZE === 8) {
            /**
             * If the bit sign is set,
             * because unpack treat all as unsigned, -1 equals to 2147483647 .
             * So, remove the sign and subtract -1
             * Ex:
             *    -1 equals 4294967295 as unsigned int
             *    4294967295 ^ 0x80000000 = 2147483647
             *    2147483647 - 0x80000000 = -1
             */
            if ( $signed & 0x80000000 ){
                $signed = ( $signed ^ 0x80000000 ) - 0x80000000;
            }
        }
        return $signed;
    }

    /**
     * Unpack a long.
     *
     * @param string $value
     *
     * @return int the long unpacked
     */
    public static function unpackLong( $value ) {

        $value = (string)$value;

        // If x64 system, just shift bytes to the left.
        if (PHP_INT_SIZE === 8) {
            $LongNum =  ord($value[0]) << 56 |
                        ord($value[1]) << 48 |
                        ord($value[2]) << 40 |
                        ord($value[3]) << 32 |
                        ord($value[4]) << 24 |
                        ord($value[5]) << 16 |
                        ord($value[6]) << 8  |
                        ord($value[7]);
        } else {

            $first = substr( $value, 0, 4 );
            $last  = substr( $value, 4, 4 );

            // First of all, unpack 8 bytes, divided into hi and low parts
            $hi  = current( unpack( 'N', $first ) );
            $low = current( unpack( 'N', $last ) );

            $isNegative = false;
            if ( $hi & 0x80000000 ){
                /**
                 * if the bit sign is set,
                 * remove the sign, subtract from the max signed integer
                 * and store the sign
                 * Ex:
                 *  -12345 & 0x7FFFFFFF     == 2147471303
                 *  0x7FFFFFFF - 2147471303 == 12344
                 */
                $hi = 0x7FFFFFFF - ( $hi & 0x7FFFFFFF );
                $isNegative = true;
            }

            if ( function_exists( "bcmul" ) ) {
                $LongNum = bcadd( bcmul( $hi, "4294967296" ), $low );
            } elseif ( function_exists( "gmp_mul" ) ) {
                $LongNum = gmp_strval( gmp_add( gmp_mul( $hi, "4294967296" ), $low ) );
            } else {

                // compute everything manually
                $a    = substr( $hi, 0, -5 );
                $b    = substr( $hi, -5 );
                $ac   = $a * 42949; // hope that float precision is enough
                $bd   = $b * 67296;
                $adbc = $a * 67296 + $b * 42949;
                $r4   = substr( $bd, -5 ) + substr( $low, -5 );
                $r3   = substr( $bd, 0, -5 ) + substr( $adbc, -5 ) + substr( $low, 0, -5 );
                $r2   = substr( $adbc, 0, -5 ) + substr( $ac, -5 );
                $r1   = substr( $ac, 0, -5 );
                while ( $r4 > 100000 ) {
                    $r4 -= 100000;
                    $r3++;
                }
                while ( $r3 > 100000 ) {
                    $r3 -= 100000;
                    $r2++;
                }
                while ( $r2 > 100000 ) {
                    $r2 -= 100000;
                    $r1++;
                }

                $r = sprintf( "%d%05d%05d%05d",
                        ( $r1 > 0 ? $r1 : - $r1 ),
                        ( $r2 > 0 ? $r2 : - $r2 ),
                        ( $r3 > 0 ? $r3 : - $r3 ),
                        ( $r4 > 0 ? $r4 : - $r4 )
                );

                $LongNum = ltrim( $r, "0" );
                if( $LongNum == '' ) $LongNum = '0';
                $LongNum = ( !$isNegative ? '' : '-' ) . $LongNum;

            }

        }

        return $LongNum;
    }

    /**
     * Unpack a string.
     *
     * @param mixed $value
     *
     * @return string|null the string unpack, or null if it's empty.
     */
    public static function unpackString( $value ) {
        $length = self::unpackInt( substr( $value, 0, 4 ) );
        $value  = substr( $value, 4, $length );
        if ( $length === -1 ) {
            return null;
        } else {
            if ( $length === 0 ) {
                return '';
            } else {
                return $value;
            }
        }
    }

    /**
     * Unpack bytes.
     *
     * @param mixed $value
     *
     * @return string|null the string unpack, or null if it's empty.
     */
    public static function unpackBytes( $value ) {
        $length = self::unpackInt( substr( $value, 0, 4 ) );
        $value  = substr( $value, 4, $length );
        if ( $length === -1 ) {
            return null;
        } else {
            if ( $length === 0 ) {
                return '';
            } else {
                return $value;
            }
        }
    }

}
