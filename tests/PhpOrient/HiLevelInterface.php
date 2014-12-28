<?php
/**
 * User: gremorian
 * Date: 22/12/14
 * Time: 23.05
 *
 */

namespace PhpOrient;


use PhpOrient\Abstracts\TestCase;
use PhpOrient\Protocols\Common\Constants;

class HiLevelInterface extends TestCase {

    protected $db_name = 'animals';

    protected $open;

    public function testRecordList(){
        $this->open   = $this->client->dbOpen( "GratefulDeadConcerts" );
        $result = $this->client->query( 'select from followed_by limit 10' );
        $this->assertContainsOnly( '\PhpOrient\Protocols\Binary\Data\Record', $result );
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\ClusterMap', $this->open );
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\ClusterMap', $this->cluster_struct );
        $this->assertNotEquals( $this->cluster_struct, $this->open );
    }

    public function testSql() {

        $this->client->command( 'create class Animal extends V' );
        $this->client->command( "insert into Animal set name = 'rat', specie = 'rodent'" );
        $animal = $this->client->query( "select * from Animal" );


        $this->client->command( 'create class Food extends V' );
        $this->client->command( "insert into Food set name = 'pea', color = 'green'" );

        $this->client->command( 'create class Eat extends E' );

        $this->client->command( "create edge Eat from ( select from Animal where name = 'rat' ) to ( select from Food where name = 'pea' )" );

        $pea_eaters = $this->client->query( "select expand( in( Eat )) from Food where name = 'pea'" );

        $animal_foods = $this->client->query( "select expand( out( Eat )) from Animal" );

        foreach ( $animal_foods as $food ) {
            $animal = $this->client->query(
                "select name from ( select expand( in('Eat') ) from Food where name = 'pea' )"
            );
            $this->assertEquals( 'pea', $food[ 'name' ] );
            $this->assertEquals( 'green', $food[ 'color' ] );
            $this->assertEquals( 'rat', $animal[ 0 ][ 'name' ] );
        }

    }

}