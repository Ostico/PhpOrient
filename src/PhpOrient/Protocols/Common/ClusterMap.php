<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 15/12/14
 * Time: 22.30
 * 
 */

namespace PhpOrient\Protocols\Common;

/**
 * Class ClusterMap
 * @package PhpOrient\Protocols\Common
 *
 * When you create a new record specifying its Class,
 * OrientDB automatically selects the Class where to store the physical record,
 * by using configurable strategies.
 *
 * The available strategies are:
 *
 * <ul>
 *  <li>default, uses always the Class's defaultClusterId property. This was the default before 1.7</li>
 *  <li>round-robin, put the Class's configured clusters in a ring and returns a different cluster every time restarting from the first when the ring is completed</li>
 *  <li>balanced, checks the records in all the clusters and returns the smaller cluster. This allows the cluster to have all the underlying clusters balanced on size. On adding a new cluster to an existent class, the new empty cluster will be filled before the others because more empty then the others. Calculation of cluster size is made every 5 or more seconds to avoid to slow down insertion</li>
 *  <li>local. This is injected when OrientDB is running in distributed mode. With this strategy the cluster that is the master on current node is always preferred. This avoids conflicts and reduces network latency with remote calls between nodes.</li>
 * </ul>
 *
 * //TODO only default strategy is supported
 *
 */
class ClusterMap implements ConfigurableInterface, \ArrayAccess, \Countable, \Iterator {
    use ConfigurableTrait {
        configure as config;
        fromConfig as fromConf;
    }

    /**
     * @var array
     */
    protected $dataClusters;

    /**
     * @var array
     */
    protected $reverseMap;

    /**
     * @var array
     */
    protected $reverseIDMap;

    /**
     * @var int
     */
    protected $servers;

    /**
     * @var string
     */
    protected $release;

    /**
     * @var int
     */
    protected $majorVersion;

    /**
     * @var int
     */
    protected $minorVersion;

    /**
     * @var string
     */
    protected $buildNumber;

    /**
     * @return int
     */
    public function getServers() {
        return $this->servers;
    }

    protected function _parseRelease(){
        @list(
            $this->majorVersion,
            $this->minorVersion,
            $this->buildNumber
        ) = @explode( ".", $this->release );

        if ( stripos( $this->minorVersion, "-" ) !== false ){
            @list( $this->minorVersion, $this->buildNumber ) = explode( "-", $this->minorVersion );
        }
        @list( $this->buildNumber, ) = explode( " ", $this->buildNumber );
    }

    /**
     * @return string
     */
    public function getRelease() {
        return $this->release;
    }

    /**
     * @return int
     */
    public function getMajorVersion() {
        return (int)$this->majorVersion;
    }

    /**
     * @return int
     */
    public function getMinorVersion() {
        return (int)$this->minorVersion;
    }

    /**
     * @return string
     */
    public function getBuildNumber() {
        return $this->buildNumber;
    }

    /**
     * Expected ClusterMap
     * <pre>
     * array (
     *      0 =>
     *        array (
     *          'name' => 'orids',
     *          'id' => 8,
     *        ),
     *      1 =>
     *        array (
     *          'name' => 'oschedule',
     *          'id' => 7,
     *        ),
     *   .....
     *   )
     * </pre>
     * @param array $options
     *
     * @return $this|void
     */
    public function configure( Array $options = array() ) {

        $this->config( $options );
        $this->_parseRelease();
        if ( !empty( $this->dataClusters ) ) {
            $this->reverseMap = array();
            $this->reverseIDMap = array();
            foreach ( $this->dataClusters as $pos => $value ) {
                $this->reverseMap[ $value[ 'name' ] ] = [ $pos, $value[ 'id' ] ];
                $this->reverseIDMap[ $value[ 'id' ] ] = [ $pos, $value[ 'name' ] ];
            }
        }

        return $this;

    }

    /**
     * Return the list of cluster IDs
     *
     * @return int[]|string[] numeric
     */
    public function getIdList(){
        return array_keys( $this->reverseIDMap );
    }

    /**
     * Alias for @see ClusterList::offsetGet
     * @param $name
     *
     * @return int|null
     */
    public function getClusterID( $name ){
        return $this->offsetGet( $name );
    }

    /**
     * Remove a cluster by ID
     *
     * @param $ID
     */
    public function dropClusterID( $ID ){
        list( $pos, $name ) = $this->reverseIDMap[ $ID ];
        unset( $this->reverseIDMap[ $ID ] );
        unset( $this->reverseMap[ $name ] );
        unset( $this->dataClusters[ $pos ] );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $name <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists( $name ) {
        return array_key_exists( $name, $this->reverseMap );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $name <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return int|null Can return all value types.
     */
    public function offsetGet( $name ) {
        if( array_key_exists( $name, $this->reverseMap ) ){
            return $this->reverseMap[ $name ][ 1 ];
        }
        return null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $name <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     */
    public function offsetSet( $name, $value ) {
        $maxID = max( array_keys( $this->dataClusters ) );
        $this->reverseMap[ $name ] = [ $maxID + 1, $value ];
        $this->reverseIDMap[ $value ] = [ $maxID + 1, $name ];
        $this->dataClusters[ $maxID + 1 ] = [ 'name' => $name, 'id' => $value ];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $name <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     */
    public function offsetUnset( $name ) {
        if( !array_key_exists( $name, $this->reverseMap ) ){
            trigger_error( 'Offset ' . $name . ' does not exists in oData structure. Added as a new key.', E_USER_NOTICE );
        } else {
            $position = $this->reverseMap[ $name ][0];
            $value    = $this->reverseMap[ $name ][1];
            unset( $this->reverseMap[ $name ] );
            unset( $this->reverseIDMap[ $value ] );
            unset( $this->dataClusters[ $position ] );
        }
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
        return count( $this->dataClusters );
    }

    protected $internal_position;

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next() {
        next( $this->dataClusters );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key() {
        return key( $this->dataClusters );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid() {
        $key = key( $this->dataClusters );
        return !is_null( $key );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind() {
        reset( $this->dataClusters );
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current() {
        return current( $this->dataClusters );
    }


}