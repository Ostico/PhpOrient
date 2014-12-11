<?php

namespace PhpOrient\Protocols\Binary\Serialization;

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

        $record = [ ];

        $chunk = self::eatFirstKey( $input );
        if ( $chunk[ 2 ] ) {
            // this is actually a class name.
            $record[ '@class' ] = $chunk[ 0 ];
            $input              = $chunk[ 1 ];
            $chunk              = self::eatKey( $input );
            $key                = $chunk[ 0 ];
            $input              = $chunk[ 1 ];
        } else {
            $key   = $chunk[ 0 ];
            $input = $chunk[ 1 ];
        }

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
//            } elseif ( $c == '{' || $c == '"' ) {
//                continue;
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
        if ( $input[ 0 ] === '"' ) {
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
        $c     = $input[ 0 ];
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
            if ( $c === '-' || is_numeric( $c ) ) {
                $collected .= $c;
            } elseif ( $c === '.' ) {
                $isFloat = true;
                $collected .= $c;
            } else {
                break;
            }
        }

        $input = substr( $input, $i );

        $c = $input[ 0 ];

        if ( $c === 'a' || $c === 't' ) {
            // date
            $collected = \DateTime::createFromFormat( 'U', $collected );
            $input     = substr( $input, 1 );
        } elseif ( $c === 'f' ) {
            // float
            $collected = (float)$collected;
            $input     = substr( $input, 1 );
        } elseif ( $c === 'b' || $c === 's' || $c === 'l' ) {
            $collected = (int)$collected;
            $input     = substr( $input, 1 );
        } elseif ( $c === 'c' || $c === 'd' ) {
            $input = substr( $input, 1 );
        } elseif ( $isFloat ) {
            $collected = (float)$collected;
        } else {
            $collected = (int)$collected;
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
            } elseif ( is_numeric( $c ) ) {
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
            $record[ '@class' ] = $chunk[ 0 ];
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
//        return [ $collected, substr( $input, $i + 1 ) ];
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
}
