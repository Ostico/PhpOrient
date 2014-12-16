<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 15/12/14
 * Time: 22.30
 * 
 */

namespace PhpOrient\Protocols\Common;

/**
 * Class ClusterList
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
class ClusterList implements ConfigurableInterface, \ArrayAccess, \Countable {
    use ConfigurableTrait {
        configure as config;
        fromConfig as fromConf;
    }

    /**
     * @var int
     */
    protected $sessionId;

    /**
     * @var array
     */
    protected $dataClusters;

    /**
     * @var array
     */
    protected $reverseMap;

    /**
     * @var int
     */
    protected $servers;

    /**
     * @var string
     */
    protected $release;

    /**
     * Expected ClusterList
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
        if ( !empty( $this->dataClusters ) ) {
            foreach ( $this->dataClusters as $pos => $value ) {
                $this->reverseMap[ $value[ 'name' ] ] = [ $pos, $value[ 'id' ] ];
            }
        }

        return $this;

    }

    /**
     * Alias for @see ClusterList::offsetGet
     * @param $offset
     *
     * @return int|null
     */
    public function getClusterID( $offset ){
        return $this->offsetGet( $offset );
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
        return array_key_exists( $offset, $this->reverseMap );
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
     * @return int|null Can return all value types.
     */
    public function offsetGet( $offset ) {
        if( array_key_exists( $offset, $this->reverseMap ) ){
            return $this->reverseMap[ $offset ][ 1 ];
        }
        return null;
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
        $maxID = max( array_keys( $this->dataClusters ) );
        $this->reverseMap[ $offset ] = [ $maxID + 1, $value ];
        $this->dataClusters[ $maxID + 1 ] = [ 'name' => $offset, 'id' => $value ];
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
        if( !array_key_exists( $offset, $this->reverseMap ) ){
            trigger_error( 'Offset ' . $offset . ' does not exists in oData structure. Added as a new key.', E_USER_NOTICE );
        } else {
            $position = $this->reverseMap[ $offset ][0];
            unset( $this->reverseMap[ $offset ] );
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

}