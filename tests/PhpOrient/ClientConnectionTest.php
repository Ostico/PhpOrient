<?php

namespace PhpOrient;

class ClientConnectionTest extends TestCase {

    public function testSelfCreation() {
        $client = $this->createClient();
        $transport = $client->getTransport();
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\AbstractTransport', $transport );
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\TransportInterface', $transport );
        $client->execute('connect');
        $this->assertNotEquals( -1, $client->getTransport()->getSessionId() );
        $this->assertNotEquals( -1, $client->getTransport()->getProtocol() );

    }

    public function testManualConfiguration() {

        $config = static::getConfig();

        $client = new Client();
        $client->hostname = $config[ 'hostname' ];
        $client->port     = $config[ 'port' ];
        $client->username = $config[ 'username' ];
        $client->password = $config[ 'password' ];

        $transport = new \PhpOrient\Protocols\Binary\Transport();
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\AbstractTransport', $transport );
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\TransportInterface', $transport );

        $transport->configure( $config );

        $client->setTransport( $transport );

        $client->execute('connect');
        $this->assertNotEquals( -1, $client->getTransport()->getSessionId() );
        $this->assertNotEquals( -1, $client->getTransport()->getProtocol() );

    }

    public function testManualConfiguration2() {

        $config = static::getConfig();

        $client = new Client();

        $transport = new \PhpOrient\Protocols\Binary\Transport();
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\AbstractTransport', $transport );
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\TransportInterface', $transport );

        $transport->configure( $config );

        $client->setTransport( $transport );

        $client->execute('connect',$config);
        $this->assertNotEquals( -1, $client->getTransport()->getSessionId() );
        $this->assertNotEquals( -1, $client->getTransport()->getProtocol() );

    }

    public function testAnotherConfiguration() {

        $config = static::getConfig();

        $client = new Client();

        $transport = $client->getTransport();
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\AbstractTransport', $transport );
        $this->assertInstanceOf( '\PhpOrient\Protocols\Common\TransportInterface', $transport );

        $transport->configure( $config );

        $client->execute('connect');
        $this->assertNotEquals( -1, $client->getTransport()->getSessionId() );
        $this->assertNotEquals( -1, $client->getTransport()->getProtocol() );

    }

    public function testINVALIDConfiguration() {

        $client = new Client();
        $client->getTransport();
        $this->setExpectedException( '\PhpOrient\Exceptions\TransportException',
                'Can not initialize a transport ' .
                'without connection parameters');
        $client->execute('connect');

    }

}
