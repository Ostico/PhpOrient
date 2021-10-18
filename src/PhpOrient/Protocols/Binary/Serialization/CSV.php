<?php

namespace PhpOrient\Protocols\Binary\Serialization;

use PhpOrient\Configuration\Constants;
use PhpOrient\Protocols\Binary\Abstracts\SerializableInterface;
use PhpOrient\Protocols\Binary\Data\Bag;
use PhpOrient\Protocols\Binary\Data\ID;

class CSV {

    /**
     * Deserialize a record.
     *
     * @param string $input The input to un-serialize.
     *
     * @return array|null The un-serialized document, or null if the input is empty.
     */
    public static function unserialize( $input ) {
        if ( !$input ) {
            return null;
        }

        $input = trim( $input );
        $record = [ ];

        $chunk = self::eatFirstKey( $input );
        if ( $chunk[ 2 ] ) {
            // this is actually a class name.
            $record[ 'oClass' ] = $chunk[ 0 ];
            $input              = $chunk[ 1 ];
            $chunk              = self::eatKey( $input );
            $key                = $chunk[ 0 ];
            $input              = $chunk[ 1 ];
        } else {
            $key   = $chunk[ 0 ];
            $input = $chunk[ 1 ];
        }

        if ( empty( $key ) && empty( $input ) ) return $record;

        $chunk = self::eatValue( $input );
        $value = $chunk[ 0 ];
        $input = $chunk[ 1 ];

        $record[ $key ] = $value;

        while ( strlen( $input ) > 0 ) {
            if ( $input[ 0 ] === ',' ) {
                $input = substr( $input, 1 );
            } else {
                break;
            }

            $chunk = self::eatKey( $input );
            $key   = $chunk[ 0 ];
            $input = $chunk[ 1 ];
            if ( strlen( $input ) > 0 ) {
                $chunk          = self::eatValue( $input );
                $value          = $chunk[ 0 ];
                $input          = $chunk[ 1 ];
                $record[ $key ] = $value;
            } else {
                $record[ $key ] = null;
            }
        }

        return $record;
    }

    /**
     * Consume the first field key, which could be a class name.
     *
     * @param string $input The input to consume
     *
     * @return array The collected string and any remaining content, followed by a boolean indicating whether
     *                this is a class name.
     */
    protected static function eatFirstKey( $input ) {
        $length      = strlen( $input );
        $collected   = '';
        $isClassName = false;
        if ( $input[ 0 ] === '"' ) {
            $result = self::eatString( substr( $input, 1 ) );

            return [ $result[ 0 ], substr( $result[ 1 ], 1 ) ];
        }

        for ( $i = 0; $i < $length; $i++ ) {
            $c = $input[ $i ];
            if ( $c === '@' ) {
                $isClassName = true;
                break;
            } elseif ( $c === ':' ) {
                break;
            } else {
                $collected .= $c;
            }
        }

        return [ $collected, substr( $input, $i + 1 ), $isClassName ];
    }

    /**
     * Consume a field key, which may or may not be quoted.
     *
     * @param string $input The input to consume
     *
     * @return array The collected string and any remaining content.
     */
    protected static function eatKey( $input ) {
        $length    = strlen( $input );
        $collected = '';
        if ( isset( $input[ 0 ]  ) && $input[ 0 ] === '"' ) {
            $result = self::eatString( substr( $input, 1 ) );

            return [ $result[ 0 ], substr( $result[ 1 ], 1 ) ];
        }

        for ( $i = 0; $i < $length; $i++ ) {
            $c = $input[ $i ];
            if ( $c === ':' ) {
                break;
            } else {
                $collected .= $c;
            }

        }

        return [ $collected, substr( $input, $i + 1 ) ];
    }


    /**
     * Consume a field value.
     *
     * @param string $input The input to consume
     *
     * @return array The collected value and any remaining content.
     */
    protected static function eatValue( $input ) {
        $input = ltrim( $input, ' ' );
        $c     = @$input[ 0 ];  # avoid Notice: Uninitialized string offset: 0
        if ( !strlen( $input ) || $c === ',' ) {
            return [ null, $input ];
        } elseif ( $c === '"' ) {
            return self::eatString( substr( $input, 1 ) );
        } elseif ( $c === '#' ) {
            return self::eatRID( substr( $input, 1 ) );
        } elseif ( $c === '[' ) {
            return self::eatArray( substr( $input, 1 ) );
        } elseif ( $c === '<' ) {
            return self::eatSet( substr( $input, 1 ) );
        } elseif ( $c === '{' ) {
            return self::eatMap( substr( $input, 1 ) );
        } elseif ( $c === '(' ) {
            return self::eatRecord( substr( $input, 1 ) );
        } elseif ( $c === '%' ) {
            return self::eatBag( substr( $input, 1 ) );
        } elseif ( $c === '_' ) {
            return self::eatBinary( substr( $input, 1 ) );
        } elseif ( $c === '-' || is_numeric( $c ) ) {
            return self::eatNumber( $input );
        } elseif ( $c === 'n' && substr( $input, 0, 4 ) === 'null' ) {
            return [ null, substr( $input, 4 ) ];
        } elseif ( $c === 't' && substr( $input, 0, 4 ) === 'true' ) {
            return [ true, substr( $input, 4 ) ];
        } elseif ( $c === 'f' && substr( $input, 0, 5 ) === 'false' ) {
            return [ false, substr( $input, 5 ) ];
        } else {
            return [ null, $input ];
        }
    }

