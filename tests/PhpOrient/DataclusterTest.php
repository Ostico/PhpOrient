<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 07/12/14
 * Time: 15.02
 *
 */

namespace PhpOrient;

use PhpOrient\Protocols\Common\Constants;

class DataClusterTest extends TestCase {

    /**
     * @var Client
     */
    protected $client;

    protected $db_name = 'test_create';

    protected $cluster_struct;

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

    }

    public function tearDown() {

        try {
            $this->client->execute( 'dbDrop', [
                    'database'     => 'test_cluster',
                    'storage_type' => Constants::STORAGE_TYPE_MEMORY
            ] );
        } catch ( \Exception $e ) {
        }

    }

    public function testClusterCount() {

        $ids = [ ];
        foreach ( $this->cluster_struct[ 'dataClusters' ] as $cluster ) {
            $ids[ ] = $cluster[ 'id' ];
        }

        $this->assertNotEmpty( $ids );
        $res = $this->client->execute( 'dataClusterCount', [ 'ids' => $ids ] );
        $this->assertNotEmpty( $res );

    }

    public function testClusterAddReload() {

        $cluster_name = 'cluster_123';

        foreach ( $this->cluster_struct[ 'dataClusters' ] as $cluster ) {
            $this->assertNotEquals( $cluster_name, $cluster[ 'name' ] );
        }

        $id = $this->client->execute( 'dataClusterAdd', [ 'cluster_name' => $cluster_name ] );

        $reloaded_list = $this->client->execute( 'dbReload' );

        $this->assertNotEquals( $this->cluster_struct[ 'dataClusters' ], $reloaded_list );

        $found = false;
        foreach ( $reloaded_list as $cluster ) {
            if ( $cluster_name == $cluster[ 'name' ] ) {
                $found = true;
                $this->assertEquals( $id, $cluster[ 'id' ] );
            }
        }

        $this->assertTrue( $found );

    }

    public function testDataClusterAddDropReload() {
        $cluster_name = 'cluster_456';

        $id            = $this->client->execute( 'dataClusterAdd', [ 'cluster_name' => $cluster_name ] );
        $reloaded_list = $this->client->execute( 'dbReload' );
        $this->assertNotEquals( $this->cluster_struct[ 'dataClusters' ], $reloaded_list );

        $found = false;
        foreach ( $reloaded_list as $cluster ) {
            if ( $cluster_name == $cluster[ 'name' ] ) {
                $found = true;
                $this->assertEquals( $id, $cluster[ 'id' ] );
            }
        }
        $this->assertTrue( $found );

        $id           = $this->client->execute( 'dataClusterDrop', [ 'id' => $id ] );
        $dropped_list = $this->client->execute( 'dbReload' );
        $this->assertNotEquals( $reloaded_list, $dropped_list );

        foreach ( $dropped_list as $cluster ) {
            if ( $cluster_name == $cluster[ 'name' ] ) {
                $this->assertFalse( true );
            }
        }

    }

    public function testDataRange() {

        $_cluster = $this->client->execute( 'dbOpen', [
                'database' => 'GratefulDeadConcerts'
        ] );

        $data = [ ];
        foreach ( $_cluster[ 'dataClusters' ] as $cluster ) {
            if ( 'followed_by' == $cluster[ 'name' ] ) {
                $data = $this->client->execute( 'dataClusterDataRange', [ 'id' => $cluster[ 'id' ] ] );
            }
        }

        $this->assertNotEmpty( $data );
        $this->assertNotEmpty( $data[ 1 ] );
        $this->assertEquals( 7046, $data[ 1 ] );

    }

} 