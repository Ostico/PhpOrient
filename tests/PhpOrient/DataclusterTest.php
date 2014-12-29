<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 07/12/14
 * Time: 15.02
 *
 */

namespace PhpOrient;
use PhpOrient\Abstracts\TestCase;
class DataClusterTest extends TestCase {

    protected $db_name = 'test_cluster';

    public function testClusterCount() {

        $ids = [ ];
        foreach ( $this->cluster_struct as $cluster ) {
            $ids[ ] = $cluster[ 'id' ];
        }

        $this->assertNotEmpty( $ids );
        $res = $this->client->execute( 'dataClusterCount', [ 'ids' => $ids ] );
        $this->assertNotEmpty( $res );

    }

    public function testClusterAddReload() {

        $cluster_name = 'cluster_123';

        $this->assertEmpty( $this->cluster_struct[ $cluster_name ] );

        $id = $this->client->execute( 'dataClusterAdd', [ 'cluster_name' => $cluster_name ] );

        $reloaded_list = $this->client->execute( 'dbReload' );

        $this->assertEquals( $this->cluster_struct, $reloaded_list );

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
        $this->assertEquals( $reloaded_list, $dropped_list );

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
        foreach ( $_cluster as $cluster ) {
            if ( 'followed_by' == $cluster[ 'name' ] ) {
                $data = $this->client->execute( 'dataClusterDataRange', [ 'id' => $cluster[ 'id' ] ] );
            }
        }

        $this->assertNotEmpty( $data );
        $this->assertNotEmpty( $data[ 1 ] );
        $this->assertEquals( 7046, $data[ 1 ] );

    }

    public function testClusterCountByClusterMap(){
        $this->client->dbOpen( 'GratefulDeadConcerts', 'admin', 'admin' );
        $total = $this->client->dataClusterCount( $this->client->getTransport()->getClusterMap()->getIdList() );

        $this->assertNotEmpty( $total );

    }

} 