    /**
     * Consume a string.
     *
     * @param string $input The input to consume
     *
     * @return array The collected string and any remaining content.
     */
    protected static function eatString( $input ) {
        $length    = strlen( $input );
        $collected = '';
        for ( $i = 0; $i < $length; $i++ ) {
            $c = $input[ $i ];
            if ( $c === '\\' ) {
                // escape, skip to the next character
                $i++;
                $collected .= $input[ $i ];
                continue;
            } elseif ( $c === '"' ) {
                break;
            } else {
                $collected .= $c;
            }
        }

        return [ $collected, substr( $input, $i + 1 ) ];
    }

    /**
     * Consume a number.
     *
     * If the number has a suffix, consume it also and instantiate the right type, e.g. for dates
     *
     * @param string $input The input to consume
     *
     * @return array The collected number and any remaining content.
     */
    protected static function eatNumber( $input ) {
        $length    = strlen( $input );
        $collected = '';
        $isFloat   = false;
        for ( $i = 0; $i < $length; $i++ ) {
            $c = $input[ $i ];
            if ( $c === '-' || is_numeric( $c ) || $c == 'E' ) {
                $collected .= $c;
            } elseif ( $c === '.' ) {
                $isFloat = true;
                $collected .= $c;
            } else {
                break;
            }
        }

        $input = substr( $input, $i );

        if ( !isset( $input[ 0 ] ) ) {
            return [ $collected, $input ];
        }

        $c = $input[ 0 ];

        $useStrings = ( PHP_INT_SIZE == 4 );

        if ( $c === 'a' || $c === 't' ) {
            # date / 1000
            $dt = new \DateTimeZone( date_default_timezone_get() );
            $collected = \DateTime::createFromFormat( 'U', substr( $collected, 0, -3 ) );
            $collected->setTimeZone( $dt );
            $input     = substr( $input, 1 );
        } elseif ( $c === 'f' ) {
            // float
            if( !$useStrings ){
                $collected = (float)$collected;
            }
            $input     = substr( $input, 1 );
        } elseif ( $c === 'b' || $c === 's' || $c === 'l' ) {
            if( !$useStrings ){
                $collected = (int)$collected;
            }
            $input     = substr( $input, 1 );
        } elseif ( $c === 'c' || $c === 'd' ) {
            if( !$useStrings ){
                $collected = (double)$collected;
            }
            $input     = substr( $input, 1 );
        } elseif ( $isFloat ) {
            if( !$useStrings ){
                $collected = (float)$collected;
            }
        } else {
            if( !$useStrings ){
                $collected = (int)$collected;
            }
        }

        return [ $collected, $input ];
    }

    /**
     * Consume a Record ID.
     *
     * @param string $input The input to consume
     *
     * @return array The collected RID and any remaining content.
     */
    protected static function eatRID( $input ) {
        $length    = strlen( $input );
        $collected = '';
        $cluster   = null;
        for ( $i = 0; $i < $length; $i++ ) {
            $c = $input[ $i ];

            if ( $cluster === null && $c === ':' ) {
                $cluster   = (int)$collected;
                $collected = '';
            } elseif ( $c === '-' || is_numeric( $c ) ) {
                $collected .= $c;
            } else {
                break;
            }
        }

        return [ new ID( (string)$cluster, (string) $collected ), substr( $input, $i ) ];
    }

    /**
     * Consume an array of values.
     *
     * @param string $input The input to consume
     *
     * @return array The collected array and any remaining content.
     */
    protected static function eatArray( $input ) {
        $length  = strlen( $input );
        $array   = [ ];
        $cluster = null;
        while ( strlen( $input ) ) {
            $c = $input[ 0 ];
            if ( $c === ',' ) {
                $input = substr( $input, 1 );
            } elseif ( $c === ']' ) {
                $input = substr( $input, 1 );
                break;
            }
            $chunk    = self::eatValue( $input );
            $array[ ] = $chunk[ 0 ];
            $input    = $chunk[ 1 ];
        }

        return [ $array, $input ];
    }


