<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 08/12/14
 * Time: 18.00
 *
 */

namespace PhpOrient\Protocols\Binary\Data;


class ID implements \JsonSerializable{

    /**
     * The cluster the record belongs to.
     *
     * @var int
     */
    public $cluster;

    /**
     * The position of the record in the cluster.
     *
     * @var int
     */
    public $position;


    /**
     * # Record ID Constructor.
     *
     * @param int|string|array $cluster  The cluster id, string representation or configuration object
     *
     * @param int              $position The position in the cluster, if $cluster is an integer.
     */
    public function __construct( $cluster = -1, $position = -1 ) {
        if ( is_array( $cluster ) ) {
            if ( isset( $cluster[ 'cluster' ] ) ) {
                $this->cluster = (string)$cluster[ 'cluster' ];
            }
            if ( isset( $cluster[ 'position' ] ) ) {
                $this->position = (string)$cluster[ 'position' ];
            }
        } else {
            if ( is_string( $cluster ) && $cluster[ 0 ] === '#' ) {
                list( $this->cluster, $this->position ) = self::parseString( $cluster );
            } else {
                $this->cluster  = (string)$cluster;
                $this->position = (string)$position;
            }
        }
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize() {
        return $this->__toString();
    }


    /**
     * @return string A string representation of the record id, e.g. "#12:10".
     */
    public function __toString() {
        return '#' . $this->cluster . ':' . $this->position;
    }

    /**
     * Transform a rid string format ( '#1:2' ) to array [ cluster, position ]
     *
     * @param $input string
     *
     * @return array
     */
    public static function parseString( $input ) {
        return explode( ':', substr( $input, 1 ) );
    }

} 