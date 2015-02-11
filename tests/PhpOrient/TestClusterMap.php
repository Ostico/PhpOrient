<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 15/12/14
 * Time: 23.11
 *
 */

namespace PhpOrient;

use PhpOrient\Abstracts\TestCase;
use PhpOrient\Protocols\Common\ClusterMap;
use PhpOrient\Protocols\Common\Constants;

class ClusterMapTest extends TestCase {

    protected $db_name = 'db_list_test';

    public function testMakeDBStruct() {

        $class_name = 'my_class';

        $result = $this->client->execute( 'command', [
            'command' => Constants::QUERY_CMD,
            'query'   => "create class $class_name extends V"
        ] );
        $this->assertNotEmpty( $result );

        $this->cluster_struct[ $class_name ] = $result;
        $this->assertTrue( $this->cluster_struct->offsetExists( $class_name ) );
        $this->assertTrue( isset( $this->cluster_struct[ $class_name ] ) );
        $this->assertArrayHasKey( $class_name, $this->cluster_struct );

        $this->assertEquals( $result, $this->cluster_struct->getClusterID( $class_name ) );
        $this->assertEquals( $result, $this->cluster_struct[ $class_name ] );

        $drop_table = $this->client->execute( 'command', [
            'command' => Constants::QUERY_CMD,
            'query'   => "drop class $class_name"
        ] );

        unset( $this->cluster_struct[ $class_name ] );

        $this->assertEquals( 11, count( $this->cluster_struct ) );
        $this->assertEquals( 11, count( $this->client->getTransport()->getClusterMap() ) );
        $this->assertEquals( $this->cluster_struct, $this->client->getTransport()->getClusterMap() );

        $this->client->execute( 'connect', self::getConfig('connect') );
        $list = $this->client->execute( 'dbList' );
        $this->assertTrue( count( $list['databases'] ) > 1 );

    }

    public function testClusterMap(){

        $id = $this->client->execute( 'dataClusterAdd', [ 'cluster_name' => 'test_cluster' ] );
        $this->assertEquals( 12, count( $this->client->getTransport()->getClusterMap() ) );
        $this->assertEquals( 11, $this->client->getTransport()->getClusterMap()[ 'test_cluster' ] );

        $reloaded_list = $this->client->execute( 'dbReload' );
        $this->assertEquals( $reloaded_list, $this->client->getTransport()->getClusterMap() );
        $this->assertEquals( $this->cluster_struct, $this->client->getTransport()->getClusterMap() );

    }

}
