<?php

namespace PhpOrient;
use PhpOrient\Abstracts\EmptyTestCase;
use PhpOrient\Configuration\Constants;

class ClientConnectionTest extends EmptyTestCase {

    public function testLogging(){
        Constants::$LOGGING = true;
        Constants::$LOG_FILE_PATH = '/dev/null';
        $result = $this->client->execute( 'dbOpen', [ 'database' => 'GratefulDeadConcerts' ] );
        $this->assertNotEquals( -1, $result['sessionId'] );
        $this->assertNotEmpty( count( $result ) );
    }

    public function testSelfCreation() {
        $client = $this->createClient();
        $transport = $client->getTransport();
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\AbstractTransport', $transport );
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\TransportInterface', $transport );
        $client->execute('connect', ['username' => 'root', 'password' => 'root'] );
        $this->assertNotEquals( -1, $client->getTransport()->getSessionId() );
        $this->assertNotEquals( -1, $client->getTransport()->getProtocolVersion() );

    }

    public function testManualConfiguration() {

        $config = static::getConfig( 'connect' );

        $client = new PhpOrient();
        $client->hostname = $config[ 'hostname' ];
        $client->port     = $config[ 'port' ];
        $client->username = $config[ 'username' ];
        $client->password = $config[ 'password' ];

        $transport = new \PhpOrient\Protocols\Binary\SocketTransport();
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\AbstractTransport', $transport );
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\TransportInterface', $transport );

        $transport->configure( $config );

        $client->setTransport( $transport );

        $client->execute('connect');
        $this->assertNotEquals( -1, $client->getTransport()->getSessionId() );
        $this->assertNotEquals( -1, $client->getTransport()->getProtocolVersion() );

    }

    public function testManualConfiguration2() {

        $config = static::getConfig('connect');
        $client = new PhpOrient();

        $transport = new \PhpOrient\Protocols\Binary\SocketTransport();
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\AbstractTransport', $transport );
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\TransportInterface', $transport );

        $transport->configure( $config );

        $client->setTransport( $transport );

        $client->execute( 'connect', $config );
        $this->assertNotEquals( -1, $client->getTransport()->getSessionId() );
        $this->assertNotEquals( -1, $client->getTransport()->getProtocolVersion() );

    }

    public function testAnotherConfiguration() {

        $config = static::getConfig( 'connect' );

        $client = new PhpOrient();

        $transport = $client->getTransport();
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\AbstractTransport', $transport );
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\TransportInterface', $transport );

        $transport->configure( $config );

        $client->execute( 'connect' );
        $this->assertNotEquals( -1, $client->getTransport()->getSessionId() );
        $this->assertNotEquals( -1, $client->getTransport()->getProtocolVersion() );

    }

    public function testINVALIDConfiguration() {

        $client = new PhpOrient();
        $client->getTransport();
        $this->setExpectedException( '\PhpOrient\Exceptions\SocketException',
                'Can not initialize a connection ' .
                'without connection parameters');
        $client->execute('connect');

    }

    public function testWrongProtocol_1() {
        $config = $this->getConfig( 'connect' );
        $client = new PhpOrient();
        $client->configure(  $config  );
        $this->setExpectedException( '\PhpOrient\Exceptions\PhpOrientWrongProtocolVersionException' );
        $client->connect( null, null, PhpOrient::SERIALIZATION_SERIAL_BIN );

    }

    public function testWrongProtocol_2() {

        $config = $this->getConfig( 'open' );
        $client = new PhpOrient();
        $client->configure(  $config  );
        $this->setExpectedException( '\PhpOrient\Exceptions\PhpOrientWrongProtocolVersionException' );
        $client->dbOpen( 'GratefulDeadConcerts', 'admin', 'admin',
            [ 'serializationType' => PhpOrient::SERIALIZATION_SERIAL_BIN, ]
        );

    }

}