    /**
     * Consume a set of values.
     *
     * @param string $input The input to consume
     *
     * @return array The collected set and any remaining content.
     */
    protected static function eatSet( $input ) {
        $set     = [ ];
        $cluster = null;
        while ( strlen( $input ) ) {
            $c = $input[ 0 ];
            if ( $c === ',' ) {
                $input = substr( $input, 1 );
            } elseif ( $c === '>' ) {
                $input = substr( $input, 1 );
                break;
            }
            $chunk  = self::eatValue( $input );
            $set[ ] = $chunk[ 0 ];
            $input  = $chunk[ 1 ];
        }

        return [ $set, $input ];
    }


    /**
     * Consume a map of keys to values.
     *
     * @param string $input The input to consume
     *
     * @return array The collected map and any remaining content.
     */
    protected static function eatMap( $input ) {
        $map     = [ ];
        $cluster = null;
        $input   = ltrim( $input, ' ' );
        while ( strlen( $input ) ) {
            $c = $input[ 0 ];
            if ( $c === ' ' ) {
                $input = ltrim( substr( $input, 1 ), ' ' );
                continue;
            } elseif ( $c === ',' ) {
                $input = ltrim( substr( $input, 1 ), ' ' );
            } elseif ( $c === '}' ) {
                $input = substr( $input, 1 );
                break;
            }
            $chunk = self::eatKey( $input );
            $key   = $chunk[ 0 ];
            $input = ltrim( $chunk[ 1 ], ' ' );
            if ( strlen( $input ) ) {
                $chunk       = self::eatValue( $input );
                $map[ $key ] = $chunk[ 0 ];
                $input       = ltrim( $chunk[ 1 ], ' ' );
            } else {
                $map[ $key ] = null;
                break;
            }
        }

        return [ $map, $input ];
    }

    /**
     * Consume an embedded record.
     *
     * @param string $input The input to unserialize.
     *
     * @return array The collected record and any remaining content.
     */
    protected static function eatRecord( $input ) {
        $record = [ ];

        $input = ltrim( $input, ' ' );
        if ( $input[ 0 ] === ')' ) {
            // this is an empty record.
            return [ $record, substr( $input, 1 ) ];
        }

        $chunk = self::eatFirstKey( $input );
        if ( $chunk[ 2 ] ) {
            // this is actually a class name.
            $record[ 'oClass' ] = $chunk[ 0 ];
            $input              = ltrim( $chunk[ 1 ], ' ' );
            if ( $input[ 0 ] === ')' ) {
                return [ $record, substr( $input, 1 ) ];
            }
            $chunk = self::eatKey( $input );
            $key   = $chunk[ 0 ];
            $input = $chunk[ 1 ];
        } else {
            $key   = $chunk[ 0 ];
            $input = $chunk[ 1 ];
        }

        $chunk = self::eatValue( $input );
        $value = $chunk[ 0 ];
        $input = ltrim( $chunk[ 1 ], ' ' );;

        $record[ $key ] = $value;

        while ( strlen( $input ) > 0 ) {
            if ( $input[ 0 ] === ',' ) {
                $input = ltrim( substr( $input, 1 ), ' ' );
            } elseif ( $input[ 0 ] === ')' ) {
                $input = ltrim( substr( $input, 1 ), ' ' );
                break;
            }

            $chunk = self::eatKey( $input );
            $key   = $chunk[ 0 ];
            $input = ltrim( $chunk[ 1 ], ' ' );
            if ( strlen( $input ) > 0 ) {
                $chunk          = self::eatValue( $input );
                $value          = $chunk[ 0 ];
                $input          = $chunk[ 1 ];
                $record[ $key ] = $value;
            } else {
                $record[ $key ] = null;
            }
        }

        $payload = [ ];
        if ( isset( $record[ 'oClass' ] ) ) {
            $payload[ 'oClass' ] = $record[ 'oClass' ];
            unset( $record[ 'oClass' ] );
        }

        if ( isset( $record[ '@type' ] ) ) {
            $payload[ 'type' ] = $record[ '@type' ];
            unset( $record[ '@type' ] );
        }

        $payload[ 'oData' ] = $record;
        $record             = \PhpOrient\Protocols\Binary\Data\Record::fromConfig( $payload );

        return [ $record, $input ];
    }


