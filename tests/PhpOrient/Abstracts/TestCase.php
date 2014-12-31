<?php

namespace PhpOrient\Abstracts;
use PhpOrient\PhpOrient;
use PhpOrient\Protocols\Common\ClusterMap;
use PhpOrient\Protocols\Common\Constants;

abstract class TestCase extends \PHPUnit_Framework_TestCase {
    use ClientTrait;

    protected $thisTest;

    /**
     * @var PhpOrient
     */
    protected $client;
    protected $db_name;

    /**
     * @var ClusterMap
     */
    protected $cluster_struct;

    protected $reflectedClass;
    protected $reflectedMethod;

    public function setUp() {

        $this->client = $this->createClient('connect');

        $this->client->connect();

        try {
            $this->client->dbDrop( $this->db_name, Constants::STORAGE_TYPE_MEMORY );
        } catch ( \Exception $e ) {
//            echo $e->getMessage();
            $this->client->getTransport()->debug( $e->getMessage() );
        }

        $this->client->dbCreate( $this->db_name,
            Constants::STORAGE_TYPE_MEMORY,
            Constants::DATABASE_TYPE_GRAPH
        );

        $this->cluster_struct = $this->client->dbOpen( $this->db_name, 'admin', 'admin' );

        $this->thisTest = microtime(true);
    }

    public function tearDown(){
        $resultTime = microtime(true) - $this->thisTest;
        echo " " . str_pad(
                substr(
                    get_class($this),
                    strrpos( get_class($this), "\\" ) + 1
                ) . "::" . $this->getName(false), 61, " ", STR_PAD_RIGHT
            ) . " - Did in " . $resultTime . " seconds.\n";
    }

}
