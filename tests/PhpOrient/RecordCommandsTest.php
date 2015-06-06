<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 07/12/14
 * Time: 15.02
 *
 */

namespace PhpOrient;
use PhpOrient\Abstracts\TestCase;
use PhpOrient\Configuration\Constants as ClientConstants;
use PhpOrient\Protocols\Common\AbstractTransport;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Data\Record;

class RecordCommandsTest extends TestCase {

    protected $db_name = 'test_record_commands';

    public function testRecordLoad() {

        $this->cluster_struct = $this->client->execute( 'dbOpen', [
            'database' => 'GratefulDeadConcerts'
        ] );

        $res = $this->client->execute( 'recordLoad', [
            'rid' => new ID( "#9:5" ),
            'fetch_plan' => '*:2'

            , '_callback' => function ( Record $arg ){
               $this->assertNotEmpty( $arg->getOData() );

            }

        ] );

        $this->assertNotEmpty( $res );

    }

    public function testCreateLoad(){

        if( $this->client->getTransport()->getProtocolVersion() < 26 ){
//            $this->markTestSkipped( 'Record Create/Update Unpredictable Behaviour' );
        }

        $rec1 = [
            'oData' => [
                'alloggio' => 'baita',
                'lavoro'   => 'no',
                'vacanza'  => 'lago'
            ]
        ];

        $rec = Record::fromConfig( $rec1 );
        $result = $this->client->execute( 'recordCreate', [
                'cluster_id' => 9,
                'record'    => $rec
            ]
        );

        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $result );
        $this->assertEquals( '#9:0', (string)$result->getRid() );

//        var_export( $result . "\n" );

        $rec2 = [ 'oData' => [ 'alloggio' => 'albergo', 'lavoro' => 'ufficio', 'vacanza' => 'montagna' ] ];
        $rec = Record::fromConfig( $rec2 );
        $result = $this->client->execute( 'recordCreate', [
                'cluster_id' => 9,
                'record'    => $rec
            ]
        );

        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $result );
        $this->assertEquals( '#9:1', (string)$result->getRid() );
//        var_export( $result . "\n" );

        $rec3 = [ 'alloggio' => 'case', 'lavoro' => 'mercato', 'vacanza' => 'mare' ];
        $rec = new Record();
        $rec->setOData( $rec3 );
        $rec->setOClass( 'V' );
        $result = $this->client->execute( 'recordCreate', [
                'cluster_id' => 9,
                'record'    => $rec
            ]
        );

        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $result );
        $this->assertEquals( '#9:2', (string)$result->getRid() );

//        var_export( $result . "\n" );

        $load = $this->client->execute( 'recordLoad', [ 'rid' => $result->getRid() ]);
        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $load[0] );
        $this->assertEquals( '#9:2', (string)$load[0]->getRid() );
        $this->assertEquals( (string)$rec, (string)$load[0] );

