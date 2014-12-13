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

class Record implements \JsonSerializable, SerializableInterface {
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
     * @var string The raw bytes that make up the record.
     */
    protected $oData;


    /**
     * Gets the Record ID
     * @return ID
     */
    public function getRid() {
        return $this->rid;
    }

    /**
     * Sets the Id
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
     * Sets the Class
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
     * Gets the Class
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
     * Sets the Bytes
     *
     * @param string $oData
     *
     * @return $this the current object
     */
    public function setOData( $oData ) {
        $this->oData = $oData;

        return $this;
    }

    /**
     * Gets the Bytes
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

}