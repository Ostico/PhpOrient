<?php
/**
 * User: gremorian
 * Date: 22/12/14
 * Time: 23.05
 *
 */

namespace PhpOrient;


use PhpOrient\Abstracts\EmptyTestCase;
use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Data\Record;
use PhpOrient\Protocols\Common\Constants;

class ConnectionTokenTest extends EmptyTestCase {

    protected $token = '';

    protected $backupGlobalsBlacklist = array('old_db_token','old_root_token');

    public function setUp(){

        parent::setUp();
        $client = $this->createClient('connect');
        $client->connect();
        $client->dbOpen( 'GratefulDeadConcerts', 'admin', 'admin' );

        if ( $client->getTransport()->getProtocolVersion() < 26 ) {
            $this->markTestSkipped( "Token not supported in OrientDB < 2.0" );
        }

        if ( $client->getTransport()->getProtocolVersion() >= 32 && $client->getTransport()->getOrientVersion()->getBuildNumber() < 4 ) {
            $this->markTestSkipped( "Token not well supported in OrientDB >= 2.2.0 and < 2.4.0" );
        }

        if(
            (
                $client->getTransport()->getOrientVersion()->getMinorVersion() >= 0 &&
                $client->getTransport()->getOrientVersion()->getBuildNumber() >= 18
            ) && (
                $client->getTransport()->getOrientVersion()->getMinorVersion() >= 1 &&
                $client->getTransport()->getOrientVersion()->getBuildNumber() <= 25
            )
        ){
            $this->markTestSkipped( "Token not well supported in OrientDB >= 2.0.18 and < 2.1.25" );
        }

    }

    public function tearDown(){
        parent::tearDown();

    }

    public function testPrepareConnection(){
        $this->client->setSessionToken( true );
        $open   = $this->client->dbOpen( "GratefulDeadConcerts", 'admin', 'admin' );
        $record = $this->client->query( 'select from V limit 1' );
        $this->assertNotEmpty( $this->client->getSessionToken() );
        $GLOBALS[ 'old_db_token' ] = $this->client->getSessionToken();
    }

    public function testReconnection(){

        $this->assertEmpty( $this->client->getSessionToken() );
        $old_token = $GLOBALS[ 'old_db_token' ];
        $this->client->setSessionToken( $old_token );
        $record = $this->client->query( 'select from V limit 1' );
        $this->assertNotEmpty( $record );
        $this->assertContainsOnly( '\PhpOrient\Protocols\Binary\Data\Record', $record );

    }

    public function testReconnectionFailRoot(){
        $this->assertEmpty( $this->client->getSessionToken() );
        $old_token = $GLOBALS[ 'old_db_token' ];

        //this because the connection credentials
        // are not correct for Orient root access
        $this->setExpectedException( '\PhpOrient\Exceptions\PhpOrientException' );
        $res = $this->client->setSessionToken( $old_token )->dbExists("GratefulDeadConcerts");

    }

    public function testReconnectionRoot(){
        $this->assertEmpty( $this->client->getSessionToken() );
        $old_token = $GLOBALS[ 'old_db_token' ];

        //even if i pass the old token it will be ignored from server
        //and it will create a new one because of root connection
        $res = $this->client->setSessionToken( $old_token )->connect('root','root');
        $this->assertNotEquals( $this->client->getSessionToken(), $old_token );
        $GLOBALS[ 'old_root_token' ] = $this->client->getSessionToken();

    }

    public function testServerCommandsOps(){
        $this->assertEmpty( $this->client->getSessionToken() );
        $old_root_token = $GLOBALS[ 'old_root_token' ];
        $clusterID = $this->client
                        ->setSessionToken( $old_root_token )
                        ->dbCreate( 'new_test_db', PhpOrient::STORAGE_TYPE_MEMORY );

        $this->assertNotEmpty( $clusterID );

        $this->assertTrue( $this->client->dbExists( 'new_test_db' ) );

        $this->assertTrue( $this->client->dbFreeze( 'new_test_db' ) );
        $this->assertTrue( $this->client->dbRelease( 'new_test_db' ) );

        $list = $this->client->dbList();
        $this->assertNotEmpty( $list );

        $this->assertTrue( $this->client->dbDrop( 'new_test_db' ) );

    }

