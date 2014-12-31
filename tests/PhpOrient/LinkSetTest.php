<?php
/**
 * User: gremorian
 * Date: 31/12/14
 * Time: 15.00
 * 
 */

namespace PhpOrient;


use PhpOrient\Abstracts\TestCase;
use PhpOrient\Protocols\Binary\Data\Bag;
use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Data\Record;

class TestSetsAndRidBags extends TestCase {

    protected $db_name = 'test_link_set';

    public function setup(){

        parent::setUp();

        $this->client->command( "create class links extends V" );
        $this->client->command( "create class sites extends V" );
        $this->client->command("create vertex sites set name = 'linkedin', id = 1 ");
        $this->client->command("create vertex sites set name = 'google', id = 2 ");
        $this->client->command("create vertex sites set name = 'github', id = 3 ");


        $this->client->command( "create vertex links set name = 'link1', " .
            "value = " .
            "'https://github.com/mogui/pyorient/issues', " .
            "id = 1, siteId = 3" );
        $this->client->command( "create vertex links set name = 'link2', " .
            "value = " .
            "'https://github.com/mogui/pyorient/pulls', " .
            "id = 2, siteId = 3" );
        $this->client->command( "create vertex links set name = 'link3', " .
            "value = " .
            "'https://github.com/mogui/pyorient/pulse', " .
            "id = 3, siteId = 3" );
        $this->client->command( "create vertex links set name = 'link4', " .
            "value = " .
            "'https://github.com/mogui/pyorient/graphs', " .
            "id = 4, siteId = 3" );

        $this->client->command( "CREATE LINK link TYPE LINKSET FROM links.siteId TO sites.id INVERSE");

    }

    public function testLinkSet() {
        $res = $this->client->query( "SELECT FROM sites where id = 3" );
        $this->assertEquals( 4, count( $res[ 0 ]->getOData()[ 'link' ] ) );
        $this->assertEquals( '#11:0', $res[ 0 ]->getOData()[ 'link' ][0]->__toString() );
    }

    public function testRidBags(){
        $this->client->dbOpen( 'GratefulDeadConcerts', 'admin', 'admin' );
        $myFunction = function( Record $rec ){
            $data = $rec->getOData();
            if( isset($data['in_followed_by']) ){
                $data = $data['in_followed_by'];

                if( $data instanceof ID ){
                    $this->assertNotEmpty( $data->__toString() );
                } elseif( $data instanceof Bag ){
                    foreach( $data as $k => $val ){
                        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\ID', $val );
                    }
                }

            } else {
                $this->assertTrue( is_array( $data ) );
            }
        };
        $result = $this->client->queryAsync( 'select from #11:0', [
            'limit'      => 20,
            'fetch_plan' => '*:-1',
            '_callback'  => $myFunction
        ] );

        foreach( $result as $res ){
            $data = $res->getOData();
            if( isset($data['in_followed_by']) ){
                $data = $data['in_followed_by'];
                foreach( $data as $k => $val ){
                    $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\ID', $val );
                }
            } else {
                $this->assertNotEmpty($data);
                $this->assertTrue( is_array( $data ) );
            }
        }

    }

}