//        var_export( $load[0] . "\n" );

    }

    public function testCreateUpdateLoad(){

        if( $this->client->getTransport()->getProtocolVersion() < 26 ){
//            $this->markTestSkipped( 'Record Create/Update Unpredictable Behaviour' );
        }

        $recOrig = [ 'alloggio' => 'case', 'lavoro' => 'mercato', 'vacanza' => 'mare' ];
        $rec = new Record();
        $rec->setOData( $recOrig );
        $result = $this->client->execute( 'recordCreate', [
                'cluster_id' => 9,
                'record'    => $rec
            ]
        );

        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $result );
        $this->assertEquals( '#9:0', (string)$result->getRid() );
        $this->assertEquals( '9', $result->getRid()->cluster );
        $this->assertEquals( '0', $result->getRid()->position );

        $recUp = [ 'alloggio' => 'home', 'lavoro' => 'bazar', 'vacanza' => 'sea' ];
        $rec2 = new Record();
        $rec2->setOData( $recUp );
        $rec2->setOClass( 'V' );
        $updated = $this->client->execute( 'recordUpdate', [
                'cluster_id'       => '9',
                'cluster_position' => '0',
                'record'           => $rec2
            ]
        );

        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $result );
        $this->assertEquals( '#9:0', (string)$updated->getRid() );
        $this->assertEquals( '9', $updated->getRid()->cluster );
        $this->assertEquals( '0', $updated->getRid()->position );
        $this->assertTrue( $updated->getVersion() > 0 );

        $this->assertEquals( (string)$rec2, (string)$updated );

        $load = $this->client->execute( 'recordLoad', [ 'rid' => $updated->getRid() ])[0];
        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $load );
        $this->assertEquals( (string)$updated->getRid(), (string)$load->getRid() );
        $this->assertEquals( (string)$updated, (string)$load );

    }

    public function testReLoadUpdate(){

        if( $this->client->getTransport()->getProtocolVersion() < 26 ){
//            $this->markTestSkipped( 'Record Create/Update Unpredictable Behaviour' );
        }

        $recOrig = [ 'alloggio' => 'case', 'lavoro' => 'mercato', 'vacanza' => 'mare' ];
        $rec = new Record();
        $rec->setOData( $recOrig );
        $result = $this->client->execute( 'recordCreate', [
                'cluster_id' => 9,
                'record'    => $rec
            ]
        );

        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $result );
        $this->assertEquals( '#9:0', (string)$result->getRid() );
        $this->assertEquals( '9', $result->getRid()->cluster );
        $this->assertEquals( '0', $result->getRid()->position );

        $recUp = [ 'alloggio' => 'home', 'lavoro' => 'bazar', 'vacanza' => 'sea' ];

        //push the old record with a new value
        $rec->setOData( $recUp );
        $rec->setOClass( 'V' );
        $updated = $this->client->execute( 'recordUpdate', [
                'rid'              => $rec->getRid(),
                'record'           => $rec
            ]
        );

        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $result );
        $this->assertEquals( '#9:0', (string)$updated->getRid() );
        $this->assertEquals( '9', $updated->getRid()->cluster );
        $this->assertEquals( '0', $updated->getRid()->position );
        $this->assertTrue( $updated->getVersion() > 0 );
        //assert that the created record is the same as the updated
        // ( why not should be?? is the same object this time )
        $this->assertEquals( (string)$rec, (string)$updated );

        $load = $this->client->execute( 'recordLoad', [ 'rid' => $updated->getRid() ]);
        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $load[0] );
        $this->assertEquals( (string)$updated->getRid(), (string)$load[0]->getRid() );
        $this->assertEquals( (string)$updated, (string)$load[0] );

    }

    public function testCreateLoadDeleteLoad(){

        if( $this->client->getTransport()->getProtocolVersion() < 26 ){
//            $this->markTestSkipped( 'Record Create/Update Unpredictable Behaviour' );
        }

        $recOrig = [ 'alloggio' => 'case', 'lavoro' => 'mercato', 'vacanza' => 'mare' ];
        $rec = new Record();
        $rec->setOData( $recOrig );
        $rec->setOClass( 'V' );
        $result = $this->client->execute( 'recordCreate', [
                'cluster_id' => 9,
                'record'    => $rec
            ]
        );

        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $result );
        $this->assertEquals( '#9:0', (string)$result->getRid() );
        $this->assertEquals( '9', $result->getRid()->cluster );
        $this->assertEquals( '0', $result->getRid()->position );

        $load = $this->client->execute( 'recordLoad', [ 'rid' => $result->getRid() ]);
        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $load[0] );
        $this->assertEquals( (string)$result->getRid(), (string)$load[0]->getRid() );
        $this->assertEquals( (string)$result, (string)$load[0] );

        $delete = $this->client->execute( 'recordDelete', [ 'rid' => $load[0]->getRid() ] );
        $this->assertTrue( $delete );

        //try load again, this should be empty
        $reLoad = $this->client->execute( 'recordLoad', [ 'rid' => $result->getRid() ]);
        $this->assertEmpty( $reLoad );

        $result = $this->client->execute( 'DataClusterCount', [
                'ids' => 9
            ]
        );

        $this->assertEmpty( $result );

    }

    public function testLimit() {

        $this->cluster_struct = $this->client->execute( 'dbOpen', [
                'database' => 'GratefulDeadConcerts'
        ] );

        $response = $this->client->query( 'select from V limit 4' );
        $this->assertCount( 4, $response );
        $response = $this->client->query( 'select from V limit 29' );
        $this->assertCount( 29, $response );

        $response = $this->client->query( 'select from V limit 4', 40 );
        $this->assertCount( 4, $response );
        $response = $this->client->query( 'select from V limit 29', 40 );
        $this->assertCount( 29, $response );

        $response = $this->client->query( 'select from V', 40 );
        $this->assertCount( 40, $response );

        $response = $this->client->query( 'select from V' );
        $this->assertCount( 20, $response );

    }

    public function testHiLevelCreateDelete(){

        $config = static::getConfig();
        $this->client = new PhpOrient();
        $this->client->configure( array(
            'username' => $config[ 'username' ],
            'password' => $config[ 'password' ],
            'hostname' => $config[ 'hostname' ],
            'port'     => $config[ 'port' ],
        ) );

        $this->client->dbOpen( $this->db_name, 'admin', 'admin' );

        $recOrig = [ 'accommodation' => 'case', 'work' => 'mercato', 'holiday' => 'mare' ];
        $rec = new Record();
        $rec->setOData( $recOrig );
        $rec->setOClass( 'V' );
        $rec->setRid( new ID(9) );
        $result = $this->client->recordCreate( $rec );

        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $result );
        $this->assertEquals( '#9:0', (string)$result->getRid() );
        $this->assertEquals( '9', $result->getRid()->cluster );
        $this->assertEquals( '0', $result->getRid()->position );

        $load = $this->client->recordLoad( $result->getRid() );
        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $load[0] );
        $this->assertEquals( (string)$result->getRid(), (string)$load[0]->getRid() );
        $this->assertEquals( (string)$result, (string)$load[0] );

        $_recUp = [ 'accommodation' => 'hotel', 'work' => 'office', 'holiday' => 'mountain' ];
        $recUp = ( new Record() )->setOData( $_recUp )->setOClass( 'V' )->setRid( $result->getRid() );
        $updated0 = $this->client->recordUpdate( $recUp );
        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $updated0 );

        $delete = $this->client->recordDelete( $load[0]->getRid() );
        $this->assertTrue( $delete );

        //try load again, this should be empty
        $reLoad = $this->client->recordLoad( $result->getRid() );
        $this->assertEmpty( $reLoad );

        $result = $this->client->dataClusterCount( [ 9 ] );

        $this->assertEmpty( $result );
    }

    public function testUpdateEdges(){

        $client = PhpOrient::fromConfig(
            array(
                'username' => 'root',
                'password' => 'root',
                'hostname' => 'localhost',
                'port'     => 2424
            )
        );

        $res = $client->execute('connect');

        try {
            $client->dbDrop( "db_test_edges", Constants::STORAGE_TYPE_MEMORY );
        } catch ( \Exception $e ) {
//            echo $e->getMessage();
            $client->getTransport()->debug( $e->getMessage() );
        }

        $client->dbCreate( "db_test_edges",
            Constants::STORAGE_TYPE_MEMORY,
            Constants::DATABASE_TYPE_GRAPH
        );

        $orientInfo = $client->dbOpen( "db_test_edges", 'admin', 'admin' );

        $cmd = 'begin;' .
            'let a = create vertex set script = true;' .
            'let b = select from v limit 1;' .
            'let e = create edge from $a to $b;' .
            'commit retry 100;';

        $lastRecord = $client->sqlBatch( $cmd );
        $lastRecord = $client->sqlBatch( $cmd );
        $lastRecord = $client->sqlBatch( $cmd );
        $lastRecord = $client->sqlBatch( $cmd );
        $lastRecord = $client->sqlBatch( $cmd );

        $rec = $client->recordLoad( new ID("#9:0") )[0];

        /**
         * @var $bag \PhpOrient\Protocols\Binary\Data\Bag
         */
        $bag = $rec->getOData()['in_'];
        $this->assertNotEmpty( $bag->getRawBagContent() );
        $client->recordUpdate($rec);

        /**
         * @var $bag2 \PhpOrient\Protocols\Binary\Data\Bag
         */
        $rec = $client->recordLoad( new ID("#9:0") )[0];
        $bag2 = $rec->getOData()['in_'];
        $this->assertNotEmpty( $bag2->getRawBagContent() );

        if( $orientInfo->getMajorVersion() >= 2
            && $orientInfo->getMinorVersion() >= 0
            && ( $orientInfo->getBuildNumber() >= 7 || !is_numeric( $orientInfo->getBuildNumber() ) )
        ) {
            $this->assertEquals( $bag->getRawBagContent(), $bag2->getRawBagContent() );
        }

    }

    public function testRecordEmbedded(){

        $client = PhpOrient::fromConfig(
            array(
                'username' => 'root',
                'password' => 'root',
                'hostname' => 'localhost',
                'port'     => 2424
            )
        );

        $res = $client->execute('connect');

        try {
            $client->dbDrop( "db_test_embed", Constants::STORAGE_TYPE_MEMORY );
        } catch ( \Exception $e ) {
//            echo $e->getMessage();
            $client->getTransport()->debug( $e->getMessage() );
        }

        $client->dbCreate( "db_test_embed",
            Constants::STORAGE_TYPE_MEMORY,
            Constants::DATABASE_TYPE_GRAPH
        );

        $orientInfo = $client->dbOpen( "db_test_embed", 'admin', 'admin' );

        $client->command( "create class Test extends V" );
        $client->command( "create class TestInfo" );
        $client->command( "create property Test.attr1 string" );
        $client->command( "create property Test.attr2 embedded TestInfo" );
        $recID = $client->command( 'insert into Test set attr1 = "test", attr2 = {"@class": "TestInfo", "@type": "d", "subAttr1": "sub test"}' );

        $this->assertNotEmpty( $recID->getRid() );

        $record = $client->recordLoad( $recID->getRid() )[0];

        $this->assertEquals( $recID, $record );

        $updatedRecord = $client->recordUpdate( $record );

        $this->assertEquals( $record, $updatedRecord );

    }

} 