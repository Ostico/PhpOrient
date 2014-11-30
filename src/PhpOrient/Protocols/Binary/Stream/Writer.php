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
        return chr( $value );
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
     * Pack a long.
     *
     * @todo 64 bit not yet supported!
     *
     * @param int $value
     *
     * @return string the packed long
     */
    public static function packLong( $value ) {

        if ( PHP_INT_SIZE > 4 ) {

            $binaryString = chr( $value >> 56 & 0xFF ) .
                            chr( $value >> 48 & 0xFF ) .
                            chr( $value >> 40 & 0xFF ) .
                            chr( $value >> 32 & 0xFF ) .
                            chr( $value >> 24 & 0xFF ) .
                            chr( $value >> 16 & 0xFF ) .
                            chr( $value >> 8 & 0xFF ) .
                            chr( $value & 0xFF );

        } else {
            $binaryString = str_repeat( chr( 0 ), 4 ) . pack( 'N', $value );
        }

        return $binaryString;

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
