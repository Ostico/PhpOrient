<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 15/12/14
 * Time: 22.30
 * 
 */

namespace PhpOrient\Protocols\Common;

/**
 * Class ClustersMap
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
class OrientVersion {
    use ConfigurableTrait {
        configure as config;
        fromConfig as fromConf;
    }

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
     * @var string
     */
    protected $subversion;

    protected function _parseRelease(){
        @list(
            $this->majorVersion,
            $this->minorVersion,
            $this->buildNumber
        ) = @explode( ".", $this->release );

        if ( stripos( $this->minorVersion, "-" ) !== false ){
            @list( $this->minorVersion, $this->buildNumber ) = explode( "-", $this->minorVersion );
        }

        if ( stripos( $this->buildNumber, "-" ) !== false ){
            @list( $this->buildNumber, $this->subversion ) = explode( "-", $this->buildNumber );
            @list( $this->subversion, ) = explode( " ", $this->subversion );
        } else {
            @list( $this->buildNumber, ) = explode( " ", $this->buildNumber );
        }

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
     * @return string
     */
    public function getSubversion() {
        return $this->subversion;
    }

    /**
     * Expected Version string
     * @param array $options
     *
     * @return $this|void
     */
    public function configure( Array $options = array() ) {

        $this->config( $options );
        $this->_parseRelease();
        return $this;

    }

}