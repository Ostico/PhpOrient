<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 07/12/14
 * Time: 15.02
 *
 */

namespace PhpOrient;
use PhpOrient\Abstracts\TestCase;

use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Data\Record;
use PhpOrient\Protocols\Common\Constants;

class SQLCommandsTest extends TestCase {

    protected $db_name = 'test_sql_commands';

    public function testOne(){
        $result = $this->client->execute( 'command', [
            'command' => Constants::QUERY_CMD,
            'query'   => 'create class my_class extends V'
        ] );

        $this->assertNotEmpty( $result );
        $this->assertInternalType( 'string', $result );

    }

    public function testTwo(){

        $this->testOne();

        $res1 = $this->client->execute( 'command', [
                'command' => Constants::QUERY_CMD,
                'query'   => "create vertex my_class content { 'Band': 'AC/DC', 'Song': 'Hells Bells' }"
            ]
        );
        $res2 = $this->client->execute( 'command', [
                'command' => Constants::QUERY_CMD,
                'query'   => "create vertex my_class content { 'Band': 'AC/DC', 'Song': 'Who Made Who' }"
            ]
        );
        $res3 = $this->client->execute( 'command', [
                'command' => Constants::QUERY_CMD,
                'query'   => "create vertex my_class content { 'Band': 'AC/DC', 'Song': 'T.N.T.' }"
            ]
        );
        $res4 = $this->client->execute( 'command', [
                'command' => Constants::QUERY_CMD,
                'query'   => "create vertex my_class content { 'Band': 'AC/DC', 'Song': 'High Voltage' }"
            ]
        );

        $this->assertNotEmpty( $res1 );
        $this->assertEquals( '11', $res1->getRid()->cluster );
        $this->assertEquals( '0', $res1->getRid()->position );
        $this->assertEquals( 'my_class', $res1->getOClass() );
        $this->assertTrue( $res1->getVersion() > -1 );

        $this->assertNotEmpty( $res2 );
        $this->assertEquals( '11', $res2->getRid()->cluster );
        $this->assertEquals( '1', $res2->getRid()->position );
        $this->assertEquals( 'my_class', $res2->getOClass() );
        $this->assertTrue( $res1->getVersion() > -1 );

        $this->assertNotEmpty( $res3 );
        $this->assertEquals( '11', $res3->getRid()->cluster );
        $this->assertEquals( '2', $res3->getRid()->position );
        $this->assertEquals( 'my_class', $res3->getOClass() );
        $this->assertTrue( $res1->getVersion() > -1 );
        $this->assertEquals( 'AC/DC', $res3['Band'] );
        $this->assertEquals( 'T.N.T.', $res3->Song );
        $res3Version = $res1->getVersion();

        $this->assertNotEmpty( $res4 );
        $this->assertEquals( '11', $res4->getRid()->cluster );
        $this->assertEquals( '3', $res4->getRid()->position );
        $this->assertEquals( 'my_class', $res4->getOClass() );
        $this->assertTrue( $res1->getVersion() > -1 );

        $upd = $this->client->execute( 'command', [
                'command' => Constants::QUERY_CMD,
                'query'   => "update my_class set Band = 'KoRn', Song = 'Make me bad' where @rid='" . $res3->getRid() . "'"
            ]
        );
        $this->assertEquals( 1, $upd );

        $res5 = $this->client->execute( 'command', [
                'command' => Constants::QUERY_SYNC,
                'query'   => "select from my_class where @rid='" . $res3->getRid() . "'"
            ]
        );

        $this->assertNotEmpty( $res5[0] );
        $this->assertEquals( '11', $res5[0]->getRid()->cluster );
        $this->assertEquals( '2', $res5[0]->getRid()->position );
        $this->assertEquals( 'my_class', $res5[0]->getOClass() );
        $this->assertTrue( $res5[0]->getVersion() > $res3Version );
        $this->assertEquals( 'KoRn', $res5[0]['Band'] );
        $this->assertEquals( 'Make me bad', $res5[0]->Song );

        $load = $this->client->execute( 'recordLoad', [ 'rid' => $res5[0]->getRid() ]);
        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $load[0] );
        $this->assertEquals( '#11:2', (string)$load[0]->getRid() );
        $this->assertEquals( (string)$res5[0], (string)$load[0] );

    }

    public function testSQLBatch(){

        $cmd = 'begin;' .
               'let a = create vertex set script = true;' .
               'let b = select from v limit 1;' .
               'let e = create edge from $a to $b;' .
               'commit retry 100;';

        $result = $this->client->sqlBatch( $cmd );

        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $result[0] );

    }

    public function testDateDeserialization(){
        $client = new PhpOrient('localhost',2424);
        $client->dbOpen( 'GratefulDeadConcerts', 'admin', 'admin' );

        $dateToTest = \DateTime::createFromFormat( 'U', time() )->format( 'Y-m-d H:i:s' );

        $result = $client->query("SELECT DATE( SYSDATE('yyy-MM-dd HH:mm:ss') ) FROM V LIMIT 1");
        $this->assertEquals( $dateToTest, $result[0]->getOData()['DATE']->format('Y-m-d H:i:s') );

    }

    public function testNullValueHandling(){

        try {

            $client = new PhpOrient('localhost', 2424);
            $client->username = 'root';
            $client->password = 'root';
            $client->connect();

            try {
                $client->dbDrop( 'temp',
                    Constants::STORAGE_TYPE_MEMORY,
                    Constants::DATABASE_TYPE_DOCUMENT
                );
            } catch ( \Exception $e ) {
//            echo $e->getMessage();
                $client->getTransport()->debug( $e->getMessage() );
            }

            $client->dbCreate( 'temp',
                Constants::STORAGE_TYPE_MEMORY,
                Constants::DATABASE_TYPE_GRAPH
            );

            $client->dbOpen('temp');
            $client->sqlBatch('
                create class Prova1;
                create property Prova1.aString string;
                insert into Prova1 (aString) VALUES ("b"),("c"),("d");
                create class Prova2;
                create property Prova2.aString string;
                create property Prova2.anEmbeddedSetOfString embeddedset string;
                create property Prova2.prova1 link Prova1;');

            $clusterProva1 = $client->query("select classes[name='Prova1'].defaultClusterId from 0:1", -1)[0]['classes'];
            $clusterProva2 = $client->query("select classes[name='Prova2'].defaultClusterId from 0:1", -1)[0]['classes'];

//            echo "Default cluster for Prova1: $clusterProva1\n";
//            echo "Default cluster for Prova2: $clusterProva2\n\n";

            $newRecord = ['oClass' => 'Prova2', 'oData' => [
                'aString'               => 'record di prova',
                'anEmbeddedSetOfString' => ['qualcosa 1', 'qualcosa 2', 'ancora altro'],
                'prova1'                => null
            ]];

            $newRecordObject = Record::fromConfig($newRecord);
            $newRecordObject->setRid(new ID($clusterProva2) );

            $tmp = $client->recordCreate($newRecordObject);

            $record = $client->recordLoad($tmp->getRid())[0];

            $this->assertEquals( 'record di prova', $record->getOData()['aString'] );
            $this->assertEquals( null, $record->getOData()['prova1'] );

//            print_r($record->getOData());

        } catch (\Exception $e) {
            echo $e . "\n";
        }

    }

    public function testWrongClusterID(){
        $client = new PhpOrient('localhost', 2424);
        $client->dbOpen( 'GratefulDeadConcerts', 'admin', 'admin' );

        $records = $client->query( 'select song_type, name from V ' );

        /**
         * @var Record[] $records
         */
        foreach ($records as $k => $rec) {

            if ( $client->getTransport()->getProtocolVersion() < 26 )
                $this->assertEquals( $k +1, $rec->getRid()->position );
            else
                $this->assertEquals( $k, $rec->getRid()->position );

            $this->assertEquals( -2, $rec->getRid()->cluster );
        }
    }

}