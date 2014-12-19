<?php

namespace PhpOrient\Abstracts;
use PhpOrient\Client;
use PhpOrient\Protocols\Common\ClusterMap;
use PhpOrient\Protocols\Common\Constants;

abstract class TestCase extends \PHPUnit_Framework_TestCase {
    use ClientTrait;

    protected $thisTest;

    /**
     * @var Client
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

        $this->client = $this->createClient();

        $this->client->execute( 'connect' );

        try {
            $this->client->execute( 'dbDrop', [
                'database'     => $this->db_name,
                'storage_type' => Constants::STORAGE_TYPE_MEMORY
            ] );
        } catch ( \Exception $e ) {
            $this->client->getTransport()->debug( $e->getMessage() );
        }

        $this->client->execute( 'dbCreate', [
            'database'      => $this->db_name,
            'database_type' => Constants::DATABASE_TYPE_GRAPH,
            'storage_type'  => Constants::STORAGE_TYPE_MEMORY,
        ] );

        $this->cluster_struct = $this->client->execute( 'dbOpen', [
            'database' => $this->db_name
        ] );

        $this->thisTest = microtime(true);
    }

    public function tearDown() {
        $resultTime = microtime(true) - $this->thisTest;
        echo " " . str_pad( $this->getName(false) , 41, " ", STR_PAD_RIGHT ). " - Did in " . $resultTime . " seconds.\n";
    }

}
