<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 13/12/14
 * Time: 14.30
 *
 */

namespace PhpOrient\Protocols\Binary\Data;


use PhpOrient\Protocols\Binary\Abstracts\SerializableInterface;
use PhpOrient\Protocols\Common\ConfigurableTrait;

class Record implements \ArrayAccess, \JsonSerializable, SerializableInterface {
    use ConfigurableTrait;

    /**
     * @var ID The record id.
     */
    protected $rid;

    /**
     * @var string The class this record belongs to.
     */
    protected $oClass;

    /**
     * @var int The record version.
     */
    protected $version = 0;

    /**
     * @var array The raw bytes that make up the record.
     */
    protected $oData = [];


    /**
     * Gets the Record ID
     * @return ID
     */
    public function getRid() {
        return $this->rid;
    }

    /**
     * Sets the Record Id
     *
     * @param ID $rid
     *
     * @return $this the current object
     */
    public function setRid( ID $rid ) {
        $this->rid = $rid;

        return $this;
    }


    /**
     * Sets the Orient Class
     *
     * @param string $oClass
     *
     * @return $this the current object
     */
    public function setOClass( $oClass ) {
        $this->oClass = $oClass;

        return $this;
    }

    /**
     * Gets the Orient Class
     * @return string|null
     */
    public function getOClass() {
        return $this->oClass;
    }

    /**
     * Sets the Version
     *
     * @param int $version
     *
     * @return $this the current object
     */
    public function setVersion( $version ) {
        $this->version = $version;

        return $this;
    }

    /**
     * Gets the Version
     * @return int
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Sets the Orient Record Content
     *
     * @param array|null $oData
     *
     * @return $this the current object
     */
    public function setOData( Array $oData = null ) {
        if( $oData === null ) $oData = [];
        $this->oData = $oData;
        return $this;
    }

    /**
     * Gets the Orient Record Content
     * @return string
     */
    public function getOData() {
        return $this->oData;
    }

    /**
     * Return a representation of the class that can be serialized as an
     * PhpOrient record.
     *
     * @return mixed
     */
    public function recordSerialize() {
        return $this->jsonSerialize();
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize() {
        $meta = [
            'rid'     => $this->getRid(),
            'version' => $this->getVersion(),
            'oClass'  => $this->getOClass(),
            'oData'   => $this->getOData()
        ];

        return $meta;
    }

    /**
     * To String ( as alias of json_encode )
     *
     * @return string
     */
    public function __toString(){
        return json_encode( $this );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists( $offset ) {
        return array_key_exists( $offset, $this->oData );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet( $offset ) {
        if( @array_key_exists( $offset, $this->oData ) ){
            return $this->oData[ $offset ];
        } else {
            throw new \OutOfBoundsException( 'The searched key ' . $offset . ' does not exists in this record: ' . var_export( $this, true ) );
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     */
    public function offsetSet( $offset, $value ) {
        if( !array_key_exists( $offset, $this->oData ) ){
            trigger_error( 'Offset ' . $offset . ' does not exists in oData structure. Added as a new key.', E_USER_NOTICE );
        }
        $this->oData[ $offset ] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     */
    public function offsetUnset( $offset ) {
        if( !array_key_exists( $offset, $this->oData ) ){
            trigger_error( 'Offset ' . $offset . ' does not exists in oData structure.', E_USER_NOTICE );
            return;
        }
        unset( $this->oData[ $offset ] );
    }

    /**
     * Magic Method, access directly to the Orient Record
     * content as property
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get( $name ) {
        return $this->offsetGet( $name );
    }

    /**
     * Magic Method, set directly to the Orient Record
     * content as property
     *
     * @param $name
     * @param $value
     */
    public function __set( $name, $value ) {
        $this->offsetSet( $name, $value );
    }

}