    /**
     * Consume a record id bag.
     *
     * @param string $input The input to consume
     *
     * @return array The collected record id bag and any remaining content.
     */
    protected static function eatBag( $input ) {
        $length    = strlen( $input );
        $collected = '';
        for ( $i = 0; $i < $length; $i++ ) {
            $c = $input[ $i ];
            if ( $c === ';' ) {
                break;
            } else {
                $collected .= $c;
            }
        }

        return [ new Bag( $collected ), substr( $input, $i + 1 ) ];
    }

    /**
     * Consume a binary field.
     *
     * @param string $input The input to consume
     *
     * @return array The collected binary and any remaining content.
     */
    protected static function eatBinary( $input ) {
        $length    = strlen( $input );
        $collected = '';
        for ( $i = 0; $i < $length; $i++ ) {
            $c = $input[ $i ];
            if ( $c === '_' || $c === ',' || $c === ')' || $c === '>' || $c === '}' || $c === ']' ) {
                break;
            } else {
                $collected .= $c;
            }
        }

        return [ $collected, substr( $input, $i + 1 ) ];
    }

    /**
     * Serialize a value.
     *
     * @param mixed $value    The value to serialize.
     *
     * @param bool  $embedded Whether this is a value embedded in another.
     *
     * @return string The serialized value.
     */
    public static function serialize( $value, $embedded = false ) {
        if ( $value === null ) {
            return '';
        }
        if ( is_string( $value ) ) {
            return '"' . str_replace( '"', '\\"', str_replace( '\\', '\\\\', $value ) ) . '"';
        } elseif ( is_float( $value ) ) {
            //this because float suffix "f"
            // cut the numbers to the second decimal number
            // if the field in OrientDB is set as double
            return $value . 'd';
        } elseif ( is_int( $value ) ) {
            return $value;
        } elseif ( is_bool( $value ) ) {
            return $value ? 'true' : 'false';
        } elseif ( is_array( $value ) ) {
            return self::serializeArray( $value );
        } elseif ( $value instanceof SerializableInterface ) {
            return self::serializeDocument( $value, $embedded );
        } elseif ( $value instanceof \DateTime ) {
            return $value->getTimestamp() . '000t';
        } elseif ( $value instanceof ID ) {
            return $value->__toString();
        } elseif ( $value instanceof Bag ){
            /*
             * This line works the same, but transforms the edges list to a linkSet
             * //    return self::serializeArray( $value->getRids() );
             *
             * From:
             *
             * ----+-----+------+------+--------+---------
             * #   |@RID |@CLASS|script|out_    |in_
             * ----+-----+------+------+--------+---------
             * 0   |#9:0 |V     |true  |[size=1]|[size=1]
             * ----+-----+------+------+--------+---------
             *
             * To:
             *
             * ----+-----+------+------+--------+---------
             * #   |@RID |@CLASS|script|out_    |in_
             * ----+-----+------+------+--------+---------
             * 0   |#9:0 |V     |true  |[1]     |[1]
             * ----+-----+------+------+--------+---------
             *
             */
            return $value->getRawBagContent();
        } else {
            return '';
        }
    }

    protected static function serializeDocument( SerializableInterface $document, $embedded = false ) {
        $array    = $document->recordSerialize();
        $segments = [ ];
        foreach ( $array['oData'] as $key => $value ) {
            $segments[ ] = $key . ':' . self::serialize( $value, true );
        }

        $assembled = implode( ',', $segments );
        if ( isset( $array[ 'oClass' ] ) ) {
            $assembled = $array[ 'oClass' ] . '@' . $assembled;
        }
        if ( $embedded ) {
            return '(' . $assembled . ')';
        } else {
            return $assembled;
        }
    }

    /**
     * Serialize an array of values.
     * If the array is associative a `map` will be returned, otherwise a plain array.
     *
     * @param array $array the array to serialize
     *
     * @return string the serialized array or map.
     */
    protected static function serializeArray( array $array ) {
        $isMap  = false;
        $keys   = [ ];
        $values = [ ];

        foreach ( $array as $key => $value ) {
            if ( !$isMap && is_string( $key ) && strlen( $key ) ) {
                $isMap = true;
            }
            if ( $isMap ) {
                $keys[ ] = '"' . str_replace( '"', '\\"', str_replace( '\\', '\\\\', $key ) ) . '"';
            } else {
                $keys[ ] = '"' . $key . '"';
            }
            $values[ ] = self::serialize( $value, true );
        }
        if ( $isMap ) {
            $parts = [ ];
            foreach ( $keys as $i => $key ) {
                $parts[ ] = $key . ':' . $values[ $i ];
            }

            return '{' . implode( ',', $parts ) . '}';
        } else {
            return '[' . implode( ',', $values ) . ']';
        }
    }

}
