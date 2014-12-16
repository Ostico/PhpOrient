<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 15/12/14
 * Time: 23.11
 *
 */

namespace PhpOrient;

use PhpOrient\Abstracts\TestCase;
use PhpOrient\Protocols\Common\ClusterList;
use PhpOrient\Protocols\Common\Constants;

class DatabaseListTest extends TestCase {

    protected $db_name = 'db_list_test';

    public function testMakeDBStruct() {

        $class_name = 'my_class';

        $clusters = new ClusterList();
        $clusters->configure( $this->cluster_struct );

        $result = $this->client->execute( 'command', [
            'command' => Constants::QUERY_CMD,
            'query'   => "create class $class_name extends V"
        ] );
        $this->assertNotEmpty( $result );

        $clusters[ $class_name ] = $result;
        $this->assertTrue( $clusters->offsetExists( $class_name ) );
        $this->assertTrue( isset( $clusters[ $class_name ] ) );
        $this->assertArrayHasKey( $class_name, $clusters );

        $this->assertEquals( $result, $clusters->getClusterID( $class_name ) );
        $this->assertEquals( $result, $clusters[ $class_name ] );

        $drop_table = $this->client->execute( 'command', [
            'command' => Constants::QUERY_CMD,
            'query'   => "drop class $class_name"
        ] );

        unset( $clusters[ $class_name ] );

        $this->assertEquals( 11, count( $clusters ) );
        $this->assertEquals( 11, count( $this->client->getTransport()->getClusterList() ) );
        $this->assertEquals( $clusters, $this->client->getTransport()->getClusterList() );

//        $this->client->execute('db')
//        $list = $this->client->execute( 'dbList' );

    }

}
