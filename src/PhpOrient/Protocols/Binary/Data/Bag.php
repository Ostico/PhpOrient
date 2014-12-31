<?php

namespace PhpOrient\Protocols\Binary\Data;

use PhpOrient\Protocols\Binary\Stream\Reader;

/**
 *
 * @property int $type
 * @property int $size
 *
 * @package OrientDB\Records
 */
class Bag implements \Countable, \ArrayAccess, \Iterator {

    const EMBEDDED = 0;
    const TREE = 1;

    /**
     * @var string The base64 encoded representation of the bag.
     */
    protected $serialized;

    /**
     * @var string The base64 decoded stream of bytes.
     */
    protected $deserialized;

    /**
     * @var int The bag type, either embedded or tree.
     */
    protected $type;

    /**
     * @var int The number of record ids in the bag.
     */
    protected $size;

    /**
     * @var string The UUID for the bag.
     */
    protected $uuid;

    /**
     * @var int The file id for tree based RidBags.
     */
    protected $fileId;

    /**
     * @var int The page index for tree based RidBags.
     */
    protected $pageIndex;

    /**
     * @var int The page offset for tree based RidBags.
     */
    protected $pageOffset;

    /**
     * @var int The size of the changes for tree based RidBags.
     */
    protected $changeSize;

    /**
     * @var int The current offset.
     */
    protected $ReaderOffset = 0;

    /**
     * @var int The base offset to begin counting at for embedded bags.
     */
    protected $baseOffset = 0;

    /**
     * @var int The iterator offset.
     */
    protected $offset = 0;

    /**
     * @var array The items in the bag.
     */
    protected $items = [ ];

    /**
     * # RIDBag Constructor
     *
     * @param string $serialized the base64 encoded bag
     */
    public function __construct( $serialized ) {
        $this->serialized = $serialized;
    }

    /**
     * Gets the Type
     * @return int
     */
    public function getType() {
        if ( $this->type === null ) {
            $this->parse();
        }

        return $this->type;
    }

    /**
     * Gets the Size
     * @return int
     */
    public function getSize() {
        if ( $this->size === null ) {
            $this->parse();
        }

        return $this->size;
    }

    /**
     * Parse the bag header.
     */
    protected function parse() {
        $this->deserialized = base64_decode( $this->serialized );
        $mode               = ord( $this->deserialized[ 0 ] );

        if ( ( $mode & 1 ) === 1 ) {
            $this->type = self::EMBEDDED;
        } else {
            $this->type = self::TREE;
        }

        if ( ( $mode & 2 ) === 2 ) {
            $this->uuid         = substr( $this->deserialized, 1, 16 );
            $this->ReaderOffset = 17;
        } else {
            $this->ReaderOffset = 1;
        }

        if ( $this->type === self::EMBEDDED ) {
            $this->parseEmbedded();
        } else {
            $this->parseTree();
        }
    }

    /**
     * Parse the header for an embedded bag.
     */
    protected function parseEmbedded() {
        $this->size = Reader::unpackInt( substr( $this->deserialized, $this->ReaderOffset, 4 ) );
        $this->ReaderOffset += 4;
        $this->baseOffset = $this->ReaderOffset;
    }

    /**
     * Parse the header for a tree bag.
     */
    protected function parseTree() {
        $this->fileId = Reader::unpackLong( substr( $this->deserialized, $this->ReaderOffset, 8 ) );
        $this->ReaderOffset += 8;

        $this->pageIndex = Reader::unpackLong( substr( $this->deserialized, $this->ReaderOffset, 8 ) );
        $this->ReaderOffset += 8;

        $this->pageOffset = Reader::unpackInt( substr( $this->deserialized, $this->ReaderOffset, 4 ) );
        $this->ReaderOffset += 4;

        $this->size = Reader::unpackInt( substr( $this->deserialized, $this->ReaderOffset, 4 ) );
        $this->ReaderOffset += 4;

        $this->changeSize = Reader::unpackInt( substr( $this->deserialized, $this->ReaderOffset, 4 ) );
        $this->ReaderOffset += 4;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current() {
        return $this->offsetGet( $this->offset );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next() {
        $this->offset++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key() {
        return $this->offset;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid() {
        return $this->offset > -1 && $this->offset < $this->getSize();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind() {
        $this->offset = 0;
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
        return is_numeric( $offset ) && $offset > -1 && $offset < $this->getSize();
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
     * @todo add support for Tree Based RidBags
     * @return ID The RecordID instance at the given offset.
     */
    public function offsetGet( $offset ) {
        if ( isset( $this->items[ $offset ] ) ) {
            return $this->items[ $offset ];
        }
        if ( $this->type === self::EMBEDDED ) {
            $start = $this->baseOffset + ( $offset * 10 );

            $chunk = substr( $this->deserialized, $start, 2 );
            if( $chunk === false ){
                $this->items[ $offset ] = false;
                return $this->items[ $offset ];
            }

            $cluster                = Reader::unpackShort( substr( $this->deserialized, $start, 2 ) );
            $position               = Reader::unpackLong( substr( $this->deserialized, $start + 2, 8 ) );
            $this->items[ $offset ] = new ID( $cluster, $position );
        } else {
            $this->items[ $offset ] = false;
        }

        return $this->items[ $offset ];
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
        $this->items[ $offset ] = $value;
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
        unset( $this->items[ $offset ] );
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count() {
        return $this->getSize();
    }

}
