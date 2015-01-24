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
use PhpOrient\Protocols\Common\Constants;

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
            'fetch_plan' => '*:2',
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

    /**
     *
     */
    public function testLinkSetCreation(){

        $config = static::getConfig( 'connect' );
        $client = new PhpOrient( 'localhost', 2424 );
        $client->configure( array(
            'username' => $config[ 'username' ],
            'password' => $config[ 'password' ]
        ) );

        $client->connect();

        try {
            $client->dbDrop( 'temp', Constants::STORAGE_TYPE_MEMORY );
        } catch ( \Exception $e ) {
//            echo $e->getMessage();
            $client->getTransport()->debug( $e->getMessage() );
        }

        $client->dbCreate('temp',
            Constants::STORAGE_TYPE_MEMORY,
            Constants::DATABASE_TYPE_DOCUMENT
        );

        $client->dbOpen('temp');

        $client->sqlBatch('
            create class Test1;
            create property Test1.aString string;
            insert into Test1 (aString) VALUES ("b"),("c"),("d");
            create class Test2;
            create property Test2.aString string;
            create property Test2.anEmbeddedSetOfString embeddedset string;
            create property Test2.aLinkedSetOfTest1 linkset Test1;'
        );


        $clusterTest1 = $client->query("select classes[name='Test1'].defaultClusterId from 0:1", -1)[0]['classes'];
        $clusterTest2 = $client->query("select classes[name='Test2'].defaultClusterId from 0:1", -1)[0]['classes'];

        $this->assertEquals( '9', $clusterTest1 );
        $this->assertEquals( '10', $clusterTest2 );

        $newRecord = [
            'oClass' => 'Test2',
            'oData'  => [
                'aString'               => 'Test record',
                'anEmbeddedSetOfString' => [ 'something 1', 'something 2', 'more other' ],
                'aLinkedSetOfTest1'     => [ new ID( $clusterTest1, 1 ), new ID( $clusterTest1, 2 ) ]
            ]
        ];

        $newRecordObject = Record::fromConfig( $newRecord );
        $newRecordObject->setRid( new ID( $clusterTest2 ) );

        $tmp = $client->recordCreate( $newRecordObject );

        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $tmp );

        /**
         * @var \PhpOrient\Protocols\Binary\Data\Record $record
         */
        $record = $client->recordLoad( $tmp->getRid() )[ 0 ];

        $this->assertEquals( 'Test record', $record->aString );

        $this->assertArrayHasKey( 0, $record->anEmbeddedSetOfString );
        $this->assertArrayHasKey( 1, $record->anEmbeddedSetOfString );
        $this->assertArrayHasKey( 2, $record->anEmbeddedSetOfString );

        $this->assertEquals( 'something 1', $record->anEmbeddedSetOfString[0] );
        $this->assertEquals( 'something 2', $record->anEmbeddedSetOfString[1] );
        $this->assertEquals( 'more other',  $record->anEmbeddedSetOfString[2] );

        $this->assertArrayHasKey( 0, $record->aLinkedSetOfTest1 );
        $this->assertArrayHasKey( 1, $record->aLinkedSetOfTest1 );

        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\ID', $record->aLinkedSetOfTest1[0] );
        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\ID', $record->aLinkedSetOfTest1[1] );

        $aLinkedSetOfTest1 = $record->aLinkedSetOfTest1;

        /**
         * @var \PhpOrient\Protocols\Binary\Data\ID[] $aLinkedSetOfTest1
         */
        $this->assertEquals( '#9:1', $aLinkedSetOfTest1[0]->jsonSerialize() );
        $this->assertEquals( '#9:2', $aLinkedSetOfTest1[1]->__toString() );

    }

}
