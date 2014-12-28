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

}