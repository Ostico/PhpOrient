<?php

namespace PhpOrient;

class ClientTest extends TestCase {

    public function testOne() {
        $client = $this->createClient();
        $transport = $client->getTransport();
        $client->execute('connect');
        $this->assertNotEquals( -1, $client->getTransport()->getSessionId() );
        $this->assertNotEquals( -1, $client->getTransport()->getProtocol() );

    }

}