    public function testDatabaseOps(){

        // ROOT Table creation
        $client = new PhpOrient( 'localhost', 2424 );
        $client->setSessionToken( true );
        $clusterID = $client->connect( 'root', 'root' );
        if( $client->dbExists( 'new_test_db_2' ) ) {
            $client->dbDrop( 'new_test_db_2', PhpOrient::STORAGE_TYPE_MEMORY );
        }
        $client->dbCreate( 'new_test_db_2', PhpOrient::STORAGE_TYPE_MEMORY );
        $this->assertNotEmpty( $clusterID );
        $this->assertNotEmpty( $client->getSessionToken() );
        $this->assertEmpty( $this->client->getSessionToken() );

        $this->client->setSessionToken( true );
        $this->client->dbOpen( 'new_test_db_2', 'admin', 'admin' );
        $admin_Token = $this->client->getSessionToken();

        $c = $this->client->command( "Create class test_class extends V" );

        $rec = ( new Record() )->setOClass( 'test_class' )->setOData( [ true ] )->setRid( new ID( -1, -1 ) );
        $rec = $this->client->recordCreate( $rec );

        $rec1 = $this->client->recordLoad( $rec->getRid() );
        $rec2 = $this->client->query("select from {$rec->getRid()}" );

        $this->assertEquals( $rec1, $rec2 ); // useful?? they are empty

        //renew the token and connect to new database as user
        $this->client->setSessionToken( true );
        $this->client->dbOpen( 'new_test_db_2', 'admin', 'admin' );
        $new_token = $this->client->getSessionToken();
        $this->assertNotEquals( $admin_Token, $new_token );
        $GLOBALS[ 'old_db_token' ] = $new_token;

        $rec = ( new Record() )->setOClass( 'test_class' )->setOData( [ true ] )->setRid( new ID( -1, -1 ) );
        $rec = $this->client->recordCreate( $rec );
        $rec2 = $this->client->query( "select from {$rec->getRid()}" );
        $this->assertEquals( $rec, $rec2[0] );

        $this->client->setSessionToken( $admin_Token ); //Not valid token to perform database ops
        $this->setExpectedException( '\PhpOrient\Exceptions\PhpOrientException' );
        $this->client->dbDrop( 'new_test_db_2' );

    }

    public function testSessionRenew(){

        $client = new PhpOrient( 'localhost', 2424 );
        $client->setSessionToken( true );  // set true to enable the token based authentication
        $clusterID    = $client->dbOpen( "GratefulDeadConcerts", 'admin', 'admin' );
        $sessionToken = $client->getSessionToken(); // store this token somewhere
        unset($client);

        // start a new connection
        $client = new PhpOrient( 'localhost', 2424 );

        // set the previous received token to re-attach to the old session
        $client->setSessionToken( $sessionToken );

        //now the dbOpen is not needed to perform database operations
        $records = $client->query( 'select * from V limit 10' );
        $this->assertNotEmpty( $records );

        //set the flag again to true if you want to renew the token
        $client->setSessionToken( true );  // set true
        $clusterID        = $client->dbOpen( "GratefulDeadConcerts", 'admin', 'admin' );
        $new_sessionToken = $client->getSessionToken();

        $this->assertNotEquals( $sessionToken, $new_sessionToken );

    }

    public function testWrongTokenWithNoInitialization(){

        $client = new PhpOrient( 'localhost', 2424 );
        $client->setSessionToken( true );  // set true to enable the token based authentication
        $clusterID    = $client->dbOpen( "GratefulDeadConcerts", 'admin', 'admin' );
        $sessionToken = $client->getSessionToken(); // store this token somewhere
        file_put_contents( "token.bin", $sessionToken );
        unset($client);

        $sessionToken = file_get_contents( "token.bin" );
        unlink( "token.bin" );

        // start a new connection
        $client = new PhpOrient( 'localhost', 2424 );

        // set the previous received token to re-attach to the old session
        $client->setSessionToken( $sessionToken . "WRONG_TOKEN" );
//        $client->setSessionToken( $sessionToken );

        $this->setExpectedException( '\PhpOrient\Exceptions\PhpOrientException' );

        //now the dbOpen is not needed to perform database operations
        $client->query( 'select * from V limit 10' );

    }

}